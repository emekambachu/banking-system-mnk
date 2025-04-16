<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

// This endpoint sets the CSRF cookie
Route::get('/sanctum/csrf-cookie', function (Request $request) {
    return response()->json(['message' => 'CSRF cookie set']);
})->middleware('web');

Route::get('/{any}', function () {
    return view('index');
})->where('any', '^(?!api).*$');
