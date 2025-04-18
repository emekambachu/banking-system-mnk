<?php

namespace App\Http\Middleware;

use App\Services\TwoFactorAuthService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userWithTwoFactorAuth = Auth::user()?->load('twoFactorAuth');
        if($userWithTwoFactorAuth?->twoFactorAuth?->enabled){
            if($userWithTwoFactorAuth?->twoFactorAuth?->verified_at && $userWithTwoFactorAuth?->twoFactorAuth?->verified_at < $userWithTwoFactorAuth?->twoFactorAuth?->expires_at){
                return $next($request);
            }
            return redirect('/login');
        }
        return $next($request);
    }
}
