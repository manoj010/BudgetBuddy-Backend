<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Saving extends BaseModel
{
    use HasFactory;

    protected $fillable = ['amount', 'notes'];
}
