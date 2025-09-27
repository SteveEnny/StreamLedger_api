<?php
namespace App\Actions\Transaction;

use App\Jobs\ExportTransactionsJob;
use App\Traits\ApiResponses;


class TransactionExportAction {
    use ApiResponses;

    public function exportUserTransactions(){
        $user = request()->user();
        ExportTransactionsJob::dispatch($user);

        return $this->successWithoutData(('Transaction export is being processed. You will be notified upon completion.'));
    }

}