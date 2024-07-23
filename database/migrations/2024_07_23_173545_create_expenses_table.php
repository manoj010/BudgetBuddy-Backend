<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('expense_categories')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('date_spent')->default(DB::raw('CURRENT_DATE'));
            $table->longText('notes')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->defaultInfos();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
