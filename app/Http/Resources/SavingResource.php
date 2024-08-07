<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $saving = $this->resource;

        return [
            'id' => $saving->id,
            'amount' => $saving->amount,
            'date' => $saving->created_at->format('Y-m-d'),
            'notes' => $saving->notes,
        ];
    }
}
