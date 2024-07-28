<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $dashboard = $this->resource;
        return [
            'total_income' => $dashboard->total_income,
            'total_expense' => $dashboard->total_expense,
            'total_saving' => $dashboard->total_saving,
            'total_withdraw' => $dashboard->total_withdraw,
            'balance' => $dashboard->balance
        ];
    }
}
