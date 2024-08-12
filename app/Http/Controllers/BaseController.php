<?php

namespace App\Http\Controllers;

use App\Traits\AppResponse;
use App\Traits\DateFilter;
use App\Traits\DefaultCategories;

class BaseController extends Controller
{
    use AppResponse, DefaultCategories, DateFilter;

    public function checkStatus() {
        
    }
}
