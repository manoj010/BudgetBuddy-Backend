<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserBalance extends BaseModel
{
    use HasFactory;
    
    protected $fillable = ['month', 'total_income', 'total_expense', 'total_saving', 'total_withdraw', 'opening_balance', 'closing_balance'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
