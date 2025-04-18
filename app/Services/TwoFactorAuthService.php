<?php

namespace App\Services;

use App\Mail\TwoFactorCodeMail;
use App\Repositories\TwoFactorAuthRepository;
use Illuminate\Support\Facades\Mail;
use Random\RandomException;

class TwoFactorAuthService
{
    protected TwoFactorAuthRepository $twoFactorAuthRepository;
    public function __construct()
    {
        $this->twoFactorAuthRepository = new TwoFactorAuthRepository();
    }

    /**
     * @throws RandomException
     */
    public function process2FA($user): void
    {
        $twoFactorAuth = $this->twoFactorAuthRepository->store($user);
        $this->sendTwoFactorCode($user, $twoFactorAuth->secret);
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


    public function sendTwoFactorCode($user, $secret): void
    {
        Mail::to($user->email)->send(new TwoFactorCodeMail($secret));
    }

}
