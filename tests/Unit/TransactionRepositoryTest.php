<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\TransactionRepository;
use App\Models\User;
use App\Models\UserTransaction;

class TransactionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_transaction(): void
    {
        $user = User::factory()->create();
        $payload = [
            'user_id'          => $user->id,
            'fund_transfer_id' => 123,
            'amount'           => 500,
            'currency'         => 'USD',
            'type'             => 'debit',
            'description'      => 'Test transaction',
        ];

        $repo = new TransactionRepository();
        $tx = $repo->storeTransaction($payload);

        $this->assertInstanceOf(UserTransaction::class, $tx);
        $this->assertEquals(500, $tx->amount);
        $this->assertEquals('debit', $tx->type);
        $this->assertDatabaseHas('user_transactions', [
            'id'          => $tx->id,
            'user_id'     => $user->id,
            'description' => 'Test transaction',
        ]);
    }

    public function test_get_transactions_builder(): void
    {
        $user = User::factory()->create();
        UserTransaction::factory()->count(3)->create(['user_id' => $user->id]);

        $repo = new TransactionRepository();
        $builder = $repo->getTransactions(['*'], []); // returns Builder

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);

        // Should be able to chain and count
        $count = $builder->where('user_id', $user->id)->count();
        $this->assertEquals(3, $count);
    }
}
