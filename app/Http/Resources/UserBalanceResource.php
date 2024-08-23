<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserBalanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userBalance = $this->resource;

        return [
            'month' => $userBalance->month,
            'opening_balance' => $userBalance->opening_balance,
            'closing_balance' => $userBalance->closing_balance,
            'total_income' => $userBalance->total_income, 
            'total_expense' => $userBalance->total_expense,
            'total_savings' => $userBalance->saving_balance,
            'current_saving' => $userBalance->total_saving, 
            'total_withdraw' => $userBalance->total_withdraw,
        ];
    }
}
