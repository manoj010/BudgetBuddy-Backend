<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $withdraw = $this->resource;

        return [
            'id' => $withdraw->id,
            'amount' => $withdraw->amount,
            'date' => $withdraw->created_at->format('Y-m-d'),
            'notes' => $withdraw->notes,
        ];
    }
}
