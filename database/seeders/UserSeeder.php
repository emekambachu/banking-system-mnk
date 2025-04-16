<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserAccountNumber;
use App\Models\UserRole;
use App\Models\UserTransaction;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
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

        $adminRoleId = Role::where('slug', 'admin')->first();

        $getAdminUser = User::with('roles')->whereHas('roles', function ($query) {
            $query->where('slug', 'admin');
        })->first();

        if(!$getAdminUser){
            User::factory(1)->create()->each(function ($user) use ($adminRoleId) {
                UserRole::factory(1)->create([
                    'user_id' => $user->id,
                    'role_id' => $adminRoleId->id,
                ]);
            });
        }

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
