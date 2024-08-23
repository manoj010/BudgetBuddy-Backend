<?php

namespace App\Http\Controllers;

use App\Traits\AppResponse;
use App\Traits\CategoryFilter;
use App\Traits\CustomPagination;
use App\Traits\DateFilter;
use App\Traits\DefaultCategories;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{
    use AppResponse, DefaultCategories, DateFilter, CategoryFilter, CustomPagination;

    public function getMonthlyData($model, $column, $userId, $year, $month)
    {
        $monthlyData = $model::selectRaw('EXTRACT(MONTH FROM created_at) as month, SUM(' . $column . ') as total')
            ->where('created_by', $userId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', '<=', $month)
            ->groupByRaw('EXTRACT(MONTH FROM created_at)')
            ->orderByRaw('EXTRACT(MONTH FROM created_at)')
            ->get();

        $data = array_fill(0, $month, 0);
        foreach ($monthlyData as $item) {
            $data[$item->month - 1] = $item->total;
        }

        return $data;
    }

    public function getFilteredDate($request, $model)
    {
        $query = $model::query();
        $data = $request->filterDate($query)->orderByDesc('created_at')->get();
        return $data;
    }

    public function getFilteredCategory($request, $model)
    {
        $query = $model::query();
        $data = $request->categoryFilter($query)->orderByDesc('created_at')->get();
        return $data;
    }

    protected function checkMonth($resource, $message = 'Permission Denied.', $status = Response::HTTP_FORBIDDEN)
    {
        $currentMonth = date('m');
        $currentDate = date('d');
        $resourceMonth = $resource->created_at->format('m');
        $resourceDate = $resource->created_at->format('d');
        if ($resourceMonth !== $currentMonth || ($resourceMonth == $currentMonth && $resourceDate > $currentDate)) {
            return response()->json([
                'status' => 'error',
                'code' => $status,
                'message' => $message
            ], $status);
        }
        return null;
    }
}
