<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $category = $this->resource;
        return [
            'id' => $category->id,
            'title' => $category->title,
            'description' => $category->description,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at
        ];
    }
}
