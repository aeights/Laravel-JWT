<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $registerRequest)
    {
        $validated = $registerRequest->validated();

        if ($validated) {
            $user = User::create([
                'nik' => $registerRequest->nik,
                'name' => $registerRequest->name,
                'email' => $registerRequest->email,
                'password' => $registerRequest->password,
                'phone' => $registerRequest->phone,
            ]);

            return response()->json([
                "status" => true,
                "message" => "User registered successfully"
            ]);
        }

        return response()->json([
            "status" => false,
            "message" => "error"
        ]);
    }

    public function login(LoginRequest $loginRequest)
    {
        $validated = $loginRequest->validated();

        if ($validated) {
            $token = JWTAuth::attempt([
                "email" => $loginRequest->email,
                "password" => $loginRequest->password
            ]);

            if(!empty($token)){
                return response()->json([
                    "status" => true,
                    "message" => "User logged in succcessfully",
                    "token" => $token
                ]);
            }

            return response()->json([
                "status" => false,
                "message" => "Invalid email or password",
            ]);
        }

        return response()->json([
            "status" => false,
            "message" => "error"
        ]);
    }

    public function profile()
    {
        $user = auth()->user();

        return response()->json([
            "status" => true,
            "message" => "Profile data",
            "data" => $user
        ]);
    }
    
    public function refreshToken()
    {
        $newToken = auth()->refresh();

        return response()->json([
            "status" => true,
            "message" => "New access token generate successfully",
            "token" => $newToken
        ]);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json([
            "status" => true,
            "message" => "User logged out successfully"
        ]);
    }
}
