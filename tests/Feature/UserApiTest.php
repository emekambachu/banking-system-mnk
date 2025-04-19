<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserAccountNumber;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Disable all middleware for simplicity
        $this->withoutMiddleware();

        // Create two regular users
        $this->userA = User::factory()->create([
            'first_name' => 'Alice',
            'last_name'  => 'Anderson',
            'email'      => 'alice@example.com',
            'status'     => 1,
        ]);
        $this->userB = User::factory()->create([
            'first_name' => 'Bob',
            'last_name'  => 'Baker',
            'email'      => 'bob@example.com',
            'status'     => 0,
        ]);

        // Create their account numbers
        UserAccountNumber::create([
            'user_id'        => $this->userA->id,
            'account_number' => 'ACC111',
        ]);
        UserAccountNumber::create([
            'user_id'        => $this->userB->id,
            'account_number' => 'ACC222',
        ]);
    }

    public function test_get_users_returns_paginated_list_with_accounts(): void
    {
        $response = $this->getJson('/users');

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // Check that users.data contains both users
        $data = $response->json('users.data');
        $emails = array_column($data, 'email');
        $this->assertContains('alice@example.com', $emails);
        $this->assertContains('bob@example.com', $emails);

        // Check that each user has an 'account' sub‑object with account_number
        foreach ($data as $user) {
            $this->assertArrayHasKey('account', $user);
            $this->assertArrayHasKey('account_number', $user['account']);
        }
    }

    public function test_search_users_by_email(): void
    {
        // Search for Bob only
        $response = $this->postJson('/users/search', [
            'search_value' => 'bob@example.com',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total'   => 1,
            ]);

        $users = $response->json('users.data') ?? $response->json('users');
        // The search endpoint returns 'users' as full resource structure
        $emails = array_map(fn($u) => $u['email'], $users);
        $this->assertEquals(['bob@example.com'], $emails);
    }

    public function test_update_user_status_toggles(): void
    {
        // Initially Bob has status = 0
        $this->assertEquals(0, $this->userB->status);

        $response = $this->putJson("/users/{$this->userB->id}/update-status");

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // Fresh from DB, status should now be 1
        $this->assertEquals(1, $this->userB->fresh()->status);

        // Call again to toggle back
        $response2 = $this->putJson("/users/{$this->userB->id}/update-status");
        $response2->assertStatus(200)->assertJson(['success'=>true]);
        $this->assertEquals(0, $this->userB->fresh()->status);
    }
}
