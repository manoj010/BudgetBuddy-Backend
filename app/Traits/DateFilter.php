<?php

namespace App\Traits;

use Carbon\Carbon;

trait DateFilter
{
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
