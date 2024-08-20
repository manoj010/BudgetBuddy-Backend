<?php

namespace App\Traits;

use Carbon\Carbon;

trait DateFilter
{
    public function filterDate($query)
    {
        $fromDate = $this->input('from_date');
        $toDate = $this->input('to_date');

        return $this->applyDateFilters($query, $fromDate, $toDate);
    }

    protected function applyDateFilters($query, $fromDate, $toDate)
    {
        $query->where('created_by', auth()->id());
        $toDate = Carbon::parse($toDate)->endOfDay();

        if(!$toDate) {
            $toDate = Carbon::now()->endOfDay();
        }

        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }

        return $query;
    }
}
