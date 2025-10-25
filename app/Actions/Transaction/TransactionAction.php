<?php
namespace App\Actions\Transaction;

use App\Traits\ApiResponses;
use Illuminate\Support\Facades\DB;

class TransactionAction {
    use ApiResponses;

    public function processTransaction(array $data){
        $user = request()->user();
        $amount = $data['amount'];
        $entry = $data['entry'];

        return match ($entry) {
            'debit' => $this->processDebit($amount, $user),
            'credit' => $this->processCredit($amount, $user),
        };
    }



    private function processDebit($amount, $user){
        return DB::transaction(function () use ($amount, $user) {
            $wallet = $user->wallet()->lockForUpdate()->first();
            if($amount > $wallet->balance){
                return $this->error('Insufficient balance', 422);
            }

            $newBalance = $wallet->balance - $amount;
            $wallet->update(
                ['balance' => $newBalance]
            );
            $transaction = $user->transactions()->create([
                'entry' => 'debit',
                'amount' => $amount,
                'balance' => $newBalance
            ]);

            return $this->success('Account debited successfully', $transaction, 201);
        });

    }

    private function processCredit($amount, $user){
        return DB::transaction(function () use ($amount, $user) {
            $wallet = $user->wallet()->lockForUpdate()->first();
            $newBalance = $wallet->balance + $amount;

            $wallet->update(
                ['balance' => $newBalance]
            );

            $transaction = $user->transactions()->create([
                'entry' => 'credit',
                'amount' => $amount,
                'balance' => $newBalance
            ]);

            return $this->success('Account credited successfully', $transaction, 201);
        });
    }
}