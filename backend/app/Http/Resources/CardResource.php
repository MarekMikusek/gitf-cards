<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'card_number' => $this->card_number,
            'pin' => $this->pin,
            'activation_date' => $this->activation_date->format('Y-m-d H:i:s'),
            'expiration_date' => $this->expiration_date->format('Y-m-d'),
            'balance' => (float) $this->balance,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
