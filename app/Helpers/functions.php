<?php

namespace App\Helpers;

use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;
use App\Models\LoanCategory;
use App\Models\UserBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class functions
{
    // public static function createDefaultCategories()
    // {
    //     $incomeCategories = [
    //         ['title' => 'Salary', 'description' => 'Income from salary'],
    //         ['title' => 'Freelance', 'description' => 'Income from freelance work'],
    //         ['title' => 'Investments', 'description' => 'Income from investments'],
    //     ];

    //     $expenseCategories = [
    //         ['title' => 'Purchase', 'description' => 'Expenses for purchases'],
    //         ['title' => 'Groceries', 'description' => 'Expenses for groceries'],
    //         ['title' => 'Rent', 'description' => 'Expenses for rent'],
    //         ['title' => 'Utilities', 'description' => 'Expenses for utilities'],
    //     ];

    //     $loanCategories = [
    //         ['title' => 'Personal Loan', 'description' => 'Loan for personal use'],
    //         ['title' => 'Home Loan', 'description' => 'Loan for purchasing a home'],
    //         ['title' => 'Car Loan', 'description' => 'Loan for purchasing a car'],
    //     ];

    //     foreach ($incomeCategories as $category) {
    //         IncomeCategory::create([
    //             'title' => $category['title'],
    //             'description' => $category['description'],
    //         ]);
    //     }

    //     foreach ($expenseCategories as $category) {
    //         ExpenseCategory::create([
    //             'title' => $category['title'],
    //             'description' => $category['description'],
    //         ]);
    //     }

    //     foreach ($loanCategories as $category) {
    //         LoanCategory::create([
    //             'title' => $category['title'],
    //             'description' => $category['description'],
    //         ]);
    //     }
    // }

    // public static function userBalance()
    // {
    //     $totalIncome = DB::table('incomes')
    //         ->where('created_by', auth()->id())
    //         ->sum('amount');

    //     $totalExpense = DB::table('expenses')
    //         ->where('created_by', auth()->id())
    //         ->sum('amount');

    //     $balance = $totalIncome - $totalExpense;

    //     return [
    //         'balance' => $balance,
    //     ];
    // }

    // public static function getOrCreateMonthlyBalance($userId)
    // {
    //     $currentMonth = Carbon::now()->format('Y-m');

    //     return UserBalance::firstOrCreate([
    //         'created_by' => $userId,
    //         'month' => $currentMonth,
    //     ]);
    // }
}
