<?php

namespace App\Jobs;

use App\Exports\TransactionsExport;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ExportTransactionsJob implements ShouldQueue
{
    use Queueable,Dispatchable, SerializesModels;
    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $filePath = 'exports/transactions/' . $this->user->id . '-transactions-' . now()->timestamp . '.xlsx';

        Excel::store(new TransactionsExport($this->user), $filePath, 'public');

        Log::info($this->user->name . ' transactions exported to ' . $filePath);

        //TODO: create an export model to save the file path and user id
    }
}