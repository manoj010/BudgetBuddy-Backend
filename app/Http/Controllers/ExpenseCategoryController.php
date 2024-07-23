<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseCategoryRequest;
use App\Models\ExpenseCategory;

class ExpenseCategoryController extends BaseCategoryController
{
    protected $expenseCategory;

    public function __construct(ExpenseCategory $expenseCategory)
    {
        $this->expenseCategory = $expenseCategory;
    }

    public function index()
    {
        return $this->allResource($this->expenseCategory);
    }
    
    public function store(ExpenseCategoryRequest $request)
    {
        $validatedData = $request->validated();
        return $this->createResource($validatedData, $this->expenseCategory);
    }

    public function show($id)
    {
        return $this->specificResource($this->expenseCategory, $id);
    }

    public function update(ExpenseCategoryRequest $request, $id)
    {
        $resource = $this->expenseCategory->find($id);
        if (!$resource) {
            return $this->notFound();
        }
        $validatedData = $request->validated();
        return $this->updateResource($validatedData, $resource);
    }

    public function destroy($id)
    {
        $resource = $this->expenseCategory->find($id);
        if (!$resource) {
            return $this->notFound();
        }
        return $this->deleteResource($resource);
    }
}
