<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isAdmin = Auth::user()->load(['roles'])->roles->contains(function ($role) {
            return $role->slug === 'admin';
        });

        if(!$isAdmin) {
            return response()->json([
                'success' => false,
                'errors' => ['unauthorized' => ['You are not authorized to access this resource']],
            ], 403);
        }

        return $next($request);
    }
}
