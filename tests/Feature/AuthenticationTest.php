<?php

namespace Tests\Feature;

use App\Models\Role;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends TestCase
{
    public function SetUp(): void
    {
        parent::setUp();
        $this->roles = [
            ['name' => 'admin', 'slug' => 'admin'],
            ['name' => 'user', 'slug' => 'user'],
        ];
    }

    use RefreshDatabase;

    /** @covers \App\Http\Controllers\AuthController::register */
    public function test_user_can_register(): void
    {
        foreach ($this->roles as $role) {
            Role::create($role);
        }

        $payload = [
            'first_name' => 'Test',
            'last_name'  => 'User',
            'email'      => 'test@example.com',
            'phone'      => '1234567890',
            'date_of_birth'    => '10/05/1990',
            'password'   => 'secret123',
            'password_confirmation'   => 'secret123',

        ];

        $response = $this->postJson('/api/register', $payload);

        $response
            ->assertStatus(201)
            ->assertJson(['success'=>true,'status'=>201])
            ->assertJsonStructure(
                [
                    'user',
                    'message',
                    'user',
                ]
            );

        $this->assertDatabaseHas('users', ['email'=>'test@example.com']);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('secret123'),
            'status'   => 1,
        ]);

        $response = $this->postJson('/api/login', [
            'email'=>'test@example.com',
            'password'=>'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success'=>true,'status'=>200]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'email'=>'wrong@example.com',
            'password'=>'badpass',
        ]);

        $response->assertStatus(422)
            ->assertJson(['success'=>false])
            ->assertJsonStructure(['errors']);
    }

    /** @covers \App\Http\Controllers\AuthController::logout */
    public function test_user_can_logout_via_get_route(): void
    {
        // Create & authenticate via session guard
        $user = User::factory()->create([
            'password' => Hash::make('secret123'),
            'status'   => 1,
        ]);

        // Simulate login (session guard)
        $this->actingAs($user);

        // Call the GET /logout route (defined in web.php)
        $response = $this->getJson('/logout');

        $response->assertStatus(200)
            ->assertJson(['success'=>true,'message'=>'Logged out successfully']);
    }
}
