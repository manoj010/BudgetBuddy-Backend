<?php

use App\Http\Controllers\{
    AuthController, 
    BalanceReportController, 
    CashFlowController, 
    CashMovementController, 
    DashboardController, 
    DropdownController, 
    ExpenseCategoryController, 
    ExpenseController, 
    ImageController, 
    IncomeCategoryController, 
    IncomeController, 
    SavingController, 
    SavingGoalController, 
    TransactionController, 
    UserBalanceController, 
    UserController, 
    WithdrawController
};
use Illuminate\Support\Facades\Route;

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

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:api')->prefix('user')->group(function () {
    Route::get('/profile', [UserController::class, 'index']);
    Route::post('/profile', [UserController::class, 'save']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
    Route::apiResource('/balance', UserBalanceController::class);
    Route::post('/upload', [ImageController::class, 'upload']);
    Route::get('/show/{id}', [ImageController::class, 'show']);
});

Route::middleware('auth:api')->prefix('setup')->group(function () {
    Route::apiResource('income-category', IncomeCategoryController::class);
    Route::apiResource('expense-category', ExpenseCategoryController::class);
});

Route::middleware('auth:api')->prefix('transaction')->group(function () {
    Route::get('/get-transactions', [TransactionController::class, 'getTransactions']);
    Route::apiResource('income', IncomeController::class);
    Route::apiResource('expense', ExpenseController::class);
    Route::apiResource('saving', SavingController::class);
    Route::apiResource('saving-goal', SavingGoalController::class);
    Route::apiResource('withdraw', WithdrawController::class);
});

Route::middleware('auth:api')->prefix('dashboard')->group(function () {
    Route::get('/overview', [DashboardController::class, 'overview']);
});

Route::middleware('auth:api')->prefix('dropdown')->group(function () {
    Route::get('/category/{slug}', [DropdownController::class, 'getCategory']);
});

Route::middleware('auth:api')->prefix('analytics')->group(function () {
    Route::get('/balance', [BalanceReportController::class, 'allData']);
    Route::get('/balance/overview', [BalanceReportController::class, 'overview']);
    Route::get('/cash-flow/overview', [CashFlowController::class, 'overview']);
    Route::get('/cash-movement/overview', [CashMovementController::class, 'overview']);
});
