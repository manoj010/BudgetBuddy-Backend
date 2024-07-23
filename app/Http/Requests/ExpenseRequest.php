<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'amount' => 'required|numeric|min:0',
            'category_id' => [
                'required',
                Rule::exists('expense_categories', 'id')->where(function ($query) use ($userId) {
                    $query->where('created_by', $userId)
                        ->whereNull('archived_at');
                }),
            ],
            'date_spent' => 'nullable|date',
            'notes' => 'nullable|string',
            'is_recurring' => 'required|boolean',
        ];
    }
}
