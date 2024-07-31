<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $expenses = [
            [
                'category_id' => 1,
                'amount' => 100.00,
                'is_recurring' => false,
                'notes' => 'Office Supplies',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 2,
                'amount' => 250.00,
                'is_recurring' => true,
                'notes' => 'Monthly Subscription',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 3,
                'amount' => 75.00,
                'is_recurring' => false,
                'notes' => 'Utilities',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 1,
                'amount' => 200.00,
                'is_recurring' => false,
                'notes' => 'Internet Bill',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 2,
                'amount' => 500.00,
                'is_recurring' => true,
                'notes' => 'Annual Software License',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 3,
                'amount' => 300.00,
                'is_recurring' => true,
                'notes' => 'Office Rent',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 1,
                'amount' => 50.00,
                'is_recurring' => false,
                'notes' => 'Stationery',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 2,
                'amount' => 150.00,
                'is_recurring' => false,
                'notes' => 'Marketing Materials',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 3,
                'amount' => 120.00,
                'is_recurring' => true,
                'notes' => 'Electricity Bill',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 1,
                'amount' => 90.00,
                'is_recurring' => false,
                'notes' => 'Cleaning Supplies',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 2,
                'amount' => 60.00,
                'is_recurring' => true,
                'notes' => 'Monthly Service Fee',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 3,
                'amount' => 80.00,
                'is_recurring' => false,
                'notes' => 'Telephone Bill',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 1,
                'amount' => 130.00,
                'is_recurring' => false,
                'notes' => 'Miscellaneous Supplies',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 2,
                'amount' => 220.00,
                'is_recurring' => true,
                'notes' => 'Web Hosting',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 3,
                'amount' => 140.00,
                'is_recurring' => true,
                'notes' => 'Water Bill',
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];

        foreach ($expenses as $expense) {
            DB::table('expenses')->insert($expense);
            DB::table('user_balances')
                ->where('created_by', $expense['created_by'])
                ->decrement('total_expense', $expense['amount']);
        }
    }
}
