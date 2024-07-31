<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class IncomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userId = Auth::id() ?? 1; // Use authenticated user ID or default to 1
        $now = Carbon::now(); // Current timestamp

        $incomes = [
            [
                'category_id' => 1,
                'amount' => 5000.00,
                'is_recurring' => true,
                'notes' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => 2,
                'amount' => 200.00,
                'is_recurring' => false,
                'notes' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => 3,
                'amount' => 150.00,
                'is_recurring' => false,
                'notes' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => 1,
                'amount' => 5500.00,
                'is_recurring' => true,
                'notes' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => 2,
                'amount' => 300.00,
                'is_recurring' => false,
                'notes' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => 3,
                'amount' => 400.00,
                'is_recurring' => false,
                'notes' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => 1,
                'amount' => 4500.00,
                'is_recurring' => true,
                'notes' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => 2,
                'amount' => 250.00,
                'is_recurring' => false,
                'notes' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => 3,
                'amount' => 600.00,
                'is_recurring' => false,
                'notes' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => 1,
                'amount' => 5200.00,
                'is_recurring' => true,
                'notes' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => 2,
                'amount' => 700.00,
                'is_recurring' => false,
                'notes' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => 3,
                'amount' => 150.00,
                'is_recurring' => false,
                'notes' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => 1,
                'amount' => 4800.00,
                'is_recurring' => true,
                'notes' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => 2,
                'amount' => 1000.00,
                'is_recurring' => false,
                'notes' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => 3,
                'amount' => 200.00,
                'is_recurring' => false,
                'notes' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

    }
}
