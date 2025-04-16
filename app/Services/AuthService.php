<?php

namespace App\Services;

use App\Repositories\AccountNumberRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
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
                // Get Token
                $request->session()->regenerate();

                return [
                    'success' => true,
                    'status' => 200
                ];
            }

            return [
                'success' => false,
                'errors' => ['access' => ['Unauthorized']],
                'status' => 401
            ];

        }

        return [
            'success' => false,
            'errors' => ['access' => ['Incorrect credentials']],
            'status' => 401
        ];
    }

    public function logout()
    {
        // Logic for user logout
    }

    /**
     * @throws RandomException
     */
    public function register($userData): Model|Collection|Builder|array|null
    {
        $userData['password'] = Hash::make($userData['password']);

        DB::beginTransaction();
        try {
            $user = $this->userRepository->storeUser($userData);
            $this->accountNumberRepository->createAccountNumberForUser($user);

            $totalUsers = $this->userRepository->getUsers()->count();
            if($totalUsers === 0) {
                $this->roleRepository->getRoleBySlug('super-admin')->users()->attach($user->id);
            }else{
                $this->roleRepository->getRoleBySlug('user')->users()->attach($user->id);
            }

            DB::commit();

            return [
                'success' => true,
                'user' => $user,
                'message' => 'User Created Successfully',
                'status' => 200,
            ];

        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Error creating user',
                'status' => 500,
            ];
        }


    }

    public function resetPassword($email)
    {
        // Logic for password reset
    }
}
