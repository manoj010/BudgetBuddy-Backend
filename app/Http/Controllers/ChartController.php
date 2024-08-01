<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ChartController extends Controller
{
    public function monthlyData()
    {
        $user = Auth::user();
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        
        $monthlyIncomes = Income::selectRaw('EXTRACT(MONTH FROM created_at) as month, SUM(amount) as total_income')
            ->where('created_by', $user->id)
            ->whereRaw('EXTRACT(YEAR FROM created_at) = ?', [$currentYear])
            ->whereRaw('EXTRACT(MONTH FROM created_at) <= ?', [$currentMonth])
            ->groupBy('month')
            ->get();

        $incomeData = array_fill(0, $currentMonth, 0); 
        foreach ($monthlyIncomes as $income) {
            $incomeData[$income->month - 1] = $income->total_income;
        }

        $monthlyExpenses = Expense::selectRaw('EXTRACT(MONTH FROM created_at) as month, SUM(amount) as total_expense')
            ->where('created_by', $user->id)
            ->whereRaw('EXTRACT(YEAR FROM created_at) = ?', [$currentYear])
            ->whereRaw('EXTRACT(MONTH FROM created_at) <= ?', [$currentMonth])
            ->groupBy('month')
            ->get();

        $expenseData = array_fill(0, $currentMonth, 0); 
        foreach ($monthlyExpenses as $expense) {
            $expenseData[$expense->month - 1] = $expense->total_expense;
        }

        return response()->json([
            'income_data' => $incomeData,
            'expense_data' => $expenseData
        ]);
    }
}
