<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Middleware\TwoFactorAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\TwoFactorAuth;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TwoFactorAuthMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_passes_when_user_has_no_two_factor_record(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $middleware = new TwoFactorAuthMiddleware();
        $request = Request::create('/dashboard', 'GET');
        $next    = fn($req) => 'NEXT';

        $this->assertSame('NEXT', $middleware->handle($request, $next));
    }

    public function test_passes_when_two_factor_disabled(): void
    {
        $user = User::factory()->create();
        TwoFactorAuth::create([
            'user_id'              => $user->id,
            'secret'               => 'ABC',
            'secret_verified_at'   => Carbon::now()->subMinutes(5),
            'secret_expires_at'    => Carbon::now()->addMinutes(5),
            'enabled'              => false,
        ]);
        Auth::login($user);

        $middleware = new TwoFactorAuthMiddleware();
        $request = Request::create('/dashboard', 'GET');
        $next    = fn($req) => 'NEXT';

        $this->assertSame('NEXT', $middleware->handle($request, $next));
    }

    public function test_passes_when_two_factor_enabled_and_not_expired(): void
    {
        $user = User::factory()->create();
        TwoFactorAuth::create([
            'user_id'              => $user->id,
            'secret'               => 'ABC',
            'secret_verified_at'   => Carbon::now()->subSeconds(10),
            'secret_expires_at'    => Carbon::now()->addMinutes(1),
            'enabled'              => true,
        ]);
        Auth::login($user);

        $middleware = new TwoFactorAuthMiddleware();
        $request = Request::create('/dashboard', 'GET');
        $next    = fn($req) => 'NEXT';

        $this->assertSame('NEXT', $middleware->handle($request, $next));
    }

    public function test_redirects_when_two_factor_enabled_but_expired(): void
    {
        $user = User::factory()->create();
        TwoFactorAuth::create([
            'user_id'              => $user->id,
            'secret'               => 'XYZ',
            'secret_verified_at'   => Carbon::now()->subHours(2),
            'secret_expires_at'    => Carbon::now()->subHour(),
            'enabled'              => true,
        ]);
        Auth::login($user);

        $middleware = new TwoFactorAuthMiddleware();
        $request = Request::create('/dashboard', 'GET');
        $next    = fn($req) => 'NEXT';

        $response = $middleware->handle($request, $next);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('/login', $response->getTargetUrl());
    }
}
