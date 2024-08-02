<?php

namespace App\Http\Controllers;

use App\Http\Resources\DashboardResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends BaseController
{
    public function total()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $previousMonth = Carbon::now()->subMonth()->format('Y-m');

        // Retrieve financial summary for the current month
        $currentMonthSummary = DB::table('user_balances')
            ->where('created_by', auth()->id())
            ->where('created_at', 'like', $currentMonth . '%')
            ->first();

        // Retrieve financial summary for the previous month
        $previousMonthSummary = DB::table('user_balances')
            ->where('created_by', auth()->id())
            ->where('created_at', 'like', $previousMonth . '%')
            ->first();

        // Initialize fields array
        $fields = ['balance', 'total_income', 'total_expense', 'total_saving', 'total_withdraw'];

        // Calculate percentage difference for each field
        $percentageChanges = [];

        if ($currentMonthSummary && $previousMonthSummary) {
            foreach ($fields as $field) {
                $currentValue = $currentMonthSummary->$field;
                $previousValue = $previousMonthSummary->$field;

                if ($previousValue != 0) {
                    $percentageChange = (($currentValue - $previousValue) / $previousValue) * 100;
                } else {
                    $percentageChange = ($currentValue != 0) ? 100 : 0; // Handle division by zero
                }

                $percentageChanges[$field] = $percentageChange;
            }
        } else {
            foreach ($fields as $field) {
                $percentageChanges[$field] = null; // No data to compare
            }
        }

        // Prepare the response
        $response = [
            'current_month' => new DashboardResource($currentMonthSummary),
            'percentage_changes' => $percentageChanges
        ];

        return $this->success($response, 'Summary', Response::HTTP_OK);
    }
}
