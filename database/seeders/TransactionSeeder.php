<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $incomeCategories = DB::table('income_categories')->pluck('id')->toArray();
        $expenseCategories = DB::table('expense_categories')->pluck('id')->toArray();

        foreach (range(1, 7) as $month) {
            for ($i = 0; $i < 8; $i++) {
                $incomeDate = Carbon::create(2024, $month, rand(1, Carbon::create(2024, $month)->daysInMonth))
                    ->setTime(rand(0, 23), rand(0, 59), rand(0, 59))
                    ->format('Y-m-d H:i:s');

                $expenseDate = Carbon::create(2024, $month, rand(1, Carbon::create(2024, $month)->daysInMonth))
                    ->setTime(rand(0, 23), rand(0, 59), rand(0, 59))
                    ->format('Y-m-d H:i:s');

                $dateReceived = Carbon::parse($incomeDate)->format('Y-m-d');
                $dateSpent = Carbon::parse($expenseDate)->format('Y-m-d');

                if (date('m', strtotime($dateReceived)) % 2 == 0) {
                    $incomeAmount = rand(5000, 12000) + rand(3000, 8000) + rand(0, 99)/100;
                } else {
                    $incomeAmount = rand(8000, 15000) + rand(5000, 10000) + rand(0, 99)/100;
                }

                if (date('m', strtotime($dateSpent)) % 2 == 0) {
                    $expenseAmount = rand(2000, 8000) + rand(2000, 12000) + rand(0, 99)/100;
                } else {
                    $expenseAmount = rand(4000, 12000) + rand(8000, 15000) + rand(0, 99)/100;
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
            }
        }
    }
}
