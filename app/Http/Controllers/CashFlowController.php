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

        return response()->json([
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
        ]);
    }

    private function getTotalsByCategory($model, $categoryTable, $transactionTable, $fromDate, $toDate)
    {
        return $model::select("$categoryTable.id", "$categoryTable.title", DB::raw("SUM($transactionTable.amount) as total"))
            ->join($categoryTable, "$transactionTable.category_id", '=', "$categoryTable.id")
            ->groupBy("$categoryTable.id", "$categoryTable.title")
            ->orderBy("$categoryTable.id")
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
}
