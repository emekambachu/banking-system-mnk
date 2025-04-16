<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserAccountNumber;
use Illuminate\Support\Facades\Log;
use Random\RandomException;

class AccountNumberRepository
{
    private UserAccountNumber $userAccountNumber;
    public function __construct()
    {
        $this->userAccountNumber = new UserAccountNumber();
    }

    /**
     * @throws RandomException
     */
    public function createAccountNumberForUser($user): void
    {
        if (empty($user)) {
            Log::error('User is empty');
        }
        if (!($user instanceof User)) {
            Log::error('User is not an instance of User');
        }

        $this->userAccountNumber->create([
            'user_id' => $user->id,
            'account_number' => generateUniqueRandomString(User::class, 'email', 10),
            'account_type' => 'savings',
            'currency' => 'GBP',
        ]);
    }
}
