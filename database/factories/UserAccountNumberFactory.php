<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class UserAccountNumberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function definition(): array
    {
        return [
            'account_number' => generateUniqueRandomString(User::class, 'account_number', 10),
            'account_type' => 'savings',
            'currency' => 'USD',
            'amount' => 10000.00,
        ];
    }
}
