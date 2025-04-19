<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\TwoFactorAuthMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Providers;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\SessionServiceProvider;
use Illuminate\Support\Facades\Request;
use Laravel\Sanctum\SanctumServiceProvider;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    // 1) Load your routing files as before
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    // 3) Register global & route middleware
    ->withMiddleware(function (Middleware $middleware) {

        // Cookies & session:
        $middleware->append(EncryptCookies::class);
        $middleware->append(AddQueuedCookiesToResponse::class);
        $middleware->append(StartSession::class);
        $middleware->append(AuthenticateSession::class);

        // ← **This is the critical piece** – must come before your auth guard:
        $middleware->append(EnsureFrontendRequestsAreStateful::class);

        // Alias Sanctum’s stateful check for your API routes
        $middleware->alias([
            'auth.sanctum' => EnsureFrontendRequestsAreStateful::class,
            'admin'        => AdminMiddleware::class,
            'two_factor_auth'      => TwoFactorAuthMiddleware::class,
        ]);
    })

    // 4) (Optional) Exception handling config
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })

    ->create();
