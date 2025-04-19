<?php

namespace Database\Seeders;

use App\Models\TwoFactorAuth;
use App\Repositories\TwoFactorAuthRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TwoFactorAuthSeeder extends Seeder
{
    protected TwoFactorAuthRepository $twoFactorAuthRepository;
    protected UserRepository $userRepository;
    public function __construct(
        TwoFactorAuthRepository $twoFactorAuthRepository,
        UserRepository $userRepository
    )
    {
        $this->twoFactorAuthRepository = $twoFactorAuthRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = $this->userRepository
            ->getUsers([], ['twoFactorAuth', 'roles'])
            ->whereDoesntHave('twoFactorAuth')->get();

        foreach ($users as $user) {
            $this->twoFactorAuthRepository->activate($user);
        }

    }
}
