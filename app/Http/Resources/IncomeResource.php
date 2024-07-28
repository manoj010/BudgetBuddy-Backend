<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $income = $this->resource;
        return [
            'id' => $income->id,
            'category_title' => $income->category->title,
            'date_received' => $income->date_received, 
            'amount' => $income->amount,
            'notes' => $income->notes,
            'is_recurring' => $income->is_recurring
        ];
    }
}
