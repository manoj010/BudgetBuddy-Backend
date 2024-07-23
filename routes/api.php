<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\IncomeCategoryController;
use App\Http\Controllers\LoanCategoryController;
use App\Http\Controllers\SavingCategoryController;
use App\Http\Controllers\UserController;
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
    Route::apiResource('income-category', IncomeCategoryController::class);
    Route::apiResource('expense-category', ExpenseCategoryController::class);
    Route::apiResource('saving-category', SavingCategoryController::class);
    Route::apiResource('loan-category', LoanCategoryController::class);
});

