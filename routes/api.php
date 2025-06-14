<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\DetailTransaksiController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\BillingController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    Route::get('/userData', [AuthController::class, 'getUserData']);

    // API Resource untuk Agent
    Route::apiResource('agents', AgentController::class);

    Route::get('/getuser', [DetailTransaksiController::class, 'index']);
    Route::get('/AgentTransaction', [AgentController::class, 'AgentTransaction']);

    Route::post('/mikrotikData', [DetailTransaksiController::class, 'postData']);
    Route::get('/dataTransaksi', [TransaksiController::class, 'getData']);
    Route::get('/getDetail', [DetailTransaksiController::class, 'getDetail']);
    Route::post('/createTransaksi', [TransaksiController::class, 'createTX']);
    Route::post('/mark-transactions-sent', [TransaksiController::class, 'markAsSent']);
    
    Route::get('/billing-info', [BillingController::class, 'getBillingInfo']);


    // ✅ Superadmin-specific routes
    Route::prefix('superadmin')->group(function () {
        Route::get('/users', [SuperAdminController::class, 'usersGet']);
        Route::get('/agents', [SuperAdminController::class, 'agentsGet']);
        Route::get('/transaksis', [SuperAdminController::class, 'transaksisGet']);
        Route::get('/detail-transaksis', [SuperAdminController::class, 'detailTransaksisGet']);
    });
});

// Handle preflight requests
Route::options('/{any}', function () {
    return response()->json([], 204);
})->where('any', '.*');

