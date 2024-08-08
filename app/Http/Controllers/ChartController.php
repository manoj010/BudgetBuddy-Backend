<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Saving;
use App\Models\Withdraw;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function monthlyData()
    {
        $user = Auth::user();
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $incomeData = $this->getMonthlyData(Income::class, 'total_income', $user->id, $currentYear, $currentMonth);
        $expenseData = $this->getMonthlyData(Expense::class, 'total_expense', $user->id, $currentYear, $currentMonth);
        $savingData = $this->getMonthlyData(Saving::class, 'total_saving', $user->id, $currentYear, $currentMonth);
        $withdrawData = $this->getMonthlyData(Withdraw::class, 'total_withdraw', $user->id, $currentYear, $currentMonth);

        return response()->json([
            'income_data' => $incomeData,
            'expense_data' => $expenseData,
            'saving_data' => $savingData,
            'withdraw_data' => $withdrawData
        ]);
    }

    private function getMonthlyData($model, $columnAlias, $userId, $year, $month)
    {
        $monthlyData = $model::selectRaw('EXTRACT(MONTH FROM created_at) as month, SUM(amount) as ' . $columnAlias)
            ->where('created_by', $userId)
            ->whereRaw('EXTRACT(YEAR FROM created_at) = ?', [$year])
            ->whereRaw('EXTRACT(MONTH FROM created_at) <= ?', [$month])
            ->groupBy('month')
            ->get();

        $data = array_fill(0, $month, 0);
        foreach ($monthlyData as $item) {
            $data[$item->month - 1] = $item->$columnAlias;
        }

        return $data;
    }
    
    // public function byCategory()
    // {
    //     $incomeData = Income::select('category_id', DB::raw('SUM(amount) as total_amount'))
    //         ->groupBy('category_id')
    //         ->get();


    //     $expenseData = Expense::select('category_id', DB::raw('SUM(amount) as total_amount'))
    //         ->groupBy('category_id')
    //         ->get();

    //     return response()->json([
    //         'income_category_data' => $incomeData,
    //         'expense_category_data' => $expenseData,
    //     ]);
    // }
}
