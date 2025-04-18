<?php

namespace App\Providers;

use App\Models\UserTransaction;
use App\Observers\UserTransactionObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Observers
        UserTransaction::observe(UserTransactionObserver::class);
    }
}
