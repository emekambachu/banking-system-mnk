<?php

namespace App\Repositories;

use App\Mail\TwoFactorCodeMail;
use App\Models\TwoFactorAuth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Random\RandomException;

class TwoFactorAuthRepository
{
    protected TwoFactorAuth $twoFactorAuth;
    public function __construct()
    {
        $this->twoFactorAuth = new TwoFactorAuth();
    }

    public function twoFactorAuth(): TwoFactorAuth
    {
        return $this->twoFactorAuth;
    }

    /**
     * @throws RandomException
     */
    public function activate($user)
    {
        return $this->twoFactorAuth->create([
            'user_id' => $user->id,
            'enabled' => true,
        ]);
    }

    /**
     * @throws RandomException
     */
    public function store($userId)
    {
        $secret = generateUniqueRandomString(TwoFactorAuth::class, 'secret', 6);
        $expires_at = Carbon::now()->addMinutes(5);

        $this->twoFactorAuth->where('user_id', $userId)->update([
            'secret' => $secret,
            'secret_expires_at' => $expires_at,
            'enabled' => true,
        ]);

        return $this->twoFactorAuth->where('user_id', $userId)->first()->refresh();
    }

    public function verify($secret): bool
    {
        $verified = $this->twoFactorAuth->with('user')->where([
            ['secret', $secret],
            ['enabled', true],
            ['secret_expires_at', '>=', Carbon::now()],
        ])->first();

        if ($verified) {
            $verified->secret_verified_at = Carbon::now();
            $verified->save();
            return true;
        }

        return false;
    }

    public function sendTwoFactorCode($email, $secret): void
    {
        Mail::to($email)->send(new TwoFactorCodeMail($secret));
    }
}
