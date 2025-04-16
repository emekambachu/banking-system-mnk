<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAccountNumber;
use App\Models\UserRole;
use App\Models\UserTransaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 users with random data
        User::factory(10)->create()->each(function ($user) {

            UserAccountNumber::factory(1)->create([
                'user_id' => $user->id,
            ]);

            UserTransaction::factory(5)->create([
                'user_id' => $user->id,
            ]);

            UserRole::factory(1)->create([
                'user_id' => $user->id,
                'role_id' => 2, // Assuming 2 is the ID for the user role
            ]);
        });
    }
}
