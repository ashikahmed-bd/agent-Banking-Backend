<?php

use App\Http\Controllers\AccountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('account')->group(function (){
    Route::get('all', [AccountController::class, 'index']);
    Route::get('balance', [AccountController::class, 'getBalance']);
    Route::post('{account}/deposit', [AccountController::class, 'deposit']);
    Route::post('{account}/withdraw', [AccountController::class, 'withdraw']);
});

Route::get('accounts', [AccountController::class, 'getAccounts']);


Route::prefix('transaction')->group(function (){
    Route::get('latest', [AccountController::class, 'latestTransaction']);
});
