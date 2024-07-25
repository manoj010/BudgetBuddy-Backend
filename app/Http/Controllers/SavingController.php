<?php

namespace App\Http\Controllers;

use App\Http\Requests\SavingRequest;
use App\Http\Resources\SavingCollection;
use App\Http\Resources\SavingResource;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Saving;
use App\Models\UserBalance;
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
            $validatedData['type'] = 'save';
            $type = $validatedData['type'];
            dd($validatedData);
            $save = $this->saving::create($validatedData);
            $this->updateUserBalance($type, $validatedData['amount']);
            DB::commit();
            return $this->success(new SavingResource($save), 'Amount Saved', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }

    protected function updateUserBalance($type, $amount)
    {
        $userBalance = UserBalance::where('created_by', auth()->id())->first();
        if (!$userBalance) {
            throw new \Exception('User balance not found.');
        }
        switch ($type) {
            case 'saving':
                $userBalance->balance += $amount;
                break;
            case 'withdraw':
                $userBalance->balance -= $amount;
                break;
            default:
                throw new \Exception('Invalid transaction type.');
        }
        $userBalance->save();
    }
}
