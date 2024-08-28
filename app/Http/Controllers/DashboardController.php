<?php

namespace App\Http\Controllers;

use App\Http\Resources\DashboardResource;
use App\Models\UserBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends BaseController
{
    public function overview()
    {
        $user = Auth::user();
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        $currentMonthData = Carbon::now()->format('Y-m');
        $previousMonth = Carbon::now()->subMonth()->format('Y-m');

        $userImage = $user->image_path ? asset($user->image_path) : null;

        $username = $user->username;

        $goal = DB::table('saving_goals')
            ->where('created_by', auth()->id())
            ->where('archived_at', null)
            ->value('target_amount');

        $currentMonthSummary = DB::table('user_balances')
            ->where('created_by', auth()->id())
            ->where('created_at', 'like', $currentMonthData . '%')
            ->first();

        $previousMonthSummary = DB::table('user_balances')
            ->where('created_by', auth()->id())
            ->where('created_at', 'like', $previousMonth . '%')
            ->first();

        $fields = ['opening_balance', 'closing_balance', 'total_income', 'total_expense', 'total_saving', 'total_withdraw'];

        $percentageChanges = [];

        if ($currentMonthSummary && $previousMonthSummary) {
            foreach ($fields as $field) {
                $currentValue = $currentMonthSummary->$field;
                $previousValue = $previousMonthSummary->$field;

                if ($previousValue != 0) {
                    $percentageChange = (($currentValue - $previousValue) / abs($previousValue)) * 100;
                } else {
                    $percentageChange = ($currentValue != 0) ? 100 : 0;
                }

                $percentageChanges[$field] = $percentageChange;
            }
        } else {
            foreach ($fields as $field) {
                $percentageChanges[$field] = null;
            }
        }

        $percentageChanges = [
            'opening_balance' => round($percentageChanges['opening_balance']),
            'balance' => round($percentageChanges['closing_balance']),
            'total_income' => round($percentageChanges['total_income']),
            'total_expense' => round($percentageChanges['total_expense']),
            'total_saving' => round($percentageChanges['total_saving']),
        ];

        $incomeData = $this->getMonthlyData(UserBalance::class, 'total_income', $user->id, $currentYear, $currentMonth);
        $expenseData = $this->getMonthlyData(UserBalance::class, 'total_expense', $user->id, $currentYear, $currentMonth);

        $response = [
            'financial_data' => [           
                'current_month' => new DashboardResource($currentMonthSummary),
                // 'previous_month' => new DashboardResource($previousMonthSummary),
                'percentage_changes' => $percentageChanges,
                'current_month_goal' => $goal,
            ],
            'charts_data' => [
                'income_data' => $incomeData,
                'expense_data' => $expenseData
            ],
            'username' => $username,
            'user_image' => $userImage,
        ];

        return $this->success($response, 'Summary', Response::HTTP_OK);
    }
}
