<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Repositories\TwoFactorAuthRepository;
use App\Services\AuthService;
use App\Services\TwoFactorAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected AuthService $authService;
    protected TwoFactorAuthService $twoFactorAuthService;
    protected TwoFactorAuthRepository $twoFactorAuthRepository;
    public function __construct(
        AuthService $authService,
        TwoFactorAuthService $twoFactorAuthService,
        TwoFactorAuthRepository $twoFactorAuthRepository
    )
    {
        $this->authService = $authService;
        $this->twoFactorAuthService = $twoFactorAuthService;
        $this->twoFactorAuthRepository = $twoFactorAuthRepository;
    }

    public function register(UserRegisterRequest $request): JsonResponse
    {
        try {
            $response = $this->authService->register($request->all());
            return response()->json($response, $response['status']);

        }catch (\Exception $exception){

            Log::error($exception->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['server_error' => ['Unexpected error occurred']],
            ], 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $response = $this->authService->login($request);
            return response()->json($response, $response['status']);

        }catch (\Exception $exception){

            Log::error($exception->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['server_error' => ['Unexpected error occurred']],
            ], 500);

        }
    }

    public function authenticate(): JsonResponse
    {
        try {
            $user = Auth::user()->load([
                'account',
                'roles',
            ]);
            return response()->json([
                'success' => true,
                'user' => new UserResource($user),
            ], 200);

        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['server_error' => ['Unexpected error occurred']],
            ], 401);

        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request);
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
            ]);

        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['server_error' => ['Unexpected error occurred']],
            ], 500);

        }
    }

    public function verifyTwoFactorCode(Request $request): JsonResponse
    {
        try {
            $response = $this->twoFactorAuthService->verify2FA($request->secret);
            return response()->json($response, $response['status']);

        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['server_error' => ['Unexpected error occurred']],
            ], 500);

        }
    }

    public function sendTwoFactorAuthCode(Request $request): JsonResponse
    {
        try {
            $response = $this->twoFactorAuthService->processTwoFactorAuth($request->email);
            return response()->json($response, $response['status']);

        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['server_error' => ['Unexpected error occurred']],
            ], 500);
        }
    }

}
