<?php

namespace App\Services;

use App\Mail\TwoFactorCodeMail;
use App\Models\User;
use App\Repositories\TwoFactorAuthRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Random\RandomException;

class TwoFactorAuthService
{
    protected UserRepository $userRepository;
    protected TwoFactorAuthRepository $twoFactorAuthRepository;
    public function __construct(
        UserRepository $userRepository
    )
    {
        $this->twoFactorAuthRepository = new TwoFactorAuthRepository();
        $this->userRepository = $userRepository;
    }

    public function processTwoFactorAuth($userEmail): array
    {
        try{
            $userId = $this->userRepository->getUserByEmail($userEmail, [], ['id', 'email'])->id;
            $newTwoFactorAuth = $this->twoFactorAuthRepository->store($userId);
            $this->twoFactorAuthRepository->sendTwoFactorCode($userEmail, $newTwoFactorAuth->secret);
            return [
                'success' => true,
                'status' => 200,
                'message' => 'Two Factor Authentication code sent to your email.'
            ];
        }catch(Exception $exception){
            Log::error($exception->getMessage());
            return [
                'success' => false,
                'status' => 500,
                'errors' => ['server_error' => ['Unexpected error occurred']],
            ];
        }
    }

    public function verify2FA($secret): array
    {
        $verified = $this->twoFactorAuthRepository->verify($secret);
        if(!$verified){
            return [
                'success' => false,
                'status' => 401,
                'errors' => [
                    'access' => ['Invalid or expired two factor authentication code.']
                ],
            ];
        }
        return [
            'success' => true,
            'status' => 200,
            'message' => 'Two factor authentication code verified successfully.'
        ];
    }


    public function sendTwoFactorCode($email, $secret): void
    {
        // Send 2FA secret to log file because this is test
        Log::info('Sending 2FA code to user: ' . $email . ' with code: ' . $secret);
        Mail::to($email)->send(new TwoFactorCodeMail($secret));
    }

}
