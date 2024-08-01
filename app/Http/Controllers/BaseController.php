<?php

namespace App\Http\Controllers;

use App\Traits\AppResponse;
use App\Traits\DefaultCategories;

class BaseController extends Controller
{
    use AppResponse, DefaultCategories;
}
