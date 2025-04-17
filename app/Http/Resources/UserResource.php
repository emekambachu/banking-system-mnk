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
            'mobile' => $this->mobile ?? null,
            'email' => $this->email ?? null,
            'address' => $this->address ?? null,

            'account_number' => $this->account->account_number ?? null,
            'account_type' => $this->account?->account_type ?? null,
            'currency' => $this->account?->currency ?? null,
            'amount' => $this->account?->amount ?? 0.00,

            'roles' => $this->roles?->pluck('slug') ?? [],

            'status' => $this->status ?? null,
            'created_at' => $this->created_at ?? null,
            'updated_at' => $this->updated_at ?? null,
        ];
    }
}
