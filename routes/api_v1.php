<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Api\V1\WalletController;
use App\Services\KafkaProducerService;
use Illuminate\Support\Facades\Route;



Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);


Route::group(['middleware' => 'auth:sanctum'], function() {
    /// Wallet Route
    Route::get('wallet', [WalletController::class, 'wallet']);


    /// Transaction Routes
    Route::resource('transaction', TransactionController::class)->only(['index', 'store']);

    Route::get('transaction/export', [TransactionController::class, 'export']);
});