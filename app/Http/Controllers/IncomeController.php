<?php

namespace App\Http\Controllers;

use App\Http\Requests\IncomeRequest;
use App\Http\Resources\{IncomeCollection, IncomeResource};
use App\Models\{Income};
use App\Services\BalanceService;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class IncomeController extends BaseController
{
    protected $income;
    protected $balanceService;

    public function __construct(Income $income, BalanceService $balanceService)
    {
        $this->income = $income;
        $this->balanceService = $balanceService;
    }

    public function index()
    {
        $income = $this->income->where('created_by', auth()->id())->get();
        return $this->success(new IncomeCollection($income), 'All Income');
    }

    public function store(IncomeRequest $request)
    {
        try {
            DB::beginTransaction();
            $validatedData = $request->validated();
            $income = $this->income::create($validatedData);

            $balance = $this->balanceService->getOrCreateMonthlyBalance(auth()->id());
            $balance->total_income += $income->amount;
            $balance->balance += $income->amount;
            $balance->save();

            DB::commit();
            return $this->success(new IncomeResource($income), 'Income created successfully', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $specificResource = $this->income->where('created_by', auth()->id())->findOrFail($id);
            return $this->success(new IncomeResource($specificResource));
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update(IncomeRequest $request, Income $income)
    {
        $this->checkOwnership($income);
        try {
            DB::beginTransaction();

            $prevAmount = $income->amount;
            $newAmount = $request->validated()['amount'];
            $amountDifference = $newAmount - $prevAmount;

            $income->update($request->validated());

            $balance = $this->balanceService->getOrCreateMonthlyBalance(auth()->id());
            $balance->total_income += $amountDifference;
            $balance->balance += $amountDifference;
            $balance->save();

            DB::commit();
            return $this->success(new IncomeResource($income), 'Income updated Successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }

    public function destroy(Income $income)
    {
        $this->checkOwnership($income);
        try {
            DB::beginTransaction();

            $balance = $this->balanceService->getOrCreateMonthlyBalance(auth()->id());
            $balance->total_income -= $income->amount;
            $balance->balance -= $income->amount;
            $balance->save();

            $income->delete();

            DB::commit();
            return $this->success('', 'Income deleted Successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }
}
