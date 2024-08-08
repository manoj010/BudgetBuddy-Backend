<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserBalancesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $julyMonth = Carbon::createFromDate(2024, 7, 1)->startOfMonth()->format('Y-m-d');
        $julyStartDate = Carbon::createFromDate(2024, 7, 1)->format('Y-m-d H:i:s');
        $julyEndDate = Carbon::createFromDate(2024, 7, 31)->format('Y-m-d H:i:s');

        $userIds = DB::table('users')->pluck('id');

        foreach ($userIds as $userId) {
            DB::table('user_balances')->insert([
                'month' => $julyMonth,
                'opening_balance' => 0,
                'closing_balance' => 15000.00,
                'total_income' => 18000.00,
                'total_expense' => 2000.00,
                'total_saving' => 2000.00,
                'total_withdraw' => 1000.00,
                'created_by' => $userId,
                'created_at' => $julyStartDate,
                'updated_at' => $julyEndDate,
            ]);
        }
    }
}
