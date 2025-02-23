<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);
        return [
            'id' => $this->id,
            'type' => $this->type,
            'amount' => $this->amount,
            'remark' => $this->remark,
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            $this->mergeWhen($this->whenLoaded('createdBy'), [
                'created_by' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ],
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
