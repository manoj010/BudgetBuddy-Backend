<?php

namespace App\Http\Controllers;

use App\Http\Requests\SavingGoalRequest;
use App\Http\Resources\SavingGoalCollection;
use App\Http\Resources\SavingGoalResource;
use App\Models\SavingGoal;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SavingGoalController extends BaseController
{
    protected $savingGoal;

    public function __construct(SavingGoal $savingGoal)
    {
        $this->savingGoal = $savingGoal;
    }

    public function index()
    {
        $savingGoal = $this->savingGoal->where('created_by', auth()->id())->get();
        return $this->success(new SavingGoalCollection($savingGoal), 'All Saving');
    }

    public function store(SavingGoalRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $savingGoal = SavingGoal::create($validatedData);
            return $this->success(new SavingGoalResource($savingGoal), 'Saving Goal Added Successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(SavingGoalRequest $request, SavingGoal $savingGoal)
    {
        $this->checkOwnership($savingGoal);

        try {
            DB::beginTransaction();
            $request->validated();
            $savingGoal->update($request->validated());
            DB::commit();
            return $this->success(new SavingGoalResource($savingGoal), 'Goal updated Successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }

    public function destroy(SavingGoal $savingGoal)
    {
        $this->checkOwnership($savingGoal);

        try {
            DB::beginTransaction();
            $savingGoal->delete();
            DB::commit();
            return $this->success('Goal Deleted Successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }
}
