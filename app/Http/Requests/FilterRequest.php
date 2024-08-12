<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
{
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

    public function filterDate($query)
    {
        $fromDate = $this->input('from_date');
        $toDate = $this->input('to_date');
        $filter = $this->input('filter');

        return $this->applyDateFilters($query, $fromDate, $toDate, $filter);
    }

    protected function applyDateFilters($query, $fromDate, $toDate, $filter)
    {
        $query->where('created_by', auth()->id());

        if(!$toDate) {
            $toDate = Carbon::now();
        }

        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }
        if ($filter) {
            if ($filter === 'this_month') {
                $startOfMonth = Carbon::now()->startOfMonth();
                $endOfMonth = Carbon::now()->endOfMonth();
                $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
            } elseif ($filter === 'this_week') {
                $startOfWeek = Carbon::now()->startOfWeek();
                $endOfWeek = Carbon::now()->endOfWeek();
                $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            } elseif ($filter === 'today') {
                $query->whereDate('created_at', Carbon::today());
            }
        }

        return $query;
    }
}