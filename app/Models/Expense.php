<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends BaseModel
{
    use HasFactory;

    protected $fillable = ['category_id', 'amount', 'date_spent', 'notes', 'is_recurring'];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }
}