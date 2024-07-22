<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends BaseController
{
    public function login(AuthRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $tokenExpiration = Carbon::now()->addHours(1);
            Passport::personalAccessTokensExpireIn($tokenExpiration);
            $token = $user->createToken('userToken')->accessToken;
            $expirationTimestamp = $tokenExpiration->timestamp;

            return response()->json([
                'code' => Response::HTTP_OK,
                'expires_at' => $expirationTimestamp,
                'token' => $token,
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => Response::HTTP_UNAUTHORIZED,
            'message' => "Invalid Credentials.",
        ], Response::HTTP_UNAUTHORIZED);
    }
}
