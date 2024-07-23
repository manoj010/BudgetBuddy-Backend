<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Income extends BaseModel
{
    use HasFactory;

    protected $fillable = ['category_id', 'amount', 'date_received', 'notes'];
}
