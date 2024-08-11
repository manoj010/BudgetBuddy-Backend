<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeCategoryController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\LoanCategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DropdownController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SavingController;
use App\Http\Controllers\SavingGoalController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserBalanceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WithdrawController;
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
    Route::apiResource('profile', UserController::class);
    Route::post('/profile', [UserController::class, 'save']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
});

Route::middleware('auth:api')->prefix('setup')->group(function () {
    Route::apiResource('income-category', IncomeCategoryController::class);
    Route::apiResource('expense-category', ExpenseCategoryController::class);
    Route::apiResource('loan-category', LoanCategoryController::class);
});

Route::middleware('auth:api')->prefix('transaction')->group(function () {
    Route::get('/get-transactions', [TransactionController::class, 'getTransactions']);
    Route::apiResource('income', IncomeController::class);
    Route::apiResource('expense', ExpenseController::class);
    Route::apiResource('saving', SavingController::class);
    Route::apiResource('saving-goal', SavingGoalController::class);
    Route::apiResource('withdraw', WithdrawController::class);
    Route::apiResource('balance', UserBalanceController::class);
});

Route::middleware('auth:api')->prefix('dashboard')->group(function () {
    Route::get('/overview', [DashboardController::class, 'overview']);
});

Route::middleware('auth:api')->prefix('reports')->group(function () {
    Route::get('/report', [ReportController::class, 'monthlyData']);
});

Route::middleware('auth:api')->prefix('dropdown')->group(function () {
    Route::get('/category/{slug}', [DropdownController::class, 'getCategory']);
});