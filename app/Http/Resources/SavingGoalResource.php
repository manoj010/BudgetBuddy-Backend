<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavingGoalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $savingGoal = $this->resource;

        return [
            'id' => $savingGoal->id,
            'for_month' => $savingGoal->for_month,                
            'target_amount' => $savingGoal->target_amount
        ];
    }
}
