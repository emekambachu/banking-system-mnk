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
            'hash' => $this->hash,
            'id' => $this->id,
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'currency' => $this->account?->currency ?? null,
            'type' => $this->type,
            'description' => $this->description ?? '',
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
