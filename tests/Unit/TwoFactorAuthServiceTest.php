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
}
