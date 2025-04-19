<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\Services\AuthService;
use App\Repositories\UserRepository;
use App\Repositories\TwoFactorAuthRepository;
use App\Repositories\AccountNumberRepository;
use App\Repositories\RoleRepository;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthServiceLoginTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_login_incorrect_credentials(): void
    {
        // Arrange: Auth::attempt returns false
        Auth::shouldReceive('attempt')
            ->once()
            ->with(['email' => 'x@example.com', 'password' => 'wrong'])
            ->andReturnFalse();

        $request = new Request(['email'=>'x@example.com','password'=>'wrong']);

        // Mocks for unused repos
        $userRepo  = Mockery::mock(UserRepository::class);
        $twoFaRepo = Mockery::mock(TwoFactorAuthRepository::class);
        $acctRepo  = Mockery::mock(AccountNumberRepository::class);
        $roleRepo  = Mockery::mock(RoleRepository::class);

        $service = new AuthService($userRepo, $acctRepo, $roleRepo, $twoFaRepo);

        // Act
        $result = $service->login($request);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertEquals(422, $result['status']);
        $this->assertEquals(['Incorrect credentials'], $result['errors']['access']);
    }

    public function test_login_unverified_user(): void
    {
        // Arrange: valid credentials
        Auth::shouldReceive('attempt')->andReturnTrue();

        // Create a stub User with no twoFactorAuth
        $user = new User();
        $user->id = 5;
        $user->twoFactorAuth = null;

        Auth::shouldReceive('user')->andReturn($user);

        // userRepository->isVerified returns false
        $userRepo = Mockery::mock(UserRepository::class);
        $userRepo->shouldReceive('isVerified')->with(5)->andReturnFalse();

        $twoFaRepo = Mockery::mock(TwoFactorAuthRepository::class);
        $acctRepo  = Mockery::mock(AccountNumberRepository::class);
        $roleRepo  = Mockery::mock(RoleRepository::class);

        $service = new AuthService($userRepo, $acctRepo, $roleRepo, $twoFaRepo);

        $request = new Request(['email'=>'','password'=>'']);

        // Act
        $result = $service->login($request);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertEquals(422, $result['status']);
        $this->assertEquals(['User not verified'], $result['errors']['access']);
    }

    public function test_login_with_2fa_enabled_triggers_send_and_flag(): void
    {
        Auth::shouldReceive('attempt')->andReturnTrue();

        $user = new User();
        $user->id = 7;
        // Simulate loaded relation
        $twoFaModel = (object)['secret'=>'ABC123','enabled'=>true];
        $user->twoFactorAuth = $twoFaModel;

        Auth::shouldReceive('user')->andReturn($user);

        $userRepo = Mockery::mock(UserRepository::class);
        $userRepo->shouldReceive('isVerified')->with(7)->andReturnTrue();

        // twoFactorAuthRepository->store & send
        $twoFaRepo = Mockery::mock(TwoFactorAuthRepository::class);
        $twoFaRepo->expects('store')->with(7)->andReturn($twoFaModel);
        $twoFaRepo->expects('sendTwoFactorCode')->with($user->email, 'ABC123');

        $acctRepo = Mockery::mock(AccountNumberRepository::class);
        $roleRepo = Mockery::mock(RoleRepository::class);

        $service = new AuthService($userRepo, $acctRepo, $roleRepo, $twoFaRepo);

        $request = new Request(['email'=>'','password'=>'']);

        // Act
        $result = $service->login($request);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertTrue($result['two_factor_auth']);
        $this->assertEquals(200, $result['status']);
        $this->assertStringContainsString('Two Factor Authentication code sent', $result['message']);
    }
}
