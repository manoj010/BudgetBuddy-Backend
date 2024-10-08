<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Http\Requests\SavingRequest;
use App\Http\Resources\{SavingCollection, SavingResource};
use App\Models\Saving;
use App\Services\BalanceService;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SavingController extends BaseController
{
    protected $saving;
    protected $balanceService;

    public function __construct(Saving $saving, BalanceService $balanceService)
    {
        $this->saving = $saving;
        $this->balanceService = $balanceService;
    }
    
    public function index(FilterRequest $request)
    {
        $saving = $this->getFilteredDate($request, $this->saving);
        return $this->success(new SavingCollection($saving), 'All Saving');
    }

    public function store(SavingRequest $request)
    { 
        try {
            DB::beginTransaction();
            $validatedData = $request->validated();

            if (!$this->balanceService->checkIfNewMonthBalanceCreated(auth()->id())) {
                $this->balanceService->createNewMonthlyBalance(auth()->id());
            }

            $balance = $this->balanceService->getOrCreateMonthlyBalance(auth()->id());

            if ($balance->closing_balance < $validatedData['amount']) {
                DB::rollBack();
                return $this->error('Insufficient balance to save this amount', Response::HTTP_BAD_REQUEST);
            }

            $save = $this->saving::create($validatedData);

            $balance->saving_balance += $save->amount;
            $balance->total_saving += $save->amount;
            $balance->closing_balance -= $save->amount;
            $balance->save();

            DB::commit();
            return $this->success(new SavingResource($save), 'Amount Saved', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $specificResource = $this->saving->where('created_by', auth()->id())->findOrFail($id);
            return $this->success(new SavingResource($specificResource));
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function update(SavingRequest $request, Saving $saving)
    {
        $this->checkOwnership($saving);

        if ($response = $this->checkMonth($saving)) {
            return $response;
        }

        try {
            DB::beginTransaction();
            $prevAmount = $saving->amount;
            $newAmount = $request->validated()['amount'];
            $amountDifference = $newAmount - $prevAmount;

            $saving->update($request->validated());

            $balance = $this->balanceService->getOrCreateMonthlyBalance(auth()->id());
            $balance->total_saving += $amountDifference;
            $balance->closing_balance -= $amountDifference;
            $balance->save();

            DB::commit();
            return $this->success(new SavingResource($saving), 'Saving updated Successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }

    public function destroy(Saving $saving)
    {
        $this->checkOwnership($saving);

        if ($response = $this->checkMonth($saving)) {
            return $response;
        }
        
        try {
            DB::beginTransaction();

            $balance = $this->balanceService->getOrCreateMonthlyBalance(auth()->id());
            $balance->total_saving -= $saving->amount;
            $balance->closing_balance += $saving->amount;
            $balance->save();

            $saving->delete();

            DB::commit();
            return $this->success('', 'Saving deleted Successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }
}
