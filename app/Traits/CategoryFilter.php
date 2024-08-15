<?php

namespace App\Traits;

trait CategoryFilter
{
    public function categoryFilter($query)
    {
        $filter = $this->input('filter');

        return $this->applyFilter($query, $filter);
    }

    protected function applyFilter($query, $filter)
    {
        $query->where('created_by', auth()->id());

        if ($filter) {
            if ($filter === 'active') {
                $query->where('status', true);
            } elseif ($filter === 'inactive') {
                $query->where('status', false);
            }
        }

        return $query;
    }
}
