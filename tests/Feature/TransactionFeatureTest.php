<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class TransactionFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // disable middleware for simplicity
        $this->withoutMiddleware();

        $this->user = User::factory()->create([
            'password' => Hash::make('secret'),
            'status'   => 1,
        ]);

        // Create some transactions for this user
        UserTransaction::factory()->count(5)->create(['user_id' => $this->user->id]);
    }

    public function test_my_transactions_endpoint(): void
    {
        $response = $this
            ->actingAs($this->user, 'sanctum')
            ->getJson('/users/my-transactions');

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'transactions' => ['data', 'current_page', 'per_page', 'total'],
                'total',
            ]);

        $this->assertCount(5, $response->json('transactions.data'));
    }
}
