<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Builder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\TransactionRepository;
use App\Models\User;
use App\Models\UserTransaction;

class TransactionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_transactions_builder(): void
    {
        $user = User::factory()->create();
        UserTransaction::factory()->count(3)->create(['user_id' => $user->id]);

        $repo = new TransactionRepository();
        $builder = $repo->getTransactions(['*'], []); // returns Builder

        $this->assertInstanceOf(Builder::class, $builder);

        // Should be able to chain and count
        $count = $builder->where('user_id', $user->id)->count();
        $this->assertEquals(3, $count);
    }
}
