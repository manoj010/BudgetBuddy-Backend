<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseCategoryCollection;
use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\LoanCategory;
use Symfony\Component\HttpFoundation\Response;

class DropdownController extends BaseController
{
    public function getCategory($slug)
    {
        $models = [
            'income-category' => IncomeCategory::class,
            'expense-category' => ExpenseCategory::class,
            'loan-category' => LoanCategory::class,
        ];

        if (isset($models[$slug])) {
            $model = $models[$slug];
            $categories = $model::where('created_by', auth()->id())
                ->where('status', true)
                ->get();
            
            $sortedData = $categories->sortByDesc('created_at')->values();

            return $this->success(new BaseCategoryCollection($sortedData), "All {$slug} Data", Response::HTTP_OK);
        }

        return $this->error('Invalid Category.', Response::HTTP_BAD_REQUEST);
    }
}
