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
