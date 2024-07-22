<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UserController extends BaseController
{
    protected $user;

    public function __construct(User $user)
    {
        $this -> user = $user;
    }

    public function register(UserRequest $request) {
        try {
            DB::beginTransaction();
            $user = $this->user->create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            DB::commit();
            return $this->success('User created successfully', $user, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('An error occurred: ' . $e->getMessage());
        }
    }
}
