<?php

namespace App\Http\Controllers;

use App\Http\Requests\SavingRequest;
use App\Http\Resources\{SavingCollection, SavingResource};
use Symfony\Component\HttpFoundation\Response;
use App\Models\{Saving, UserBalance};
use Illuminate\Support\Facades\DB;

class SavingController extends BaseController
{
    protected $saving;

    public function __construct(Saving $saving)
    {
        $this->saving = $saving;
    }

    public function index()
    {
        $saving = $this->saving->where('created_by', auth()->id())->get();
        return $this->success(new SavingCollection($saving), 'All Saving');
    }

    public function store(SavingRequest $request)
    { 
        try {
            DB::beginTransaction();
            $validatedData = $request->validated();

            $balance = UserBalance::where('created_by', auth()->id())->firstOrNew();
        
            if ($balance->balance < $validatedData['amount']) {
                DB::rollBack();
                return $this->error('Insufficient balance to save this amount.', Response::HTTP_BAD_REQUEST);
            }

            $save = $this->saving::create($validatedData);
            $balance = UserBalance::firstOrNew();
            $balance->total_saving += $save->amount;
            $balance->balance -= $save->amount;
            $balance->save();
            DB::commit();
            return $this->success(new SavingResource($save), 'Amount Saved', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }
}
