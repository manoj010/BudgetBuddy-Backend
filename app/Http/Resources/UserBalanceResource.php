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
            'total_income' => $userBalance->total_income, 
            'total_expense' => $userBalance->total_expense, 
            'total_saving' => $userBalance->total_saving, 
            'total_withdraw' => $userBalance->total_withdraw, 
            'balance' => $userBalance->balance,
        ];
    }
}
