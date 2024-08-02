<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavingGoal extends BaseModel
{
    use HasFactory;

    protected $fillable = ['for_month', 'target_amount'];
}
