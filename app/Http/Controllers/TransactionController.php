<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\{Expense, Income, Saving, Withdraw};

class TransactionController extends BaseController
{
    public function getTransactions(Request $request)
    {
        $user = Auth::user();

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $filter = $request->input('filter'); 
        
        $incomes = Income::where('created_by', $user->id);
        $expenses = Expense::where('created_by', $user->id);
        $savings = Saving::where('created_by', $user->id);
        $withdraws = Withdraw::where('created_by', $user->id);

        $incomes = $this->dateFilters($incomes, $fromDate, $toDate, $filter);
        $expenses = $this->dateFilters($expenses, $fromDate, $toDate, $filter);
        $savings = $this->dateFilters($savings, $fromDate, $toDate, $filter);
        $withdraws = $this->dateFilters($withdraws, $fromDate, $toDate, $filter);

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
