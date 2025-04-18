<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/logout', [AuthController::class, 'logout']);

Route::get('/{any}', function () {
    return view('index');
})->where('any', '^(?!api).*$');
