<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_is_blocked(): void
    {
        // Create a user with no roles
        $user = User::factory()->create();
        Auth::login($user);

        $middleware = new AdminMiddleware();
        $request = Request::create('/admin-area', 'GET');
        $next    = fn($req) => response('OK', 200);

        /** @var JsonResponse $response */
        $response = $middleware->handle($request, $next);

        $this->assertEquals(403, $response->status());
        $body = $response->getData(true);
        $this->assertFalse($body['success']);
        $this->assertArrayHasKey('unauthorized', $body['errors']);
    }
}
