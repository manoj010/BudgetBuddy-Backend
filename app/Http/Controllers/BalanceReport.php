<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserBalanceCollection;
use App\Models\UserBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BalanceReport extends BaseController
{
    protected $userBalance;

    public function __construct(UserBalance $userBalance)
    {
        $this->userBalance = $userBalance;
    }

    public function allData()
    {
        $userBalance = $this->userBalance->where('created_by', auth()->id())->get();
        return $this->success(new UserBalanceCollection($userBalance), 'User Balance');
    }

    public function overview()
    {
        $user = Auth::user();
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        
        $openingBalance = $this->getMonthlyData(UserBalance::class, 'opening_balance', $user->id, $currentYear, $currentMonth);
        $closingBalance = $this->getMonthlyData(UserBalance::class, 'closing_balance', $user->id, $currentYear, $currentMonth);

        $response = [
            'balance' => [
                'opening_balance' => $openingBalance,
                'closing_balance' => $closingBalance,
            ],
        ];

        return $this->success($response, 'Summary', Response::HTTP_OK);
    }
}
