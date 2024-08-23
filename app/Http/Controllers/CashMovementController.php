<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserBalanceCollection;
use App\Models\Saving;
use App\Models\SavingGoal;
use App\Models\UserBalance;
use App\Models\Withdraw;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CashMovementController extends BaseController
{
    public function overview()
    {
        $user = Auth::user();
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        $previousMonth = Carbon::now()->subMonth()->month;

        $previous_month_saving_balance = UserBalance::where('created_by', $user->id)
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $previousMonth)
            ->pluck('total_saving')
            ->first();
            
        $current_month_saving = Saving::whereMonth('created_at', now()->month)->sum('amount');
        $overall_saving = Saving::sum('amount');

        $current_month_saving_goal = SavingGoal::whereMonth('created_at', now()->month)->pluck('target_amount');

        $current_month_withdraw = Withdraw::whereMonth('created_at', now()->month)->sum('amount');
        $overall_withdraw = Withdraw::sum('amount');

        $savingsData = $this->getMonthlyData(UserBalance::class, 'saving_balance', $user->id, $currentYear, $currentMonth);
        $withdrawData = $this->getMonthlyData(UserBalance::class, 'total_withdraw', $user->id, $currentYear, $currentMonth);

        $response = [
            'total_cash_movement' => [
                'saving' => [
                    'previous_month_balance' => $previous_month_saving_balance,
                    'current_month' => $current_month_saving,
                    'overall' => $overall_saving,
                ],
                'withdraw' => [
                    'current_month' => $current_month_withdraw,
                    'overall' => $overall_withdraw,
                ],
                'saving_goal' => [
                    'current_month' => $current_month_saving_goal,
                ],
            ],
            'charts_data' => [
                'saving_data' => $savingsData,
                'withdraw_data' => $withdrawData
            ],
        ];

        return $this->success($response, 'Summary', Response::HTTP_OK);
    }
}
