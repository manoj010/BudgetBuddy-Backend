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

            $daysInMonth = $month === $currentMonth ? $currentDay : Carbon::create(2024, $month)->daysInMonth;

            for ($i = 0; $i < 8; $i++) {
                $incomeDate = Carbon::create(2024, $month, rand(1, $daysInMonth))
                    ->setTime(rand(0, 23), rand(0, 59), rand(0, 59))
                    ->format('Y-m-d H:i:s');

                $expenseDate = Carbon::create(2024, $month, rand(1, $daysInMonth))
                    ->setTime(rand(0, 23), rand(0, 59), rand(0, 59))
                    ->format('Y-m-d H:i:s');

                $dateReceived = Carbon::parse($incomeDate)->format('Y-m-d');
                $dateSpent = Carbon::parse($expenseDate)->format('Y-m-d');
                
                $seasonalMultiplier = 1 + (rand(-10, 10) / 100);

                if (rand(0, 1)) {
                    if (date('m', strtotime($dateReceived)) % 2 == 0) {
                        $incomeAmount = (rand(3000, 10000) + rand(5000, 15000) * rand(0, 2) + rand(0, 99) / 100) * $seasonalMultiplier;
                    } else {
                        $incomeAmount = (rand(7000, 13000) + rand(6000, 17000) * rand(0, 2) + rand(0, 99) / 100) * $seasonalMultiplier;
                    }
                } else {
                    if (date('m', strtotime($dateReceived)) % 2 == 0) {
                        $incomeAmount = (rand(1000, 9000) + rand(2000, 11000) + rand(0, 99) / 100) * $seasonalMultiplier;
                    } else {
                        $incomeAmount = (rand(2000, 10000) + rand(3000, 12000) + rand(0, 99) / 100) * $seasonalMultiplier;
                    }
                }
                
                if (rand(0, 20) == 1) {
                    $incomeAmount *= rand(2, 5);
                }
                
                $expenseMultiplier = 1 + (rand(-15, 15) / 100);
                
                if (rand(0, 1)) {
                    if (date('m', strtotime($dateSpent)) % 2 == 0) {
                        $expenseAmount = (rand(1000, 7000) + rand(4000, 13000) * rand(0, 2) + rand(0, 99) / 100) * $expenseMultiplier;
                    } else {
                        $expenseAmount = (rand(5000, 15000) + rand(9000, 20000) * rand(0, 2) + rand(0, 99) / 100) * $expenseMultiplier;
                    }
                } else {
                    if (date('m', strtotime($dateSpent)) % 2 == 0) {
                        $expenseAmount = (rand(3000, 11000) + rand(5000, 16000) + rand(0, 99) / 100) * $expenseMultiplier;
                    } else {
                        $expenseAmount = (rand(2000, 9000) + rand(6000, 13000) + rand(0, 99) / 100) * $expenseMultiplier;
                    }
                }
                
                if (rand(0, 15) == 1) {
                    $expenseAmount *= rand(2, 4); 
                }

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
            }

            $openingBalance = $userBalances[$month-1]['closing_balance'] ?? 0;
            $closingBalance = $openingBalance + $totalIncome - $totalExpense;
            $totalSaving = 0;
            $totalWithdraw = 0;

            $userBalances[$month] = [
                'closing_balance' => $closingBalance
            ];

            DB::table('user_balances')->insert([
                'month' => Carbon::create(2024, $month, 1)->format('Y-m-d'),
                'opening_balance' => $openingBalance,
                'closing_balance' => $closingBalance,
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
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
}
