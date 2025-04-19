<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserAccountNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class FundTransferFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();

        // Sender and Receiver
        $this->sender = User::factory()->create([
            'password' => Hash::make('secret'),
            'status'   => 1,
        ]);
        $this->receiver = User::factory()->create([
            'status' => 1,
        ]);

        // Account numbers with initial balances
        UserAccountNumber::create(['user_id'=>$this->sender->id,   'account_number'=>'ACC-S', 'amount'=>1000]);
        UserAccountNumber::create(['user_id'=>$this->receiver->id, 'account_number'=>'ACC-R', 'amount'=>100]);
    }

    public function test_get_beneficiary_by_account_number(): void
    {
        $res = $this
            ->actingAs($this->sender, 'sanctum')
            ->postJson('/users/send-funds/get-beneficiary', [
                'account_number' => 'ACC-R',
            ]);

        $res
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'user' => ['id','first_name','last_name','account_number'],
            ]);
    }

    public function test_send_funds_reduces_balances_and_records_transaction(): void
    {
        $res = $this
            ->actingAs($this->sender, 'sanctum')
            ->postJson('/users/send-funds', [
                'recipient_account_number' => 'ACC-R',
                'amount'                   => 200,
                'currency'                 => 'USD',
            ]);

        $res
            ->assertStatus(200)
            ->assertJson(['success'=>true])
            ->assertJsonStructure(['success','fund_transfer','transaction']);

        // Check balances in DB
        $this->assertDatabaseHas('user_account_numbers', [
            'user_id' => $this->sender->id,
            'amount'  => 800,   // 1000 - 200
        ]);

        $this->assertDatabaseHas('user_account_numbers', [
            'user_id' => $this->receiver->id,
            'amount'  => 300,   // 100  + 200
        ]);

        // Check a UserTransaction record was created for both sender & receiver
        $this->assertDatabaseHas('user_transactions', [
            'user_id'     => $this->sender->id,
            'amount'      => 200,
            'type'        => 'debit',
        ]);
        $this->assertDatabaseHas('user_transactions', [
            'user_id'     => $this->receiver->id,
            'amount'      => 200,
            'type'        => 'credit',
        ]);
    }
}
