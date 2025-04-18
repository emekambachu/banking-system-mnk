<?php

namespace Database\Factories;

use App\Models\TwoFactorAuth;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TwoFactorAuth>
 */
class TwoFactorAuthFactory extends Factory
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
            'secret' => generateUniqueRandomString(TwoFactorAuth::class, 'secret', 6),
            'expires_at' => $this->faker->dateTimeBetween('now', '+1 hour'),
            'enabled' => $this->faker->randomElement([true, false]),
        ];
    }
}
