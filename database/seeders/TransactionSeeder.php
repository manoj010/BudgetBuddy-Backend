<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        $incomeCategories = DB::table('income_categories')->pluck('id')->toArray();
        $expenseCategories = DB::table('expense_categories')->pluck('id')->toArray();
        $userBalances = [];

        $currentDate = Carbon::now();
        $currentDay = $currentDate->day;
        $currentMonth = $currentDate->month;

        foreach (range(1, 8) as $month) {
            $totalIncome = 0;
            $totalExpense = 0;
            $totalWithdraw = 0;

            $daysInMonth = $month === $currentMonth ? $currentDay : Carbon::create(2024, $month)->daysInMonth;

            // Initialize opening balance and total saving from previous month
            $openingBalance = $userBalances[$month-1]['closing_balance'] ?? 0;
            $totalSaving = $userBalances[$month-1]['total_saving'] ?? 0;
            $savingBalance = $userBalances[$month-1]['total_saving'] ?? 0;
            $closingBalance = $openingBalance;

            for ($i = 0; $i < 8; $i++) {
                // Generate income and expense dates
                $incomeDate = Carbon::create(2024, $month, rand(1, $daysInMonth))
                    ->setTime(rand(0, 23), rand(0, 59), rand(0, 59))
                    ->format('Y-m-d H:i:s');

                $expenseDate = Carbon::create(2024, $month, rand(1, $daysInMonth))
                    ->setTime(rand(0, 23), rand(0, 59), rand(0, 59))
                    ->format('Y-m-d H:i:s');

                $dateReceived = Carbon::parse($incomeDate)->format('Y-m-d');
                $dateSpent = Carbon::parse($expenseDate)->format('Y-m-d');

                // Generate amounts with a seasonal multiplier
                $seasonalMultiplier = 1 + (rand(-10, 10) / 100);
                $incomeAmount = $this->generateAmount($dateReceived, $seasonalMultiplier, true);
                $expenseAmount = $this->generateAmount($dateSpent, $seasonalMultiplier, false);

                // Insert income
                DB::table('incomes')->insert([
                    'category_id' => $incomeCategories[array_rand($incomeCategories)],
                    'amount' => $incomeAmount,
                    'date_received' => $dateReceived,
                    'notes' => 'Random note',
                    'is_recurring' => (bool)rand(0, 1),
                    'type' => 'Income',
                    'created_at' => $incomeDate,
                    'updated_at' => $incomeDate,
                    'created_by' => 1,
                ]);
                $totalIncome += $incomeAmount;
                $closingBalance += $incomeAmount;

                // Insert expense
                DB::table('expenses')->insert([
                    'category_id' => $expenseCategories[array_rand($expenseCategories)],
                    'amount' => $expenseAmount,
                    'date_spent' => $dateSpent,
                    'notes' => 'Random note',
                    'is_recurring' => (bool)rand(0, 1),
                    'type' => 'Expense',
                    'created_at' => $expenseDate,
                    'updated_at' => $expenseDate,
                    'created_by' => 1,
                ]);
                $totalExpense += $expenseAmount;
                $closingBalance -= $expenseAmount;

                // Handle savings
                if ($closingBalance > 0) {
                    $savingDate = Carbon::create(2024, $month, rand(1, $daysInMonth))
                        ->setTime(rand(0, 23), rand(0, 59), rand(0, 59))
                        ->format('Y-m-d H:i:s');
                    $savingAmount = rand(500, min(5000, $closingBalance));

                    DB::table('savings')->insert([
                        'amount' => $savingAmount,
                        'notes' => 'Random note',
                        'type' => 'Saving',
                        'created_at' => $savingDate,
                        'updated_at' => $savingDate,
                        'created_by' => 1,
                    ]);
                    $savingBalance += $savingAmount;
                    $totalSaving += $savingAmount;
                    $closingBalance -= $savingAmount;
                }

                // Handle withdrawals
                if ($totalSaving >= 0 && rand(0, 1)) {
                    $withdrawAmount = rand(100, $totalSaving);
                    $withdrawDate = Carbon::create(2024, $month, rand(1, $daysInMonth))
                        ->setTime(rand(0, 23), rand(0, 59), rand(0, 59))
                        ->format('Y-m-d H:i:s');

                    DB::table('withdraws')->insert([
                        'amount' => $withdrawAmount,
                        'notes' => 'Random note',
                        'type' => 'Withdraw',
                        'created_at' => $withdrawDate,
                        'updated_at' => $withdrawDate,
                        'created_by' => 1,
                    ]);

                    $totalWithdraw += $withdrawAmount;
                    $totalSaving -= $withdrawAmount;
                }
            }

            // Store closing balance and total saving for the month
            $userBalances[$month] = [
                'closing_balance' => $closingBalance,
                'total_saving' => $totalSaving
            ];

            // Insert user balance for the month
            DB::table('user_balances')->insert([
                'month' => Carbon::create(2024, $month, 1)->format('Y-m-d'),
                'opening_balance' => $openingBalance,
                'closing_balance' => $closingBalance,
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'saving_balance' => $savingBalance,
                'total_saving' => $totalSaving,
                'total_withdraw' => $totalWithdraw,
                'created_by' => 1,
                'created_at' => Carbon::create(2024, $month, 1)->startOfDay()->format('Y-m-d H:i:s'),
                'updated_at' => $month === $currentMonth 
                                ? Carbon::create(2024, $month, $currentDay)->endOfDay()->format('Y-m-d H:i:s')
                                : Carbon::create(2024, $month, 1)->endOfMonth()->format('Y-m-d H:i:s'),
            ]);
        }
    }

    private function generateAmount($date, $multiplier, $isIncome = true)
    {
        $amount = 0;
        $month = date('m', strtotime($date));

        if ($isIncome) {
            $amount = $month % 2 == 0 
                ? (rand(3000, 10000) + rand(5000, 15000) * rand(0, 2) + rand(0, 99) / 100) * $multiplier 
                : (rand(7000, 13000) + rand(6000, 17000) * rand(0, 2) + rand(0, 99) / 100) * $multiplier;
        } else {
            $amount = $month % 2 == 0 
                ? (rand(1000, 7000) + rand(4000, 13000) * rand(0, 2) + rand(0, 99) / 100) * $multiplier 
                : (rand(5000, 15000) + rand(9000, 20000) * rand(0, 2) + rand(0, 99) / 100) * $multiplier;
        }

        if (rand(0, 20) == 1) {
            $amount *= rand(2, 5);
        }

        return $amount;
    }
}
