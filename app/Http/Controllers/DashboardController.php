<?php

namespace App\Http\Controllers;

use App\Helpers\functions;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseController
{
    public function total()
    {
        $financialSummary = functions::userBalance();
        return response()->json($financialSummary);
    }

    public function totalByMonth()
    {
        $incomeByMonth = DB::table('incomes')
            ->select(DB::raw('DATE_PART(\'month\', created_at) as month'), DB::raw('SUM(amount) as total_income'))
            ->where('created_by', auth()->id())
            ->groupBy(DB::raw('DATE_PART(\'month\', created_at)'))
            ->orderBy(DB::raw('DATE_PART(\'month\', created_at)'))
            ->get();

        $expenseByMonth = DB::table('expenses')
            ->select(DB::raw('DATE_PART(\'month\', created_at) as month'), DB::raw('SUM(amount) as total_expense'))
            ->where('created_by', auth()->id())
            ->groupBy(DB::raw('DATE_PART(\'month\', created_at)'))
            ->orderBy(DB::raw('DATE_PART(\'month\', created_at)'))
            ->get();

        $results = [];

        for ($i = 1; $i <= 12; $i++) {
            $income = $incomeByMonth->firstWhere('month', $i);
            $expense = $expenseByMonth->firstWhere('month', $i);

            $totalIncome = $income ? $income->total_income : 0;
            $totalExpense = $expense ? $expense->total_expense : 0;
            $balance = $totalIncome - $totalExpense;

            $results[] = [
                'month' => $i,
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'profit_loss' => $balance
            ];
        }

        return response()->json($results);
    }
}
