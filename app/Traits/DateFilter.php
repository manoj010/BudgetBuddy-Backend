<?php

namespace App\Traits;

use Carbon\Carbon;

trait DateFilter
{
    public function dateFilters($query, $fromDate, $toDate, $filter)
    {
        if ($filter === 'this_month') {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
        } elseif ($filter === 'this_week') {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
        } elseif ($fromDate && $toDate) {
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        } elseif ($filter === 'today') {
            $query->whereDate('created_at', Carbon::today());
        }
        return $query;
    }
}
