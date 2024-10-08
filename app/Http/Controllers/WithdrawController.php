<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Http\Requests\WithdrawRequest;
use App\Http\Resources\{WithdrawCollection, WithdrawResource};
use App\Models\Withdraw;
use App\Services\BalanceService;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class WithdrawController extends BaseController
{
    protected $withdraw;
    protected $balanceService;

    public function __construct(Withdraw $withdraw, BalanceService $balanceService)
    {
        $this->withdraw = $withdraw;
        $this->balanceService = $balanceService;
    }
    
    public function index(FilterRequest $request)
    {
        $withdraw = $this->getFilteredDate($request, $this->withdraw);
        return $this->success(new WithdrawCollection($withdraw), 'All Withdraw');
    }

    public function store(WithdrawRequest $request)
    { 
        try {
            DB::beginTransaction();
            $validatedData = $request->validated();

            if (!$this->balanceService->checkIfNewMonthBalanceCreated(auth()->id())) {
                $this->balanceService->createNewMonthlyBalance(auth()->id());
            }

            $balance = $this->balanceService->getOrCreateMonthlyBalance(auth()->id());
        
            if ($balance->total_saving < $validatedData['amount']) {
                DB::rollBack();
                return $this->error('Insufficient balance to withdraw this amount.', Response::HTTP_BAD_REQUEST);
            }

            $withdraw = $this->withdraw::create($validatedData);
            $balance->total_withdraw += $withdraw->amount;
            $balance->total_saving -= $withdraw->amount;
            $balance->closing_balance += $withdraw->amount;
            $balance->save();

            DB::commit();
            return $this->success(new WithdrawResource($withdraw), 'Amount Withdrawn', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $specificResource = $this->withdraw->where('created_by', auth()->id())->findOrFail($id);
            return $this->success(new WithdrawResource($specificResource));
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function update(WithdrawRequest $request, Withdraw $withdraw)
    {
        $this->checkOwnership($withdraw);

        if ($response = $this->checkMonth($withdraw)) {
            return $response;
        }

        try {
            DB::beginTransaction();
            $prevAmount = $withdraw->amount;
            $newAmount = $request->validated()['amount'];
            $amountDifference = $newAmount - $prevAmount;

            $withdraw->update($request->validated());

            $balance = $this->balanceService->getOrCreateMonthlyBalance(auth()->id());
            $balance->total_withdraw += $amountDifference;
            $balance->total_saving -= $amountDifference;
            $balance->closing_balance += $amountDifference;
            $balance->save();

            DB::commit();
            return $this->success(new WithdrawResource($withdraw), 'Withdraw updated Successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }

    public function destroy(Withdraw $withdraw)
    {
        $this->checkOwnership($withdraw);

        if ($response = $this->checkMonth($withdraw)) {
            return $response;
        }
        
        try {
            DB::beginTransaction();

            $balance = $this->balanceService->getOrCreateMonthlyBalance(auth()->id());
            $balance->total_withdraw -= $withdraw->amount;
            $balance->closing_balance -= $withdraw->amount;
            $balance->save();

            $withdraw->delete();

            DB::commit();
            return $this->success('', 'Withdraw deleted Successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }
}
