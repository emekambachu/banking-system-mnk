<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserFundsTransferController;
use App\Http\Controllers\UserTransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/login/2fa-verify', [AuthController::class, 'verifyTwoFactorCode']);
Route::post('/login/2fa-send-code', [AuthController::class, 'sendTwoFactorAuthCode']);

Route::group(['middleware' => ['auth:sanctum', 'two_factor_auth']], function () {

    Route::get('/authenticate', [AuthController::class, 'authenticate']);

    Route::get('/currencies', [BaseController::class, 'currencies']);

    Route::get('/users/my-transactions', [UserTransactionController::class, 'myTransactions']);
    Route::post('/users/send-funds/get-beneficiary', [UserFundsTransferController::class, 'getBeneficiary']);
    Route::post('/users/send-funds', [UserFundsTransferController::class, 'sendFunds']);

    Route::middleware(['admin'])->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users/search', [UserController::class, 'search']);
        Route::put('/users/{id}/update-status', [UserController::class, 'updateStatus']);
        Route::post('/users/create', [UserController::class, 'store']);
        Route::get('/users/{id}/transactions', [UserTransactionController::class, 'transactions']);
    });

});
