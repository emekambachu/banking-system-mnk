<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserTransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => [
    'auth:sanctum',
    'api'
]], function () {

    Route::get('/authenticate', [AuthController::class, 'authenticate']);

    Route::get('/users/my-transactions', [UserTransactionController::class, 'myTransactions']);

    Route::middleware(['admin'])->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/search', [UserController::class, 'search']);
        Route::put('/users/{id}/update', [UserController::class, 'update']);
        Route::post('/users/create', [UserController::class, 'store']);
        Route::delete('/users/{id}/delete', [UserController::class, 'destroy']);

        Route::get('/users/{id}/transactions', [UserTransactionController::class, 'show']);
    });

});
