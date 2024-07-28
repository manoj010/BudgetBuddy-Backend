<?php

namespace App\Http\Controllers;

use App\Http\Requests\WithdrawRequest;
use App\Http\Resources\WithdrawCollection;
use App\Http\Resources\WithdrawResource;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Withdraw;
use App\Models\UserBalance;
use Illuminate\Support\Facades\DB;

class WithdrawController extends BaseController

{
    protected $withdraw;

    public function __construct(Withdraw $withdraw)
    {
        $this->withdraw = $withdraw;
    }

    public function index()
    {
        $withdraw = $this->withdraw->where('created_by', auth()->id())->get();
        return $this->success(new WithdrawCollection($withdraw), 'All Withdraw');
    }

    public function store(WithdrawRequest $request)
    { 
        try {
            DB::beginTransaction();
            $validatedData = $request->validated();

            $balance = UserBalance::where('created_by', auth()->id())->firstOrNew();
        
            if ($balance->total_saving < $validatedData['amount']) {
                DB::rollBack();
                return $this->error('Insufficient balance to withdraw this amount.', Response::HTTP_BAD_REQUEST);
            }

            $withdraw = $this->withdraw::create($validatedData);
            $balance = UserBalance::firstOrNew();
            $balance->total_withdraw += $withdraw->amount;
            $balance->total_saving -= $withdraw->amount;
            $balance->balance += $withdraw->amount;
            $balance->save();
            DB::commit();
            return $this->success(new WithdrawResource($withdraw), 'Amount Withdrawn', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }
}
