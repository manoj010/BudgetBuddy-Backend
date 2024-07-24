<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncomeCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['title' => 'Salary', 'description' => 'Monthly salary from primary employment'],
            ['title' => 'Freelance', 'description' => 'Income from freelance work or projects'],
            ['title' => 'Other', 'description' => 'Any other source of income'],
        ];

        foreach ($categories as $category) {
            $categoryWithCreatedBy = array_merge($category, ['created_by' => 0]);

            DB::table('income_categories')->updateOrInsert(
                ['title' => $category['title']],
                $categoryWithCreatedBy
            );
        }
    }
}
