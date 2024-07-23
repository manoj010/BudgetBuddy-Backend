<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseCategoryCollection;
use App\Http\Resources\BaseCategoryResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class BaseCategoryController extends BaseController
{
    protected function createResource(array $validatedData, Model $resource)
    {
        try {
            DB::beginTransaction();
            $createdResource = $resource->create($validatedData);
            DB::commit();
            return $this->success(new BaseCategoryResource($createdResource), 'Category Created Successfully', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }

    protected function allResource(Model $resource)
    {
        $userId = auth()->id();
        $this->checkOrFindResource($resource);
        $allResource = $resource->where('created_by', $userId)->get();
        return $this->success(new BaseCategoryCollection($allResource), 'All Category Data', Response::HTTP_OK);
    }

    protected function specificResource(Model $resource, $id)
    {
        $userId = auth()->id();
        try {
            DB::beginTransaction();
            $this->checkOrFindResource($resource, $id);
            $specificResource = $resource->where('created_by', $userId)->find($id);
            DB::commit();
            return $this->success(new BaseCategoryResource($specificResource), 'Category Data', Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }

    protected function updateResource(array $validatedData, Model $resource)
    {
        try {
            DB::beginTransaction();
            $this->checkOwnership($resource);
            $resource->update($validatedData);
            $updatedResource = $resource->fresh();
            DB::commit();
            return $this->success(new BaseCategoryResource($updatedResource), 'Category Updated Successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }

    protected function deleteResource(Model $resource)
    {
        try {
            DB::beginTransaction();
            $this->checkOwnership($resource);
            $resource->delete();
            DB::commit();
            return $this->success(' ', 'Category deleted Successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }
}
