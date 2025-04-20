<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\FundsTransferService;
use App\Repositories\FundsTransferRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\AccountNumberRepository;
use App\Repositories\CurrencyConversionRepository;
use App\Models\User;

class FundsTransferServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_send_funds_sender_account_not_found(): void
    {
        $sender = User::factory()->create();

        $acctRepo = Mockery::mock(AccountNumberRepository::class);
        $acctRepo->shouldReceive('findByUserId')
            ->with($sender->id)
            ->andReturn(null); // Mock the method to return null

        $service = new FundsTransferService(
            Mockery::mock(FundsTransferRepository::class),
            Mockery::mock(CurrencyConversionRepository::class),
            $acctRepo,
            Mockery::mock(TransactionRepository::class)
        );

        $result = $service->sendFundsToUser([
            'amount'                   => 50,
            'currency'                 => 'GBP',
        ], $sender);

        $this->assertFalse($result['success']);
        $this->assertEquals(422, $result['status']);
        $this->assertEquals(['Sender account not found'], $result['errors']['account']);
    }

    public function test_send_funds_insufficient_balance(): void
    {
        $sender = User::factory()->create();
        $acct = (object)['currency' => 'USD', 'amount' => 30];
        $acct->user_id = $sender->id;

        $acctRepo = Mockery::mock(AccountNumberRepository::class);
        $acctRepo->shouldReceive('findByUserId')
            ->once()
            ->andReturn($acct);

        $service = new FundsTransferService(
            Mockery::mock(FundsTransferRepository::class),
            Mockery::mock(CurrencyConversionRepository::class),
            $acctRepo,
            Mockery::mock(TransactionRepository::class)
        );

        $result = $service->sendFundsToUser([
            'amount'                   => 100,
            'currency'                 => 'USD',
        ], $sender);

        $this->assertFalse($result['success']);
        $this->assertEquals(422, $result['status']);
        $this->assertContains('Insufficient balance', $result['errors']['account']);
    }
}
