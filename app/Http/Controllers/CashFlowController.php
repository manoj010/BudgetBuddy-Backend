<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CashFlowController extends BaseController
{
    public function overview(FilterRequest $request)
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        if (!$fromDate || !$toDate) {
            $fromDate = Carbon::now()->startOfMonth()->toDateString();
            $toDate = Carbon::now()->endOfMonth()->toDateString();
        }

        $incomeTotals = $this->getTotalsByCategory(Income::class, 'income_categories', 'incomes', $fromDate, $toDate);
        $expenseTotals = $this->getTotalsByCategory(Expense::class, 'expense_categories', 'expenses', $fromDate, $toDate);

        list($income_title, $income_total) = $this->getTitlesAndTotals($incomeTotals);
        list($expense_title, $expense_total) = $this->getTitlesAndTotals($expenseTotals);

        $top_income_category = $this->getOverallTotals(Income::class, 'income_categories', 'incomes');
        $top_expense_category = $this->getOverallTotals(Expense::class, 'expense_categories', 'expenses');

        $top_current_income_category = $this->getCurrentOverallTotals(Income::class, 'income_categories', 'incomes');
        $top_current_expense_category = $this->getCurrentOverallTotals(Expense::class, 'expense_categories', 'expenses');
        
        $current_month_income = Income::whereMonth('created_at', now()->month)->sum('amount');
        $overall_income = Income::sum('amount');

        $current_month_expense = Expense::whereMonth('created_at', now()->month)->sum('amount');
        $overall_expense = Expense::sum('amount');

        $monthlyIncomeTotals = DB::table('incomes')
            ->select(DB::raw('EXTRACT(MONTH FROM date_received) as month'), DB::raw('SUM(amount) as amount'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyExpenseTotals = DB::table('expenses')
            ->select(DB::raw('EXTRACT(MONTH FROM date_spent) as month'), DB::raw('SUM(amount) as amount'))
            ->groupBy('month')
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
                'total_by_category' => [
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
