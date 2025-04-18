<?php

namespace App\Repositories;

use App\Models\TwoFactorAuth;
use Carbon\Carbon;
use Random\RandomException;

class TwoFactorAuthRepository
{
    protected TwoFactorAuth $twoFactorAuth;
    public function __construct()
    {
        $this->twoFactorAuth = new TwoFactorAuth();
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
    public function store($user)
    {
        $secret = generateUniqueRandomString(TwoFactorAuth::class, 'secret', 6);
        $expires_at = Carbon::now()->addMinutes(5);

        $newSecret = $this->twoFactorAuth->where('user_id', $user->id)->update([
            'secret' => $secret,
            'secret_expires_at' => $expires_at,
            'enabled' => true,
        ]);

        return $newSecret->fresh();
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
}
