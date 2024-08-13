<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserBalanceCollection;
use App\Models\UserBalance;

class UserBalanceController extends BaseController
{
    // protected $userBalance;

    // public function __construct(UserBalance $userBalance)
    // {
    //     $this->userBalance = $userBalance;
    // }

    // public function index()
    // {
    //     $userBalance = $this->userBalance->where('created_by', auth()->id())->get();
    //     return $this->success(new UserBalanceCollection($userBalance), 'User Balance');
    // }
}
