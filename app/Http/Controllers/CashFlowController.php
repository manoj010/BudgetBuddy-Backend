<?php

// namespace App\Http\Controllers;

// use App\Models\Expense;
// use App\Models\Income;
// use Carbon\Carbon;
// use Illuminate\Support\Facades\DB;

// class IncomeExpenseReport extends BaseController
// {
//     public function overview() {
//         $currentMonth = Carbon::now()->format('Y-m');

//         $incomeTotals = Income::select('income_categories.id', 'income_categories.title', DB::raw('SUM(incomes.amount) as total'))
//             ->join('income_categories', 'incomes.category_id', '=', 'income_categories.id')
//             ->groupBy('income_categories.id', 'income_categories.title')
//             ->orderBy('income_categories.id')
//             ->where('incomes.created_at', 'like', $currentMonth.'%')
//             ->get();

//         $expenseTotals = Expense::select('expense_categories.id', 'expense_categories.title', DB::raw('SUM(expenses.amount) as total'))
//             ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
//             ->groupBy('expense_categories.id', 'expense_categories.title')
//             ->orderBy('expense_categories.id')
//             ->where('expenses.created_at', 'like', $currentMonth.'%')
//             ->get();

//         return response()->json([
//             'income_totals' => $incomeTotals,
//             'expense_totals' => $expenseTotals,
//         ]);
//     }
// }

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CashFlowController extends BaseController
{
    public function overview(FilterRequest $request) {
        $startDate = $request->get('from_date');
        $endDate = $request->get('to_date');
        
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->startOfMonth()->toDateString();
            $endDate = Carbon::now()->endOfMonth()->toDateString();
        }

        $incomeTotals = $this->getTotalsByCategory(Income::class, 'income_categories', 'incomes', $startDate, $endDate);
        $expenseTotals = $this->getTotalsByCategory(Expense::class, 'expense_categories', 'expenses', $startDate, $endDate);

        return response()->json([
            'income_totals' => $incomeTotals,
            'expense_totals' => $expenseTotals,
        ]);
    }

    private function getTotalsByCategory($model, $categoryTable, $transactionTable, $startDate, $endDate) {
        return $model::select("$categoryTable.id", "$categoryTable.title", DB::raw("SUM($transactionTable.amount) as total"))
            ->join($categoryTable, "$transactionTable.category_id", '=', "$categoryTable.id")
            ->groupBy("$categoryTable.id", "$categoryTable.title")
            ->orderBy("$categoryTable.id")
            ->whereBetween("$transactionTable.created_at", [$startDate, $endDate])
            ->get();
    }
}
