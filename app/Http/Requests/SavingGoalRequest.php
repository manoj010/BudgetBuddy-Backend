<?php

namespace App\Http\Requests;

use App\Traits\AppResponse;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SavingGoalRequest extends FormRequest
{
    use AppResponse;
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
        return [
            'for_month' => [
                'nullable',
                'date',
                Rule::unique('saving_goals')
                    ->where(function ($query) {
                        return $query->where('created_by', $this->user()->id)
                                     ->whereMonth('for_month', Carbon::now()->month)
                                     ->whereYear('for_month', Carbon::now()->year);
                    })
            ],
            'target_amount' => 'required|numeric|min:0',
        ];
    }

    protected function prepareForValidation()
    {
        if (is_null($this->for_month)) {
            $this->merge([
                'for_month' => Carbon::now()->startOfMonth()->toDateString(),
            ]);
        }
    }
}
