<?php

namespace App\Exports;

use App\Models\Transaction;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsExport implements FromCollection, WithHeadings,WithMapping
{
    protected $user;

    public function __construct(User $user){
        $this->user = $user;
    }
    // /**
    // * @return \Illuminate\Support\Collection
    // */
    public function collection()
    {
        return Transaction::where('user_id', $this->user->id)->get();
    }

    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->entry,
            $transaction->amount,
            $transaction->balance,
            $transaction->created_at->format('Y-m-d H:i:s'),
            $transaction->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Entry',
            'Amount',
            'Balance',
            'Created At',
            'Updated At',
        ];
    }
}