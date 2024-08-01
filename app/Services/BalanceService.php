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

    public function getPreviousMonthBalance($userId)
    {
        $previousMonth = Carbon::now()->subMonth()->format('Y-m');
        return UserBalance::where('created_by', $userId)
            ->where('month', $previousMonth)
            ->first();
    }

    public function createNewMonthlyBalance($userId)
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $previousMonthBalance = $this->getPreviousMonthBalance($userId);

        $newBalance = $previousMonthBalance ? $previousMonthBalance->balance : 0;

        return UserBalance::updateOrCreate(
            ['created_by' => $userId, 'month' => $currentMonth],
            [
                'balance' => $newBalance,
                'total_income' => 0,
                'total_expense' => 0,
                'total_saving' => $previousMonthBalance ? $previousMonthBalance->total_saving : 0,
                'total_withdraw' => 0,
            ]
        );
    }

    public function checkIfNewMonthBalanceCreated($userId)
    {
        $currentMonth = Carbon::now()->format('Y-m');
        return UserBalance::where('created_by', $userId)
            ->where('month', $currentMonth)
            ->exists();
    }
}
