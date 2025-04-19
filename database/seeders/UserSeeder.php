<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\TwoFactorAuth;
use App\Models\User;
use App\Models\UserAccountNumber;
use App\Models\UserRole;
use App\Models\UserTransaction;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
//    public function __construct(){
//
//    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrator role with full access',
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Regular user role with limited access',
            ]
        ];

        $getRoles = Role::select('id', 'slug')->pluck('slug')->toArray();
        if (count($getRoles) === 0 || array_diff(['admin', 'user'], $getRoles)) {
            foreach ($roles as $role) {
                Role::firstOrCreate([
                    'name' => $role['name'],
                    'slug' => $role['slug'],
                ]);
            }
        }

        $adminRole = Role::where('slug', 'admin')->first();
        $userRole = Role::where('slug', 'user')->first();

        $getAdminUser = User::with('roles')->whereHas('roles', function ($query) {
            $query->where('slug', 'admin');
        })->first();

        // Create an admin user if it doesn't exist
        if(!$getAdminUser){
            User::factory(1)->create()->each(function ($user) use ($adminRole) {
                UserRole::create([
                    'user_id' => $user->id,
                    'role_id' => $adminRole->id,
                ]);
                UserAccountNumber::factory(1)->create([
                    'user_id' => $user->id,
                ]);
            });
        }

        // Create 10 users with random data
        User::factory(10)->create()->each(function ($user) use ($userRole) {

            UserAccountNumber::factory(1)->create([
                'user_id' => $user->id,
            ]);

            UserTransaction::factory(5)->create([
                'user_id' => $user->id,
            ]);

            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $userRole->id,
            ]);

            TwoFactorAuth::factory()->create([
                'user_id' => $user->id
            ]);
        });

    }
}
