<?php

namespace App\Http\Controllers;

use App\Http\Requests\SavingCategoryRequest;
use App\Models\SavingCategory;

class SavingCategoryController extends BaseCategoryController
{
    protected $savingCategory;

    public function __construct(SavingCategory $savingCategory)
    {
        $this->savingCategory = $savingCategory;
    }

    public function index()
    {
        return $this->allResource($this->savingCategory);
    }
    
    public function store(SavingCategoryRequest $request)
    {
        $validatedData = $request->validated();
        return $this->createResource($validatedData, $this->savingCategory);
    }

    public function show($id)
    {
        return $this->specificResource($this->savingCategory, $id);
    }

    public function update(SavingCategoryRequest $request, $id)
    {
        $resource = $this->savingCategory->find($id);
        if (!$resource) {
            return $this->notFound();
        }
        $validatedData = $request->validated();
        return $this->updateResource($validatedData, $resource);
    }

    public function destroy($id)
    {
        $resource = $this->savingCategory->find($id);
        if (!$resource) {
            return $this->notFound();
        }
        return $this->deleteResource($resource);
    }
}
