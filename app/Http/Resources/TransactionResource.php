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
        return [
            'id' => $this->id,
            $this->mergeWhen($this->whenLoaded('account'), [
                'account' => [
                    'id' => $this->account->id,
                    'name' => $this->account->name,
                    'number' => $this->account->number,
                    'logo_url' => $this->account->logo_url,
                ],
            ]),

            'type' => $this->type,
            'amount' => $this->amount,
            'balance_after_transaction' => $this->balance_after_transaction,
            'reference' => $this->reference,
            'date' => $this->date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
