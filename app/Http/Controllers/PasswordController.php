<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\JWTAuth;
use App\Mail\OtpMail;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PasswordController extends Controller
{
    /**
     * Show the forgot email form view
     */
    public function otpSend(){
        // return view('components.password.send-otp');
    }

    /**
     * Otp verify for reset password
     */
    public function otpVerifyView(){
        // return view('components.password.otp-verify');
    }

    /**
     * New password view
     */
    public function newPassword(){
        // return view('components.password.password');
    }


    /**
     * sending the new otp for reset password
     */
    public function sendOTP(Request $request){
        if(!empty($request->input('email'))){
            try {
                $user = User::where('email',$request->input('email'))->first();
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
                ])->cookie('password_otp',$otpToken,time()+3600,'/');
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
                $token = JWTAuth::verifyToken('password_otp');
                $passToken = JWTAuth::createToken('password_token',.5,null,$token->email);
                $user = User::where('email',$token->email)->first();

                if( $request->input('otp') == $user->otp){
                    User::where('id', $user->id)->update(['otp' => '']);
                    setcookie('password_otp','',time()-3600,'/');
                    return response()->json([
                        'status' => 'success',
                        'message' => 'OTP match, Go for next',
                    ],200)->cookie('password_token',$passToken,time()+3600,'/');
                }else{
                    return response()->json([
                        'status' => 'fail',
                        'message' => 'Invalid OTP',
                        'user' => $user
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
            try {
                $token = JWTAuth::verifyToken('password_token');
                $user = User::where('email',$token->email)->first();
                $user->update([
                    'password' => password_hash($new,PASSWORD_DEFAULT)
                ]);
                setcookie('password_token','',time()-3600,'/');
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
        }else{
            return response()->json([
                'status' => 'fail',
                'message' => 'Both password must be same'
            ],400);
        }
    }
}
