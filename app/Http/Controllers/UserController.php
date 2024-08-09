<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UserProfileRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\{Auth, DB, Hash};
use Symfony\Component\HttpFoundation\Response;

class UserController extends BaseController
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function register(UserRequest $request)
    {
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

    public function index()
    {
        $user = Auth::guard('api')->user();
        return $this->success(new UserResource($user), 'User data retrieved successfully');
    }

    public function save(UserProfileRequest $request)
    {
        try {
            DB::beginTransaction();
            $validatedData = $request->validated();
            $user = Auth::guard('api')->user();
            $user->update($validatedData);
            DB::commit();
            return $this->success(new UserResource($user), 'User data updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('An error occurred: ' . $e->getMessage());
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    { 
        try {
            $user = Auth::guard('api')->user();
            DB::beginTransaction();
            if (!Hash::check($request->current_password, $user->password)) {
                return $this->error('Current password is incorrect', Response::HTTP_UNAUTHORIZED);
            }
            $user->password = bcrypt($request->new_password);
            $user->save();
            DB::commit();
            return $this->success('', 'Password updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('An error occurred: ' . $e->getMessage());
        }
    }
}
