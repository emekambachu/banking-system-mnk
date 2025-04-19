<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\Services\TwoFactorAuthService;
use App\Repositories\UserRepository;
use App\Repositories\TwoFactorAuthRepository;
use App\Models\TwoFactorAuth;

class TwoFactorAuthServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_process_two_factor_auth_sends_email(): void
    {
        $email = 'eve@example.com';
        $userId = 99;
        $secret = 'XYZ999';

        // Mock UserRepository
        $userRepo = Mockery::mock(UserRepository::class);
        $userRepo->shouldReceive('getUserByEmail')
            ->once()
            ->with($email, [], ['id','email'])
            ->andReturn((object)['id'=>$userId,'email'=>$email]);

        // Mock TwoFactorAuthRepository
        $twoFaRepo = Mockery::mock(TwoFactorAuthRepository::class);
        $twoFaModel = new TwoFactorAuth(['secret'=>$secret]);
        $twoFaRepo->shouldReceive('store')
            ->once()
            ->with($userId)
            ->andReturn($twoFaModel);
        $twoFaRepo->shouldReceive('sendTwoFactorCode')
            ->once()
            ->with($email, $secret);

        $service = new TwoFactorAuthService($userRepo, $twoFaRepo);
        $response = $service->processTwoFactorAuth($email);

        $this->assertTrue($response['success']);
        $this->assertEquals(200, $response['status']);
        $this->assertStringContainsString('code sent to your email', $response['message']);
    }

    public function test_process_two_factor_auth_handles_exception(): void
    {
        $email = 'fail@example.com';

        $userRepo = Mockery::mock(UserRepository::class);
        $userRepo->shouldReceive('getUserByEmail')
            ->andThrow(new \Exception('DB failure'));

        $twoFaRepo = Mockery::mock(TwoFactorAuthRepository::class);

        $service = new TwoFactorAuthService($userRepo, $twoFaRepo);
        $response = $service->processTwoFactorAuth($email);

        $this->assertFalse($response['success']);
        $this->assertEquals(500, $response['status']);
        $this->assertArrayHasKey('errors', $response);
    }

    public function test_verify_2fa_success_and_failure(): void
    {
        $secret = 'GOODCODE';
        $userRepo = Mockery::mock(UserRepository::class);
        $twoFaRepo = Mockery::mock(TwoFactorAuthRepository::class);

        // success path
        $twoFaRepo->shouldReceive('verify')->once()->with($secret)->andReturnTrue();
        $service = new TwoFactorAuthService($userRepo, $twoFaRepo);
        $response = $service->verify2FA($secret);
        $this->assertTrue($response['success']);
        $this->assertEquals(200, $response['status']);

        // failure path
        $twoFaRepo->shouldReceive('verify')->once()->with('BAD')->andReturnFalse();
        $response = $service->verify2FA('BAD');
        $this->assertFalse($response['success']);
        $this->assertEquals(401, $response['status']);
        $this->assertArrayHasKey('errors', $response);
    }
}
