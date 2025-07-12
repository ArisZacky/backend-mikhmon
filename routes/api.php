<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\DetailTransaksiController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\RouterController;
use App\Http\Controllers\PaketVoucherController;
use App\Http\Controllers\UserListController;
use App\Http\Controllers\BillingReportController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/v1/sync-vouchers', [UserListController::class, 'syncAll']);

Route::prefix('billing')->group(function () {
    Route::post('/generate-user-report', [BillingReportController::class, 'generateAndSendReport']);
    
    Route::post('/generate-all-reports', [BillingReportController::class, 'generateAndSendAllReports']);

    Route::post('/resend-reports', [BillingReportController::class, 'resendFailedReports']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    Route::get('/userData', [AuthController::class, 'getUserData']);

    Route::post('/createAgent', [AuthController::class, 'addAgent']);
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

    // Router
    Route::get('/routers', [RouterController::class, 'index']);
    Route::get('/routers/{id}', [RouterController::class, 'show']);
    Route::post('/create-routers', [RouterController::class, 'store']);          
    Route::put('/update-routers/{id}', [RouterController::class, 'update']);
    Route::delete('/delete-routers/{id}', [RouterController::class, 'destroy']); 
        
    // Paketan Voucher
    Route::prefix('routers/{router}')->group(function () {
        Route::get('voucher-packets', [PaketVoucherController::class, 'index']);
        Route::get('voucher-packets/{id}', [PaketVoucherController::class, 'show']);
        Route::post('create-voucher-packets', [PaketVoucherController::class, 'store']);
        Route::put('update-voucher-packets/{id}', [PaketVoucherController::class, 'update']);
        Route::delete('delete-voucher-packets/{id}', [PaketVoucherController::class, 'destroy']);

        // UserList
        Route::get('user-lists', [UserListController::class, 'index']);
        Route::get('user-lists/{id}', [UserListController::class, 'show']);
        Route::post('create-user-lists', [UserListController::class, 'store']);
        Route::put('update-user-lists/{id}', [UserListController::class, 'update']);
        Route::delete('delete-user-lists/{id}', [UserListController::class, 'destroy']);
        Route::post('sync-user-lists', [UserListController::class, 'sync']);
    });

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

