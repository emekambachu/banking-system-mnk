<?php

namespace App\Services;

use App\Mail\TwoFactorCodeMail;
use Illuminate\Support\Facades\Mail;

class TwoFactorAuthService
{
    protected TwoFactorAuthService $twoFactorAuthService;
    public function __construct()
    {
        $this->twoFactorAuthService = new TwoFactorAuthService();
    }


    public function sendTwoFactorCode($user, $secret): void
    {
        Mail::to($user->email)->send(new TwoFactorCodeMail($secret));
    }

}
