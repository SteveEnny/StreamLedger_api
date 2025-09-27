<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
        // return [
        //     'id' => $this->id,
        //     'entry' => $this->entry,
        //     'amount' => $this->amount,
        //     'balance' => $this->balance,
        //     'createdAt' => $this->created_at->toDateTimeString(),
        //     'updatedAt' => $this->updated_at->toDateTimeString(),
        // ];
    }
}