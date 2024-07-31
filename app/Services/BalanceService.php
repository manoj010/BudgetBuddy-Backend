<?php

namespace App\Services;

use App\Models\UserBalance;
use Carbon\Carbon;

class BalanceService
{
    public function getOrCreateMonthlyBalance($userId)
    {
        $currentMonth = Carbon::now()->format('Y-m');

        return UserBalance::firstOrCreate([
            'created_by' => $userId,
            'month' => $currentMonth,
        ]);
    }

    public function createNewMonthlyBalance($userId)
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $previousMonth = Carbon::now()->subMonth()->format('Y-m');

        $previousBalance = UserBalance::where('created_by', $userId)
            ->where('month', $previousMonth)
            ->first();

        return UserBalance::updateOrCreate([
            'created_by' => $userId,
            'month' => $currentMonth,
        ], [
            'balance' => $previousBalance ? $previousBalance->balance : 0,
            'total_income' => 0,
            'total_expense' => 0,
            'total_saving' => $previousBalance ? $previousBalance->total_saving : 0,
            'total_withdraw' => 0,
        ]);
    }
}
