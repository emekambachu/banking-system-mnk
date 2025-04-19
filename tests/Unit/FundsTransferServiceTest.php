<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\Services\FundsTransferService;
use App\Repositories\FundsTransferRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\AccountNumberRepository;
use App\Repositories\CurrencyConversionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\UserAccountNumber;
use App\Models\User;

class FundsTransferServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_send_funds_sender_account_missing(): void
    {
        $sender = new User(); $sender->id = 1;

        $acctRepo = Mockery::mock(AccountNumberRepository::class);
        $acctRepo->shouldReceive('findByUserId')->with(1)->andReturnNull();

        $service = new FundsTransferService(
            Mockery::mock(FundsTransferRepository::class),
            Mockery::mock(CurrencyConversionRepository::class),
            $acctRepo,
            Mockery::mock(TransactionRepository::class)
        );

        $res = $service->sendFundsToUser(['amount'=>100], $sender);
        $this->assertFalse($res['success']);
        $this->assertEquals(422, $res['status']);
        $this->assertEquals(['Sender account not found'], $res['errors']['account']);
    }

    public function test_send_funds_insufficient_balance(): void
    {
        $sender = new User(); $sender->id = 2;
        $senderAcct = (object)['amount'=>50];

        $acctRepo = Mockery::mock(AccountNumberRepository::class);
        $acctRepo->shouldReceive('findByUserId')->with(2)->andReturn($senderAcct);

        $service = new FundsTransferService(
            Mockery::mock(FundsTransferRepository::class),
            Mockery::mock(CurrencyConversionRepository::class),
            $acctRepo,
            Mockery::mock(TransactionRepository::class)
        );

        $res = $service->sendFundsToUser(['amount'=>100], $sender);
        $this->assertFalse($res['success']);
        $this->assertEquals(422, $res['status']);
        $this->assertStringContainsString('Insufficient funds', $res['errors']['funds'][0]);
    }

    public function test_send_funds_currency_conversion_failure(): void
    {
        $sender = new User(); $sender->id = 3;
        $senderAcct = (object)['amount'=>1000];
        $receiverAcct = (object)['user_id'=>5,'amount'=>0];

        $acctRepo = Mockery::mock(AccountNumberRepository::class);
        $acctRepo->shouldReceive('findByUserId')->with(3)->andReturn($senderAcct);
        $acctRepo->shouldReceive('findByAccountNumber')->andReturn($receiverAcct);

        $convRepo = Mockery::mock(CurrencyConversionRepository::class);
        $convRepo->shouldReceive('convert')
            ->once()
            ->andReturn(['success'=>false,'error'=>'API down']);

        $service = new FundsTransferService(
            Mockery::mock(FundsTransferRepository::class),
            $convRepo,
            $acctRepo,
            Mockery::mock(TransactionRepository::class)
        );

        $res = $service->sendFundsToUser([
            'receiver_account_number'=>'ACC999',
            'amount'=>100,
            'currency'=>'EUR'
        ], $sender);

        $this->assertFalse($res['success']);
        $this->assertEquals(422, $res['status']);
        $this->assertStringContainsString('Currency conversion failed', $res['errors']['currency'][0]);
    }

    public function test_send_funds_success(): void
    {
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();

        $sender = new User(); $sender->id = 4;
        $senderAcct = (object)['user_id'=>4,'amount'=>1000];
        $receiverAcct = (object)['user_id'=>6,'amount'=>0];

        $acctRepo = Mockery::mock(AccountNumberRepository::class);
        $acctRepo->shouldReceive('findByUserId')->with(4)->andReturn($senderAcct);
        $acctRepo->shouldReceive('findByAccountNumber')
            ->with('ACC123')->andReturn($receiverAcct);

        $convRepo = Mockery::mock(CurrencyConversionRepository::class);
        $convRepo->shouldReceive('convert')
            ->with('USD','EUR',100)
            ->andReturn(['success'=>true,'amount'=>90]);

        $fundRepo = Mockery::mock(FundsTransferRepository::class);
        $fundRepo->shouldReceive('store')
            ->once()
            ->with(Mockery::subset([
                'sender_id'=>4,'receiver_id'=>6,'amount'=>90,'currency'=>'EUR'
            ]))
            ->andReturn((object)['id'=>10]);

        $txRepo = Mockery::mock(TransactionRepository::class);
        $txRepo->shouldReceive('storeTransaction')->once();

        $service = new FundsTransferService(
            $fundRepo,
            $convRepo,
            $acctRepo,
            $txRepo
        );

        $res = $service->sendFundsToUser([
            'receiver_account_number'=>'ACC123',
            'amount'=>100,
            'currency'=>'EUR',
            'type'=>'transfer',
            'description'=>'Test transfer'
        ], $sender);

        $this->assertTrue($res['success']);
        $this->assertEquals(200, $res['status']);
        $this->assertStringContainsString('Funds transferred successfully', $res['message']);
    }
}
