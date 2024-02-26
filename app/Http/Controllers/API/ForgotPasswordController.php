<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\SendOTP;
use App\Models\OneTimePassword;
use App\Models\User;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validated) {
            $otp = rand(100000,999999);
            $user = User::where('email',$request->email)->first();
            $makeOtp = OneTimePassword::updateOrCreate(
                [
                    'email' => $user->email
                ],
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'otp' => $otp,
                ]
            );
            $data = [
                'title' => 'Reset Password OTP',
                'body' => "Your OTP Code: {$otp}"
            ];
            try {
                Mail::to($user->email)->send(new SendOTP($data));
                return response()->json([
                    'status' => true,
                    'message' => 'OTP Send Successfully',
                    'data' => $makeOtp
                ]);
            } catch (Error $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'error',
                    'error' => $e
                ]);
            }
        }
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required',
            'email' => 'required|exists:users,email',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|min:6' 
        ]);

        if ($validated) {
            if ($request->new_password == $request->confirm_password) {
                $user = User::where('id', $request->user_id)->orWhere('email', $request->email)->first();
                if ($user) {
                    $user->update(['password' => Hash::make($request->new_password)]);
                    return response()->json([
                        'status' => true,
                        'message' => 'Password reset successfully',
                        'data' => $user
                    ]);
                }

                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Password not match',
            ]);
        }
    }
}
