<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\TwoFactorAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Support\Facades\Hash;

class LoginAndTwoFactorTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_endpoint_incorrect_credentials(): void
    {
        $response = $this->postJson('/login', [
            'email'    => 'nouser@example.com',
            'password' => 'wrong',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'errors'  => ['access' => ['Incorrect credentials']],
            ]);
    }

    public function test_login_unverified_user(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret'),
            'status'   => 0,  // not verified
        ]);

        $response = $this->postJson('/login', [
            'email'    => $user->email,
            'password' => 'secret',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'errors'  => ['access' => ['User not verified']],
            ]);
    }

    public function test_login_with_2fa_flow(): void
    {
        Mail::fake();

        // Create verified user
        $user = User::factory()->create([
            'password' => Hash::make('secret'),
            'status'   => 1,
        ]);

        // Enable 2FA by seeding a TwoFactorAuth record
        TwoFactorAuth::factory()->create([
            'user_id' => $user->id,
            'enabled' => true,
            'secret'  => 'FLOW123',
        ]);

        // Step 1: login triggers 2FA send
        $response = $this->postJson('/login', [
            'email'    => $user->email,
            'password' => 'secret',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success'        => true,
                'two_factor_auth'=> true,
                'status'         => 200,
            ]);

        Mail::assertSent(TwoFactorCodeMail::class, function($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        // Grab the new secret from table
        $record = TwoFactorAuth::where('user_id', $user->id)
            ->orderBy('created_at','desc')
            ->first();

        // Step 2: verify the code
        $verify = $this->postJson('/login/2fa-verify', [
            'secret' => $record->secret,
        ]);

        $verify
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status'  => 200,
            ]);
    }

    public function test_2fa_verify_invalid_code(): void
    {
        $user = User::factory()->create(['status'=>1]);

        // No record => code invalid
        $response = $this->postJson('/login/2fa-verify', [
            'secret' => 'INVALID',
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_login_success_without_2fa(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret'),
            'status'   => 1,
        ]);

        $response = $this->postJson('/login', [
            'email'    => $user->email,
            'password' => 'secret',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status'  => 200,
            ]);
    }
}
