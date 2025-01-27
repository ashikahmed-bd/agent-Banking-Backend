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
            'number' => $this->number,
            'logo_url' => $this->logo_url,
            'banner_url' => $this->banner_url,
            'initial_balance' => $this->initial_balance,
            'current_balance' => $this->current_balance,
            'active' => $this->active,
        ];
    }
}
