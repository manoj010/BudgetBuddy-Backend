<?php

namespace App\Http\Controllers;

use App\Http\Requests\IncomeRequest;
use App\Http\Resources\{IncomeCollection, IncomeResource};
use App\Models\{Income, UserBalance};
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class IncomeController extends BaseController
{
    protected $income;

    public function __construct(Income $income)
    {
        $this->income = $income;
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
            $balance = UserBalance::firstOrNew();
            $balance->total_income += $income->amount;
            $balance->balance += $income->amount;
            $balance->save();
            DB::commit();
            return $this->success(new IncomeResource($income), 'Income created successfully', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }

    public function show($id)
    {
        try {
            DB::beginTransaction();
            $this->checkOrFindResource($this->income, $id);
            $specificResource = $this->income->where('created_by', auth()->id())->find($id);
            DB::commit();
            return $this->success(new IncomeResource($specificResource));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
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
            $balance = UserBalance::firstOrNew();
            $balance->total_income += $amountDifference;
            $balance->balance += $amountDifference;
            $balance->save();
            DB::commit();
            return $this->success(new IncomeResource($income), 'Income updated Successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }

    public function destroy(Income $income)
    {
        $this->checkOwnership($income);
        try {
            DB::beginTransaction();
            $balance = UserBalance::firstOrNew();
            $balance->total_income -= $income->amount;
            $balance->balance -= $income->amount;
            $balance->save();
            $income->delete();
            DB::commit();
            return $this->success('Income deleted Successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }
}
