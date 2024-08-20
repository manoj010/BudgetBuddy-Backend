<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait CustomPagination
{
    protected function paginate(Collection $items, $perPage = null)
    {
        if (!$perPage || $perPage <= 0) {
            $perPage = count($items);
        }

        $page = LengthAwarePaginator::resolveCurrentPage();
        $total = $items->count();
        $totalPages = (int) ceil($total / $perPage);

        $sliceStart = ($page - 1) * $perPage;
        $sliceLength = $perPage;

        if ($sliceStart >= $total) {
            $sliceStart = $total;
            $sliceLength = 0;
        }

        $results = $items->slice($sliceStart, $sliceLength)->values();

        return [
            'items' => $results->all(),
            'meta' => [
                'current_page' => $page,
                'last_page' => $totalPages,
                'per_page' => $perPage,
                'total' => $total,
            ],
            'links' => [
                'next_page_url' => $page < $totalPages
                    ? url()->current() . '?page=' . ($page + 1) . '&per_page=' . $perPage
                    : null,
                'prev_page_url' => $page > 1
                    ? url()->current() . '?page=' . ($page - 1) . '&per_page=' . $perPage
                    : null,
                'current_page_url' => url()->current(),
            ]
        ];
    }
}
