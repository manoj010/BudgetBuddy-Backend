<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\{Expense, Income, Saving, Withdraw};

class TransactionController extends BaseController
{
    public function getTransactions(FilterRequest $request)
    {        
        $incomes = Income::query();
        $expenses = Expense::query();
        $savings = Saving::query();
        $withdraws = Withdraw::query();

        $incomes = $request->filterDate($incomes);
        $expenses = $request->filterDate($expenses);
        $savings = $request->filterDate($savings);
        $withdraws = $request->filterDate($withdraws);

        $incomes = $incomes->get();
        $expenses = $expenses->get();
        $savings = $savings->get();
        $withdraws = $withdraws->get();

        $allData = $incomes->concat($expenses)->concat($savings)->concat($withdraws);
        $sortedData = $allData->sortByDesc('created_at')->values();

        $transformedData = $sortedData->map(function ($item) {
            return [
                'created_at' => $item->created_at->format('Y-m-d'),
                'transaction_type' => $item->type,  
                'amount' => $item->amount,
            ];
        });

        return response()->json($transformedData);
    }
}
