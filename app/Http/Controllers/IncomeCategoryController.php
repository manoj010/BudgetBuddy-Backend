<?php

namespace App\Http\Controllers;

use App\Http\Requests\IncomeCategoryRequest;
use App\Models\IncomeCategory;

class IncomeCategoryController extends BaseCategoryController
{
    protected $incomeCategory;

    public function __construct(IncomeCategory $incomeCategory)
    {
        $this->incomeCategory = $incomeCategory;
    }

    public function index()
    {
        return $this->allResource($this->incomeCategory);
    }
    
    public function store(IncomeCategoryRequest $request)
    {
        $validatedData = $request->validated();
        return $this->createResource($validatedData, $this->incomeCategory);
    }

    public function show($id)
    {
        return $this->specificResource($this->incomeCategory, $id);
    }

    public function update(IncomeCategoryRequest $request, $id)
    {
        $resource = $this->incomeCategory->find($id);
        if (!$resource) {
            return $this->notFound();
        }
        $validatedData = $request->validated();
        return $this->updateResource($validatedData, $resource);
    }

    public function destroy($id)
    {
        $resource = $this->incomeCategory->find($id);
        if (!$resource) {
            return $this->notFound();
        }
        return $this->deleteResource($resource);
    }
}
