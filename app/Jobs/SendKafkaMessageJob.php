<?php

namespace App\Jobs;

use App\Services\KafkaProducerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendKafkaMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $transaction;
    /**
     * Create a new job instance.
     */
    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     */
    public function handle(KafkaProducerService $kafkaProducer): void
    {
         $messagePayload = [
                'user_id' => $this->transaction->user_id,
                'entry' => $this->transaction->entry,
                'amount' => $this->transaction->amount,
                'balance' => $this->transaction->balance,
                'timestamp' => $this->transaction->created_at->toISOString(),
            ];

            $success = $kafkaProducer->sendJson('transactions', $messagePayload);

            if ($success) {
                Log::info("Transaction streamed to Kafka successfully", [
                    'transaction_id' => $this->transaction->id,
                    'user_id' => $this->transaction->user_id
                ]);
            } else {
                Log::error("Failed to stream transaction to Kafka", [
                    'transaction_id' => $this->transaction->id,
                    'user_id' => $this->transaction->user_id
                ]);
            }
    }
}