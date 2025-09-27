<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Services\KafkaProducerService;
use Illuminate\Support\Facades\Log;

class TransactionObserver
{
    private $kafkaProducer;

    public function __construct(KafkaProducerService $kafkaProducer)
    {
        $this->kafkaProducer = $kafkaProducer;
    }
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        try {
            $messagePayload = [
                'user_id' => $transaction->user_id,
                'entry' => $transaction->entry,
                'amount' => $transaction->amount,
                'balance' => $transaction->balance,
                'timestamp' => $transaction->created_at->toISOString(),
            ];

            $success = $this->kafkaProducer->sendJson('transactions', $messagePayload);

            if ($success) {
                Log::info("Transaction streamed to Kafka successfully", [
                    'transaction_id' => $transaction->id,
                    'user_id' => $transaction->user_id
                ]);
            } else {
                Log::error("Failed to stream transaction to Kafka", [
                    'transaction_id' => $transaction->id,
                    'user_id' => $transaction->user_id
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Exception while streaming transaction to Kafka: " . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->user_id,
                'exception' => $e
            ]);
        }
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }
}