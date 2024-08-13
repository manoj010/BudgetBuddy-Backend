<?php

namespace App\Http\Controllers;

use App\Traits\AppResponse;
use App\Traits\DateFilter;
use App\Traits\DefaultCategories;

class BaseController extends Controller
{
    use AppResponse, DefaultCategories, DateFilter;

    public function getMonthlyData($model, $column, $userId, $year, $month)
    {
        $monthlyData = $model::selectRaw('EXTRACT(MONTH FROM created_at) as month, SUM('. $column .') as total')
            ->where('created_by', $userId)
            ->whereRaw('EXTRACT(YEAR FROM created_at) = ?', [$year])
            ->whereRaw('EXTRACT(MONTH FROM created_at) <= ?', [$month])
            ->groupByRaw('EXTRACT(MONTH FROM created_at)')
            ->get();
        $data = array_fill(0, $month, 0);
        foreach ($monthlyData as $item) {
            $data[$item->month - 1] = $item->total;
        }
        return $data;
    }
}
