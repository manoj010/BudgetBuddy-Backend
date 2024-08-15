<?php

namespace App\Http\Requests;

use App\Traits\AppResponse;
use App\Traits\CategoryFilter;
use App\Traits\DateFilter;
use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
{
    use DateFilter, CategoryFilter, AppResponse;
    
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'filter' => 'nullable|string',
        ];
    }
}