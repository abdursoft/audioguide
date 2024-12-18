<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\JWTAuth;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PasswordController extends Controller
{
    /**
     * sending the new otp for reset password
     */
    public function sendOTP(Request $request){
        if(!empty($request->input('email'))){
            try {
                $user = User::where('email',$request->input('email'))->first();
                if($user->role == 'business'){
                    return response()->json([
                        'status' => false,
                        'message' => "Demo users couldn't change their password, Please contact with admin",
                    ],400);
                }
                $otp = rand(1000,9999);
                $otpToken = JWTAuth::createToken('password_otp',.5,null,$request->input('email'));
                $user->update([
                    'otp' => $otp
                ]);
                Mail::to($request->email)->send(new OtpMail($otp));
                return response()->json([
                    'status' => 'success',
                    'message' => 'OTP has been successfully sent',
                    'token' => $otpToken
                ]);
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'This email is not registered'
                ],400);
            }
        }else{
            return response()->json([
                'status' => 'fail',
                'message' => 'Email is a required field'
            ],400);
        }
    }

    /**
     * Verify otp and reset password for token
     */
    public function verifyOTP(Request $request){
        if(!empty($request->input('otp'))){
            try {
                $token = JWTAuth::verifyToken($request->input('otp_token'),false);
                $passToken = JWTAuth::createToken('password_token',.5,null,$token->email);
                $user = User::where('email',$token->email)->first();
                if( $request->input('otp') == $user->otp){
                    User::where('id', $user->id)->update(['otp' => '']);
                    return response()->json([
                        'status' => 'success',
                        'message' => 'OTP match, Go for next',
                        "password_token" => $passToken
                    ],200);
                }else{
                    return response()->json([
                        'status' => 'fail',
                        'message' => 'Invalid OTP',
                    ],400);
                }
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Your OTP token was expired',
                    'error' => $th->getMessage(),
                ],400);
            }
        }else{
            return response()->json([
                'status' => 'fail',
                'message' => 'Invalid OTP is required'
            ],400);
        }
    }

    /**
     * Changing the new password
     */
    public function passwordReset(Request $request){
        $new = $request->input('new_password');
        $con = $request->input('confirm_password');

        if($new == $con){
            if(strlen($new) < 5){
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Password length must be 5 or more characters'
                ],400);
            }else{
                try {
                    $token = JWTAuth::verifyToken($request->input('password_token'),false);
                    $user = User::where('email',$token->email)->first();
                    $user->update([
                        'password' => password_hash($new,PASSWORD_DEFAULT)
                    ]);
                    return response()->json([
                        'status' => 'success',
                        'message' => "Password successfully reset"
                    ],200);
                } catch (\Throwable $th) {
                    return response()->json([
                        'status' => 'fail',
                        'message' => 'Authorization or Server Error!'
                    ],400);
                }
            }
        }else{
            return response()->json([
                'status' => 'fail',
                'message' => 'Both password must be same'
            ],400);
        }
    }
}
