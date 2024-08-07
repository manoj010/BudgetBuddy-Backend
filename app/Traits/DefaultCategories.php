<?php

namespace App\Traits;

use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\LoanCategory;

trait DefaultCategories
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

        $loanCategories = [
            ['title' => 'Personal Loan', 'description' => 'Loan for personal use'],
            ['title' => 'Home Loan', 'description' => 'Loan for purchasing a home'],
            ['title' => 'Car Loan', 'description' => 'Loan for purchasing a car'],
        ];

        foreach ($incomeCategories as $category) {
            IncomeCategory::create([
                'title' => $category['title'],
                'description' => $category['description'],
                'status' => true,
            ]);
        }

        foreach ($expenseCategories as $category) {
            ExpenseCategory::create([
                'title' => $category['title'],
                'description' => $category['description'],
                'status' => true,
            ]);
        }

        foreach ($loanCategories as $category) {
            LoanCategory::create([
                'title' => $category['title'],
                'description' => $category['description'],
                'status' => true,
            ]);
        }
    }
}
