<?php

namespace App\Http\Controllers;

use App\Http\Requests\SavingGoalRequest;
use App\Http\Resources\SavingGoalResource;
use App\Models\SavingGoal;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SavingGoalController extends BaseController
{
    public function store(SavingGoalRequest $request)
    {
        try {
            $validatedData = $request->validated();

            // Check if a saving goal for the current month already exists
            $existingGoal = SavingGoal::whereDate('for_month', $validatedData['for_month'])->first();

            if ($existingGoal) {
                return $this->error('Saving Goal Already Exists', Response::HTTP_CONFLICT);
            }

            // Create the new saving goal
            $savingGoal = SavingGoal::create($validatedData);

            return $this->success(new SavingGoalResource($savingGoal), 'Saving Goal Added Successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
