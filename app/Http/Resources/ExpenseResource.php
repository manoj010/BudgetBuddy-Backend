<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $expense = $this->resource;
        return [
            'id' => $expense->id,
            'category_id' => $expense->category_id,
            'category_title' => $expense->category->title,
            'date_spent' => $expense->date_spent, 
            'amount' => $expense->amount,
            'notes' => $expense->notes,
            'is_recurring' => $expense->is_recurring
        ];
    }
}
