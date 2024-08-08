<?php

namespace App\Http\Controllers;

use App\Http\Resources\DashboardResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends BaseController
{
    public function overview()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $previousMonth = Carbon::now()->subMonth()->format('Y-m');

        $goal = DB::table('saving_goals')
            ->where('created_by', auth()->id())
            ->value('target_amount');

        $currentMonthSummary = DB::table('user_balances')
            ->where('created_by', auth()->id())
            ->where('created_at', 'like', $currentMonth . '%')
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
                    $percentageChange = (($currentValue - $previousValue) / $previousValue) * 100;
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

        $response = [
            'current_month' => new DashboardResource($currentMonthSummary),
            'percentage_changes' => $percentageChanges,
            'goal' => $goal
        ];

        return $this->success($response, 'Summary', Response::HTTP_OK);
    }
}
