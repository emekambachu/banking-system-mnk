<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegistrationProcessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @covers \App\Http\Controllers\AuthController::register
     * @covers \App\Services\AuthService::register
     */
    public function test_registration_creates_user_and_returns_json(): void
    {
        $payload = [
            'first_name' => 'Clara',
            'last_name'  => 'Oswald',
            'email'      => 'clara@example.com',
            'password'   => 'mypassword',
        ];

        $response = $this->postJson('/register', $payload);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'status',
                'user'    => ['data' => ['id','email','first_name','last_name']],
                'message',
            ])
            ->assertJson([
                'success' => true,
                'status'  => 200,
                'message' => 'User Created Successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'clara@example.com',
        ]);

        // The very first registered user should have status = 1 (admin)
        $this->assertDatabaseHas('users', [
            'email'  => 'clara@example.com',
            'status' => 1,
        ]);
    }

    public function test_registration_validation_errors(): void
    {
        // Missing required fields
        $response = $this->postJson('/register', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name','last_name','email','password'
            ]);
    }
}
