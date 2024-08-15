<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Http\Requests\LoanCategoryRequest;
use App\Models\LoanCategory;

class LoanCategoryController extends BaseCategoryController
{
    protected $loanCategory;

    public function __construct(LoanCategory $loanCategory)
    {
        $this->loanCategory = $loanCategory;
    }

    public function index(FilterRequest $request)
    {
        return $this->allResource($request, $this->loanCategory);
    }
    
    public function store(LoanCategoryRequest $request)
    {
        $validatedData = $request->validated();
        return $this->createResource($validatedData, $this->loanCategory);
    }

    public function show($id)
    {
        return $this->specificResource($this->loanCategory, $id);
    }

    public function update(LoanCategoryRequest $request, $id)
    {
        $resource = $this->loanCategory->find($id);
        if (!$resource) {
            return $this->notFound();
        }
        $validatedData = $request->validated();
        return $this->updateResource($validatedData, $resource);
    }

    public function destroy($id)
    {
        $resource = $this->loanCategory->find($id);
        if (!$resource) {
            return $this->notFound();
        }
        return $this->deleteResource($resource);
    }
}
