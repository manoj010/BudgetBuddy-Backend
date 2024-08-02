<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SavingGoalRequest extends FormRequest
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
        $currentMonth = Carbon::now()->format('Y-m');

        return [
            'for_month' => [
                'nullable',
                'date',
                Rule::unique('saving_goals')
                    ->where(function ($query) use ($currentMonth) {
                        return $query->whereDate('for_month', $currentMonth);
                    })
            ],
            'target_amount' => 'required|numeric|min:0',
        ];
    }
}
