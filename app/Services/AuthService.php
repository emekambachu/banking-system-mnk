<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Repositories\AccountNumberRepository;
use App\Repositories\RoleRepository;
use App\Repositories\TwoFactorAuthRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Random\RandomException;

class AuthService
{
    protected UserRepository $userRepository;
    protected AccountNumberRepository $accountNumberRepository;
    protected RoleRepository $roleRepository;
    protected TwoFactorAuthRepository $twoFactorAuthRepository;
    public function __construct(
        UserRepository $userRepository,
        AccountNumberRepository $accountNumberRepository,
        RoleRepository $roleRepository,
        TwoFactorAuthRepository $twoFactorAuthRepository
    )
    {
        $this->accountNumberRepository = $accountNumberRepository;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->twoFactorAuthRepository = $twoFactorAuthRepository;
    }

    /**
     * @throws RandomException
     */
    public function login(
        $request,
    ): array
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user()->load('twoFactorAuth');

            // Check if the user has 2FA enabled
            if ($user->twoFactorAuth && $user->twoFactorAuth->enabled) {
                try {
                    // Generate a new 2FA code and send it to the user
                    // Check code in log file
                    $newTwoFactorAuth = $this->twoFactorAuthRepository->store($user->id);
                    $this->twoFactorAuthRepository->sendTwoFactorCode($user->email, $newTwoFactorAuth->secret);
                    return [
                        'success' => true,
                        'two_factor_auth' => true,
                        'status' => 200,
                        'message' => 'Two Factor Authentication code sent to your email.'
                    ];

                }catch (\Exception $e){
                    Log::error('Error sending 2FA code: ' . $e->getMessage());
                    return [
                        'success' => false,
                        'errors' => ['access' => ['Error sending 2FA code, try again later.']],
                        'status' => 500
                    ];
                }
            }

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
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $cookieName = config('session.cookie');
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

            // if first registered user, assign admin role
            if($totalUsers === 1) {
                $this->roleRepository->getRoleBySlug('admin')->users()->attach($user->id);
                $user->status = 1;
                $user->save();

            }else{
                $this->roleRepository->getRoleBySlug('user')->users()->attach($user->id);
                // Activate 2 factor authentication for non-admin users
                $this->twoFactorAuthRepository->activate($user);
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
