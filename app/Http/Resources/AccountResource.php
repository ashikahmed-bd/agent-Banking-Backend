<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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
            'name' => $this->name,
            'number' => $this->default ? 'Default' : $this->number,
            'opening_balance' => $this->opening_balance,
            'closing_balance' => $this->closing_balance,
            'current_balance' => $this->current_balance,
            'default' => (bool) $this->default,
            'agent' => AgentResource::make($this->whenLoaded('agent')),
            'creator' => UserResource::make($this->whenLoaded('creator')),
        ];
    }
}
