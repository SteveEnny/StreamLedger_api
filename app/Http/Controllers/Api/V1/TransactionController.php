<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Transaction\TransactionAction;
use App\Actions\Transaction\TransactionExportAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CreateTransaction;
use App\Http\Resources\TransactionResource;
use App\Jobs\ExportTransactionsJob;
use App\Models\Transaction;
use App\Traits\ApiResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;


class TransactionController extends Controller
{
    use ApiResponses;
    public function __construct(
        private TransactionAction $transactionAction,
        private TransactionExportAction $exportAction
        ) {

    }

    public function index(){
        $transactions = Transaction::where('user_id', request()->user()->id)->paginate(20);
        return TransactionResource::collection($transactions);
    }


    public function store(CreateTransaction $request){
        try{
            $data = $request->validated();
            return $this->transactionAction->processTransaction($data);
        } catch (Exception $exception) {
            return $exception;
        }
        catch(Throwable $exception){
            Log::error('Transaction Error: '.$exception->getMessage(), [
                'trace' => $exception->getTraceAsString()
            ]);
            return $this->error('Application Error', 500);
        }
    }



    public function export() {
        return $this->exportAction->exportUserTransactions();
    }
}