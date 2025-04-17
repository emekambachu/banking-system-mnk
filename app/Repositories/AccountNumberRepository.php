<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserAccountNumber;
use Exception;
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
    public function createAccountNumberForUser($user): null|array
    {
        if (empty($user)) {
            Log::error('User is empty');
            return [
                'success' => false,
                'errors' => ['user' => ['User is empty']],
                'status' => 422
            ];
        }

        if (!($user instanceof User)) {
            Log::error('User is not an instance of User');
            return [
                'success' => false,
                'errors' => ['user' => ['User is not an instance of User']],
                'status' => 422
            ];
        }

        $hasAccount = $this->userAccountNumber->where('user_id', $user->id)->exists();

        try {
            if (!$hasAccount) {
                $this->userAccountNumber->create([
                    'user_id' => $user->id,
                    'account_number' => generateUniqueRandomString(User::class, 'email', 10),
                    'account_type' => 'savings',
                    'currency' => 'USD',
                    'amount' => 10000.00,
                ]);
            }
        }catch (Exception $e){
            Log::error('Error creating account number for user: ' . $e->getMessage());
            return [
                'success' => false,
                'errors' => ['user' => ['Error creating account number for user']],
                'status' => 422
            ];
        }

        return null;
    }
}
