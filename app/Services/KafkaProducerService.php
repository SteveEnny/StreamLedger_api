<?php

namespace App\Services;

use RdKafka\Producer;
use RdKafka\Conf;
use Exception;
use Illuminate\Support\Facades\Log;

class KafkaProducerService
{
    private $producer;
    private $brokers;

    public function __construct()
    {
        $this->brokers = config('kafka.brokers', 'kafka:29092');
        $this->initializeProducer();
    }

    /**
     * Initialize Kafka producer
     */
    private function initializeProducer(): void
    {
        $conf = new Conf();
        $conf->set('metadata.broker.list', $this->brokers);

        // Optional: Add more configuration
        $conf->set('compression.type', 'snappy');
        $conf->set('batch.num.messages', 1000);

        $this->producer = new Producer($conf);
    }

    /**
     * Send a simple string message to a topic
     */
    public function sendMessage(string $topic, string $message, ?string $key = null): bool
    {
        try {
            $topicProducer = $this->producer->newTopic($topic);

            $topicProducer->produce(RD_KAFKA_PARTITION_UA, 0, $message, $key);
            $this->producer->poll(0);

            $result = $this->producer->flush(5000); // Wait up to 5 seconds

            if ($result !== RD_KAFKA_RESP_ERR_NO_ERROR) {
                Log::error("Kafka flush failed with error code: {$result}");
                return false;
            }

            Log::info("Message sent to topic '{$topic}': {$message}");
            return true;

        } catch (Exception $e) {
            Log::error("Kafka produce error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send JSON data to a topic
     */
    public function sendJson(string $topic, array $data, ?string $key = null): bool
    {
        $jsonMessage = json_encode($data);

        if ($jsonMessage === false) {
            Log::error('Failed to encode data to JSON');
            return false;
        }

        return $this->sendMessage($topic, $jsonMessage, $key);
    }

    /**
     * Send user event (common use case)
     */
    public function sendUserEvent(int $userId, string $action, array $data = []): bool
    {
        $eventData = [
            'user_id' => $userId,
            'action' => $action,
            'data' => $data,
            'timestamp' => now()->toISOString(),
            'app' => config('app.name', 'Laravel')
        ];

        return $this->sendJson('user_events', $eventData, (string)$userId);
    }

    /**
     * Send system notification
     */
    public function sendNotification(string $type, string $message, array $metadata = []): bool
    {
        $notificationData = [
            'type' => $type,
            'message' => $message,
            'metadata' => $metadata,
            'timestamp' => now()->toISOString()
        ];

        return $this->sendJson('notifications', $notificationData);
    }

    /**
     * Send multiple messages in batch
     */
    public function sendBatch(string $topic, array $messages): array
    {
        $results = [];

        foreach ($messages as $index => $message) {
            if (is_array($message)) {
                $results[$index] = $this->sendJson($topic, $message);
            } else {
                $results[$index] = $this->sendMessage($topic, (string)$message);
            }
        }

        return $results;
    }

    /**
     * Get producer metadata (useful for debugging)
     */
    public function getMetadata(): array
    {
        try {
            $metadata = $this->producer->getMetadata(true, null, 5000);

            return [
                'brokers' => count($metadata->getBrokers()),
                'topics' => count($metadata->getTopics()),
                'broker_list' => $this->brokers
            ];
        } catch (Exception $e) {
            Log::error("Failed to get Kafka metadata: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Close producer connection
     */
    public function __destruct()
    {
        if ($this->producer) {
            $this->producer->flush(1000);
        }
    }
}