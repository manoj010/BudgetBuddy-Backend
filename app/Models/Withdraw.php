<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Withdraw extends BaseModel
{
    use HasFactory;

    protected $fillable = ['amount', 'notes'];
}
