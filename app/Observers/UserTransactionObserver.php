<?php

namespace App\Observers;

use App\Models\UserTransaction;
use Random\RandomException;

class UserTransactionObserver
{
    /**
     * Handle the UserTransaction "created" event.
     * @throws RandomException
     */
    public function creating(UserTransaction $userTransaction): void
    {
        $userTransaction->hash = generateUniqueRandomString(UserTransaction::class, 'hash', 10);
    }

    public function created(UserTransaction $userTransaction): void
    {

    }

    /**
     * Handle the UserTransaction "updated" event.
     */
    public function updated(UserTransaction $userTransaction): void
    {
        //
    }

    /**
     * Handle the UserTransaction "deleted" event.
     */
    public function deleted(UserTransaction $userTransaction): void
    {
        //
    }

    /**
     * Handle the UserTransaction "restored" event.
     */
    public function restored(UserTransaction $userTransaction): void
    {
        //
    }

    /**
     * Handle the UserTransaction "force deleted" event.
     */
    public function forceDeleted(UserTransaction $userTransaction): void
    {
        //
    }
}
