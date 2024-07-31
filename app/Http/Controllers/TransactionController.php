<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\{Expense, Income, Saving, Withdraw};

class TransactionController extends BaseController
{
    public function getTransactions()
    {
        $user = Auth::user();

        $incomes = Income::where('created_by', $user->id)->get();
        $expenses = Expense::where('created_by', $user->id)->get();
        $savings = Saving::where('created_by', $user->id)->get();
        $withdraws = Withdraw::where('created_by', $user->id)->get();

        $allData = $incomes->concat($expenses)->concat($savings)->concat($withdraws);
        $sortedData = $allData->sortByDesc('created_at')->values();

        $transformedData = $sortedData->map(function ($item) {
            return [
                'created_at' => $item->created_at->format("Y-m-d"),
                'transaction_type' => $item->type,  
                'amount' => $item->amount,
            ];
        });

        return response()->json($transformedData);
    }
}
