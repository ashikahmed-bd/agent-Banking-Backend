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
            $this->mergeWhen($this->whenLoaded('sender'), [
                'sender' => [
                    'id' => $this->sender->id,
                    'name' => $this->sender->name,
                ],
            ]),

            $this->mergeWhen($this->whenLoaded('receiver'), [
                'receiver' => [
                    'id' => optional($this->receiver)->id,
                    'name' => optional($this->receiver)->name,
                ],
            ]),
            'type' => $this->type,
            'amount' => $this->amount,
            'commission' => $this->commission,
            'reference' => $this->reference,
            'status' => $this->status,
            'remark' => $this->remark,
            $this->mergeWhen($this->whenLoaded('creator'), [
                'creator' => [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                ],
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
