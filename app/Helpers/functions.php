<?php

namespace App\Helpers;

use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\DB;

class functions
{
    public static function createDefaultCategories()
    {
        $incomeCategories = [
            ['title' => 'Salary', 'description' => 'Income from salary'],
            ['title' => 'Freelance', 'description' => 'Income from freelance work'],
            ['title' => 'Investments', 'description' => 'Income from investments'],
        ];

        $expenseCategories = [
            ['title' => 'Purchase', 'description' => 'Expenses for purchases'],
            ['title' => 'Groceries', 'description' => 'Expenses for groceries'],
            ['title' => 'Rent', 'description' => 'Expenses for rent'],
            ['title' => 'Utilities', 'description' => 'Expenses for utilities'],
        ];

        foreach ($incomeCategories as $category) {
            IncomeCategory::firstOrCreate([
                'title' => $category['title'],
                'description' => $category['description'],
            ]);
        }

        foreach ($expenseCategories as $category) {
            ExpenseCategory::firstOrCreate([
                'title' => $category['title'],
                'description' => $category['description'],
            ]);
        }
    }

    public static function userBalance()
    {
        $totalIncome = DB::table('incomes')
            ->where('created_by', auth()->id())
            ->sum('amount');

        $totalExpense = DB::table('expenses')
            ->where('created_by', auth()->id())
            ->sum('amount');

        $balance = $totalIncome - $totalExpense;

        return [
            'balance' => $balance,
        ];
    }
}
