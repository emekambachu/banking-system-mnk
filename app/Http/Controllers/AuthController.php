<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected AuthService $authService;
    public function __construct(
        AuthService $authService
    )
    {
        $this->authService = $authService;
    }

    public function register(UserRegisterRequest $request): JsonResponse
    {
        try {
            $response = $this->authService->register($request);
            return response()->json([
                'success' => $response['success'],
                'message' => $response['message'],
            ], $response['status']);

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
            return response()->json([
                'success' => $response['success'],
                'token' => $response['token'],
            ], $response['status']);

        }catch (\Exception $exception){

            Log::error($exception->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['server_error' => ['Unexpected error occurred']],
            ], 500);

        }
    }
}
