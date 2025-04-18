<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\AccountNumberRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Random\RandomException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    protected UserRepository $userRepository;
    protected AccountNumberRepository $accountNumberRepository;
    protected RoleRepository $roleRepository;
    public function __construct(
        UserRepository $userRepository,
        AccountNumberRepository $accountNumberRepository,
        RoleRepository $roleRepository
    )
    {
        $this->accountNumberRepository = $accountNumberRepository;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    public function login(
        $request,
    ): array
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user) {
                $request->session()->regenerate();

                return [
                    'success' => true,
                    'status' => 200
                ];
            }

            return [
                'success' => false,
                'errors' => ['access' => ['Unauthorized']],
                'status' => 422
            ];

        }

        return [
            'success' => false,
            'errors' => ['access' => ['Incorrect credentials']],
            'status' => 422
        ];
    }

    public function logout($request): JsonResponse
    {
        // 1) Log out the user from the session guard
        Auth::logout();

        // 2) Invalidate & regenerate session/CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 3) Forget the session cookie on the client
        $cookieName = config('session.cookie'); // typically 'laravel_session'
        $forget = Cookie::forget($cookieName);

        return response()->json(['message' => 'Logged out'])
            ->withCookie($forget);
    }

    public function register($userData): array
    {
        $userData['password'] = Hash::make($userData['password']);

        DB::beginTransaction();
        try {
            $user = $this->userRepository->storeUser($userData);
            $this->accountNumberRepository->createAccountNumberForUser($user);

            $totalUsers = $this->userRepository->getUsers()->count();

            if($totalUsers === 1) {
                $this->roleRepository->getRoleBySlug('admin')->users()->attach($user->id);
            }else{
                $this->roleRepository->getRoleBySlug('user')->users()->attach($user->id);
            }

            $userData = $this->userRepository->getUserById($user->id, ['roles', 'account'], ['id']);

            DB::commit();

            return [
                'success' => true,
                'user' => new UserResource($userData),
                'message' => 'User Created Successfully',
                'status' => 200,
            ];

        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Error creating user',
                'user' => null,
                'status' => 500,
            ];
        }


    }

    public function resetPassword($email)
    {
        // Logic for password reset
    }
}
