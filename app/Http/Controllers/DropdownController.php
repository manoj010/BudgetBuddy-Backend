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
        if($slug == 'income-category') {
            $incomeCategory = IncomeCategory::where('created_by', auth()->id())->where('status', true)->get();
            return $this->success(new BaseCategoryCollection($incomeCategory), 'All Income Category Data', Response::HTTP_OK);
        } elseif($slug == 'expense-category') {
            $expenseCategory = ExpenseCategory::where('created_by', auth()->id())->where('status', true)->get();
            return $this->success(new BaseCategoryCollection($expenseCategory), 'All Expense Category Data', Response::HTTP_OK);
        } elseif($slug == 'loan-category') {
            $loanCategory = LoanCategory::where('created_by', auth()->id())->where('status', true)->get();
            return $this->success(new BaseCategoryCollection($loanCategory), 'All Loan Category Data', Response::HTTP_OK);
        } else {
            return $this->error('Invalid Category Slug');
        }
    }
}
