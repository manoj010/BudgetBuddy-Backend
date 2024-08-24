<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CashFlowController extends BaseController
{
    public function overview()
    {
        $fromDate = Carbon::now()->startOfMonth()->toDateString();
        $toDate = Carbon::now()->endOfMonth()->toDateString();

        $incomeTotals = $this->getTotalsByCategory(Income::class, 'income_categories', 'incomes', $fromDate, $toDate);
        $expenseTotals = $this->getTotalsByCategory(Expense::class, 'expense_categories', 'expenses', $fromDate, $toDate);
        $incomeOverall = $this->overallByCategory(Income::class, 'income_categories', 'incomes', 'category_id', 'amount');
        $expenseOverall = $this->overallByCategory(Expense::class, 'expense_categories', 'expenses', 'category_id', 'amount');
        
        list($income_title, $income_total) = $this->getTitlesAndTotals($incomeTotals);
        list($expense_title, $expense_total) = $this->getTitlesAndTotals($expenseTotals);
        list($income_title_overall, $income_total_overall) = $this->getTitlesAndTotals($incomeOverall);
        list($expense_title_overall, $expense_total_overall) = $this->getTitlesAndTotals($expenseOverall);

        $top_income_category = $this->getOverallTotals(Income::class, 'income_categories', 'incomes');
        $top_expense_category = $this->getOverallTotals(Expense::class, 'expense_categories', 'expenses');

        $top_current_income_category = $this->getCurrentOverallTotals(Income::class, 'income_categories', 'incomes');
        $top_current_expense_category = $this->getCurrentOverallTotals(Expense::class, 'expense_categories', 'expenses');

        $current_month_income = Income::whereMonth('created_at', now()->month)->sum('amount');
        $overall_income = Income::sum('amount');
        $current_month_expense = Expense::whereMonth('created_at', now()->month)->sum('amount');
        $overall_expense = Expense::sum('amount');

        $monthlyIncomeTotals = DB::table('incomes')
            ->select(DB::raw('EXTRACT(YEAR FROM date_received) as year'), DB::raw('EXTRACT(MONTH FROM date_received) as month'), DB::raw('SUM(amount) as amount'))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $monthlyExpenseTotals = DB::table('expenses')
            ->select(DB::raw('EXTRACT(YEAR FROM date_spent) as year'), DB::raw('EXTRACT(MONTH FROM date_spent) as month'), DB::raw('SUM(amount) as amount'))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return response()->json([
            'total_cash_flow' => [
                'income' => [
                    'current_month' => $current_month_income,
                    'overall' => $overall_income,
                ],
                'expense' => [
                    'current_month' => $current_month_expense,
                    'overall' => $overall_expense,
                ]
            ],
            'charts' => [
                'overall_total_by_category' => [
                    'income_totals' => [
                        'title' => $income_title_overall,
                        'total' => $income_total_overall
                    ],
                    'expense_totals' => [
                        'title' => $expense_title_overall,
                        'total' => $expense_total_overall
                    ],
                ],
                'current_total_by_category' => [
                    'income_totals' => [
                        'title' => $income_title,
                        'total' => $income_total
                    ],
                    'expense_totals' => [
                        'title' => $expense_title,
                        'total' => $expense_total
                    ],
                ],
            ],
            'top_3' => [
                'overall' => [
                    'income_categories' => $top_income_category,
                    'expense_categories' => $top_expense_category
                ],
                'current_month' => [
                    'income_categories' => $top_current_income_category,
                    'expense_categories' => $top_current_expense_category
                ],
            ],
            'total_by_month_table' => [
                'income' => $monthlyIncomeTotals,
                'expense' => $monthlyExpenseTotals
            ]
        ]);
    }

    private function getTotalsByCategory($model, $categoryTable, $transactionTable, $fromDate, $toDate)
    {
        return $model::select("$categoryTable.id", "$categoryTable.title", DB::raw("SUM($transactionTable.amount) as total"))
            ->join($categoryTable, "$transactionTable.category_id", '=', "$categoryTable.id")
            ->groupBy("$categoryTable.id", "$categoryTable.title")
            ->orderBy(DB::raw("SUM($transactionTable.amount)"), 'desc')
            ->whereBetween("$transactionTable.created_at", [$fromDate, $toDate])
            ->get();
    }

    private function overallByCategory($model, $categoryTable, $transactionTable, $categoryIdField, $amountField)
    {
        return $model::select("$categoryTable.id", "$categoryTable.title", DB::raw("SUM($transactionTable.$amountField) as total"))
        ->join($categoryTable, "$transactionTable.$categoryIdField", '=', "$categoryTable.id")
        ->groupBy("$categoryTable.id", "$categoryTable.title")
        ->orderBy(DB::raw("SUM($transactionTable.$amountField)"), 'desc')
        ->get();
    }

    private function getTitlesAndTotals($totals)
    {
        $titles = [];
        $totalsArray = [];
        foreach ($totals as $total) {
            $titles[] = $total->title;
            $totalsArray[] = $total->total;
        }
        return [$titles, $totalsArray];
    }

    private function getOverallTotals($model, $categoryTable, $transactionTable)
    {
        return $model::select("$categoryTable.title", DB::raw("SUM($transactionTable.amount) as total"))
            ->join($categoryTable, "$transactionTable.category_id", '=', "$categoryTable.id")
            ->groupBy("$categoryTable.id", "$categoryTable.title")
            ->orderBy(DB::raw("SUM($transactionTable.amount)"), 'desc')
            ->limit(3)
            ->get();
    }

    private function getCurrentOverallTotals($model, $categoryTable, $transactionTable)
    {
        return $model::select("$categoryTable.title", DB::raw("SUM($transactionTable.amount) as total"))
            ->join($categoryTable, "$transactionTable.category_id", '=', "$categoryTable.id")
            ->groupBy("$categoryTable.id", "$categoryTable.title")
            ->orderBy(DB::raw("SUM($transactionTable.amount)"), 'desc')
            ->whereMonth("$transactionTable.created_at", now()->month)
            ->limit(3)
            ->get();
    }
}
