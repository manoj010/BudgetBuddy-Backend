<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Http\Requests\FilterRequest;
use App\Http\Resources\{ExpenseCollection, ExpenseResource};
use App\Models\Expense;
use App\Services\BalanceService;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ExpenseController extends BaseController
{
    protected $expense;
    protected $balanceService;

    public function __construct(Expense $expense, BalanceService $balanceService)
    {
        $this->expense = $expense;
        $this->balanceService = $balanceService;
    }
    
    public function index(FilterRequest $request)
    {
        $expense = $this->getFilteredDate($request, $this->expense);
        return $this->success(new ExpenseCollection($expense), 'All Expense');
    }

    public function store(ExpenseRequest $request)
    {
        try {
            DB::beginTransaction();
            $validatedData = $request->validated();

            if(!$validatedData['date_spent'] || $validatedData['date_spent'] === null) {
                $validatedData['date_spent'] = now()->format('Y-m-d');
            }
            
            $expense = $this->expense::create($validatedData);

            if (!$this->balanceService->checkIfNewMonthBalanceCreated(auth()->id())) {
                $this->balanceService->createNewMonthlyBalance(auth()->id());
            }

            $balance = $this->balanceService->getOrCreateMonthlyBalance(auth()->id());
            $balance->total_expense += $expense->amount;
            $balance->closing_balance -= $expense->amount;
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
            $specificResource = $this->expense->where('created_by', auth()->id())->findOrFail($id);
            return $this->success(new ExpenseResource($specificResource));
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function update(ExpenseRequest $request, Expense $expense)
    {
        $this->checkOwnership($expense);

        if ($response = $this->checkMonth($expense)) {
            return $response;
        }

        try {
            DB::beginTransaction();
            $prevAmount = $expense->amount;
            $newAmount = $request->validated()['amount'];
            $amountDifference = $newAmount - $prevAmount;

            $expense->update($request->validated());

            $balance = $this->balanceService->getOrCreateMonthlyBalance(auth()->id());
            $balance->total_expense += $amountDifference;
            $balance->closing_balance -= $amountDifference;
            $balance->save();

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
        
        if ($response = $this->checkMonth($expense)) {
            return $response;
        }

        try {
            DB::beginTransaction();

            $balance = $this->balanceService->getOrCreateMonthlyBalance(auth()->id());
            $balance->total_expense -= $expense->amount;
            $balance->closing_balance += $expense->amount;
            $balance->save();

            $expense->delete();

            DB::commit();
            return $this->success('Expense deleted Successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }
}
