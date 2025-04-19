<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\AccountNumberRepository;
use App\Models\User;
use App\Models\UserAccountNumber;

class AccountNumberRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_by_user_id(): void
    {
        $user = User::factory()->create();
        $record = UserAccountNumber::create([
            'user_id'        => $user->id,
            'account_number' => 'ACC123456',
        ]);

        $repo = new AccountNumberRepository();
        $found = $repo->findByUserId($user->id);

        $this->assertNotNull($found);
        $this->assertEquals('ACC123456', $found->account_number);
        $this->assertEquals($user->id, $found->user->id);
    }

    public function test_find_by_account_number(): void
    {
        $user = User::factory()->create();
        $record = UserAccountNumber::create([
            'user_id'        => $user->id,
            'account_number' => 'ACC999888',
        ]);

        $repo = new AccountNumberRepository();
        $found = $repo->findByAccountNumber('ACC999888');

        $this->assertNotNull($found);
        $this->assertEquals('ACC999888', $found->account_number);
        $this->assertEquals($user->id, $found->user->id);
    }
}
