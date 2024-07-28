<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Http\Resources\ExpenseCollection;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Models\UserBalance;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ExpenseController extends BaseController
{
    protected $expense;

    public function __construct(Expense $expense)
    {
        $this->expense = $expense;
    }

    public function index()
    {
        $expense = $this->expense->where('created_by', auth()->id())->get();
        return $this->success(new ExpenseCollection($expense), 'All Expense');
    }

    public function store(ExpenseRequest $request)
    {
        try {
            DB::beginTransaction();
            $validatedData = $request->validated();
            $expense = $this->expense::create($validatedData);
            $balance = UserBalance::firstOrNew();
            $balance->total_expense += $expense->amount;
            $balance->balance -= $expense->amount;
            $balance->save();
            DB::commit();
            return $this->success(new ExpenseResource($expense), 'Expense created successfully', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }

    public function show($id)
    {
        try {
            DB::beginTransaction();
            $this->checkOrFindResource($this->expense, $id);
            $specificResource = $this->expense->where('created_by', auth()->id())->find($id);
            DB::commit();
            return $this->success(new ExpenseResource($specificResource));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }

    public function update(ExpenseRequest $request, Expense $expense)
    {
        $this->checkOwnership($expense);
        try {
            DB::beginTransaction();
            $expense->update($request->validated());
            DB::commit();
            return $this->success(new ExpenseResource($expense), 'Expense updated Successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }

    public function destroy(Expense $expense)
    {
        $this->checkOwnership($expense);
        try {
            DB::beginTransaction();
            $expense->delete();
            DB::commit();
            return $this->success('Expense deleted Successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }
}

