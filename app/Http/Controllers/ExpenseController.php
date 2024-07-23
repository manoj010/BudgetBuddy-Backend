<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ExpenseController extends BaseController
{
    protected $expense;

    public function __construct(Expense $expense)
    {
        $this->expense = $expense;
    }

    public function store(ExpenseRequest $request)
    {
        try {
            DB::beginTransaction();
            $validatedData = $request->validated();
            $expense = $this->expense::create($validatedData);
            DB::commit();
            return $this->success(new ExpenseResource($expense), 'Expense created successfully', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }
}
