<?php

namespace App\Observers;

use App\Jobs\SendKafkaMessageJob;
use App\Models\Transaction;
use App\Services\KafkaProducerService;
use Illuminate\Support\Facades\Log;

class TransactionObserver
{


    public function __construct()
    {
    }
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        SendKafkaMessageJob::dispatch($this->kafkaProducer, $transaction);
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