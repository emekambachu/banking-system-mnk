<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name ?? null,
            'last_name' => $this->last_name ?? null,
            'email' => $this->email ?? null,

            'account_number' => $this->account_number?->account_number ?? null,
            'account_type' => $this->account_number?->account_type ?? null,
            'currency' => $this->account_number?->currency ?? null,
            'amount' => $this->account_number?->amount ?? 0.00,

            'roles' => $this->roles?->pluck('slug') ?? [],

            'created_at' => $this->created_at ?? null,
            'updated_at' => $this->updated_at ?? null,
        ];
    }
}
