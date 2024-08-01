<?php

namespace App\Http\Controllers;

use App\Helpers\functions;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\{Auth, DB};
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
            Auth::login($user);
            $this->createDefaultCategories();
            DB::commit();
            return $this->success($user, 'User created successfully', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('An error occurred: ' . $e->getMessage());
        }
    }
}
