<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ThirdController;
use App\Http\Controllers\AccountReceivableController;
use App\Http\Controllers\AccountStatementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);

});

Route::group(['middleware' => 'auth'], function () {

    Route::get('dashboard/get-data-from-dashboard', [DashboardController::class, 'getDataFromDashboard']);
    
    Route::post('thirds/register-third', [ThirdController::class, 'registerThird']);
    Route::post('thirds/update-third/{third}', [ThirdController::class, 'updateThird']);
    
    Route::get('accounts-receivable/get-account-receivables', [AccountReceivableController::class, 'getAccountReceivables']);
    Route::get('accounts-receivable/get-data-to-register-account-receivable', [AccountReceivableController::class, 'getDataToRegisterAccountReceivable']);
    Route::post('accounts-receivable/register-account-receivable', [AccountReceivableController::class, 'registerAccountReceivable']);
    Route::delete('accounts-receivable/delete-account-receivable/{accountReceivable}', [AccountReceivableController::class, 'deleteAccountReceivable']);
    Route::post('accounts-receivable/update-account-receivable/{accountReceivable}', [AccountReceivableController::class, 'updateAccountReceivable']);
    
    Route::get('accounts-statement/get-thirds', [AccountStatementController::class, 'getThirds']);
    Route::get('accounts-statement/get-account-receivables-pendings-from-third/{third}', [AccountStatementController::class, 'getAccountReceivablesPendingsFromThird']);
    Route::post('accounts-statement/register-deposit/{third}', [AccountStatementController::class, 'registerDeposit']);
    Route::post('accounts-statement/delete-deposit/{deposit}', [AccountStatementController::class, 'deleteDeposit']);
    
});

