<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\Services\AuthService;
use App\Repositories\UserRepository;
use App\Repositories\AccountNumberRepository;
use App\Repositories\RoleRepository;
use App\Repositories\TwoFactorAuthRepository;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthServiceRegistrationTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_register_first_user_assigns_admin_and_returns_user_resource(): void
    {
        // Input data
        $input = [
            'first_name' => 'Alice',
            'last_name'  => 'Smith',
            'email'      => 'alice@example.com',
            'password'   => 'password123',
        ];

        // Prepare a User model stub
        $user = new User();
        $user->id = 1;
        $user->email = $input['email'];

        // Mock dependencies
        $userRepo    = Mockery::mock(UserRepository::class);
        $acctRepo    = Mockery::mock(AccountNumberRepository::class);
        $roleRepo    = Mockery::mock(RoleRepository::class);
        $twoFaRepo   = Mockery::mock(TwoFactorAuthRepository::class);

        // storeUser hashes password internally; we simply return our stub
        $userRepo->expects('storeUser')
            ->once()
            ->with(Mockery::subset([
                'first_name' => 'Alice',
                'last_name'  => 'Smith',
                'email'      => 'alice@example.com',
                // password will be hashed
            ]))
            ->andReturn($user);

        // createAccountNumberForUser should be called
        $acctRepo->expects('createAccountNumberForUser')
            ->once()
            ->with($user);

        // Simulate only one user in the system
        $userRepo->expects('getUsers')
            ->once()
            ->andReturn(new Collection([$user]));

        // Prepare a Role model stub with a users() relation stub
        $roleModel = Mockery::mock();
        $relation  = Mockery::mock();
        $relation->expects('attach')
            ->once()
            ->with(1);

        $roleModel->expects('users')
            ->once()
            ->andReturn($relation);

        // getRoleBySlug('admin') should be called
        $roleRepo->expects('getRoleBySlug')
            ->once()
            ->with('admin')
            ->andReturn($roleModel);

        // 2FA activation for every registration
        $twoFaRepo->expects('activate')
            ->once()
            ->with($user);

        // After DB operations the service fetches the user again
        $userRepo->expects('getUserById')
            ->once()
            ->with(1, ['roles','account'], ['id'])
            ->andReturn($user);

        // Wrap the call in a transaction expectation
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();

        // Instantiate service with our mocks
        $service = new AuthService(
            $userRepo,
            $acctRepo,
            $roleRepo,
            $twoFaRepo
        );

        $response = $service->register($input);

        // Assertions on returned array
        $this->assertTrue($response['success']);
        $this->assertEquals(200, $response['status']);
        $this->assertInstanceOf(UserResource::class, $response['user']);
        $this->assertEquals('User Created Successfully', $response['message']);
    }

    public function test_register_subsequent_user_skips_admin_assignment(): void
    {
        $input = [
            'first_name' => 'Bob',
            'last_name'  => 'Jones',
            'email'      => 'bob@example.com',
            'password'   => 'password123',
        ];

        $user = new User();
        $user->id = 2;
        $user->email = $input['email'];

        $userRepo  = Mockery::mock(UserRepository::class);
        $acctRepo  = Mockery::mock(AccountNumberRepository::class);
        $roleRepo  = Mockery::mock(RoleRepository::class);
        $twoFaRepo = Mockery::mock(TwoFactorAuthRepository::class);

        $userRepo->expects('storeUser')
            ->once()
            ->andReturn($user);
        $acctRepo->expects('createAccountNumberForUser')
            ->once()
            ->with($user);

        // Simulate >1 user already exists
        $userRepo->expects('getUsers')
            ->once()
            ->andReturn(new Collection([new User(), $user]));

        // RoleRepository should NOT be called
        $roleRepo->shouldNotReceive('getRoleBySlug');

        // 2FA activation still called
        $twoFaRepo->expects('activate')
            ->once()
            ->with($user);

        $userRepo->expects('getUserById')
            ->once()
            ->andReturn($user);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();

        $service = new AuthService(
            $userRepo,
            $acctRepo,
            $roleRepo,
            $twoFaRepo
        );

        $response = $service->register($input);

        $this->assertTrue($response['success']);
        $this->assertEquals(200, $response['status']);
        $this->assertInstanceOf(UserResource::class, $response['user']);
    }
}
