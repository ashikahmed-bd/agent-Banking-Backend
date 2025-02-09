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
            'type' => $this->type,
            'amount' => $this->amount,
            'commission' => $this->commission,
            $this->mergeWhen($this->whenLoaded('createdBy'), [
                'created_by' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ],
            ]),

            $this->mergeWhen($this->whenLoaded('account'), [
                'account' => [
                    'id' => $this->account->id,
                    'name' => $this->account->name,
                    'number' => $this->account->number,
                    'logo_url' => $this->account->logo_url,
                ],
            ]),
            'created_by' => $this->whenLoaded('created_by'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
