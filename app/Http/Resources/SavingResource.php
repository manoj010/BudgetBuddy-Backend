<?php

namespace App\Http\Resources;

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
            'date_saved' => $saving->date_saved,
            'notes' => $saving->notes,
        ];
    }
}
