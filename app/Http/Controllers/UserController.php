<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\JWTAuth;
use App\Mail\LoginOTP;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return view("pages.login");
    }


    /**
     * Display a listing of the resource.
     */
    public function otpPage()
    {
        // return view("pages.signup-otp");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // return view("pages.register");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string",
            "email" => "required|email|unique:users,email",
            "password" => "required|string|min:6"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'User registration failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            DB::beginTransaction();
            $token = rand(1000,9999);
            $user = User::create(
                [
                    "name" => $request->input('name'),
                    "email" => $request->input('email'),
                    "otp" => $token,
                    "password" => password_hash($request->input('password'), PASSWORD_DEFAULT)
                ]
            );
            Mail::to($request->input('email'))->send(new LoginOTP($token,"Use the OTP to verify your account"));

            DB::commit();
            return response([
                'status' => true,
                'message' => 'A verification Code has been sent to your email',
            ], 201)->cookie('otp_email',$request->input('email'),time()+3600,'/',null,false,true,false,null);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => "fail",
                'message' => 'User registration failed',
                'errors' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
    }

    /**
     * setup verification to the user
     */
    public function setupVerification($token){
        try{
            $token = JWTAuth::verifyToken($token,false);
            $user = User::where('email',$token->email)->first();
            if($user->is_verified){
                return redirect('/user/login')->with(true,"Account already verified");
            }else{
                User::where('email',$token->email)->update([
                    'is_verified' => 1,
                    'otp' => null
                ]);
                return redirect('/user/login')->with(true,"Account successfully verified");
            }
        }catch(Exception $e){
            return redirect('/user/login')->with('error',"Account couldn\'t verified");
        }

    }

    /** 
     * Verifying signup otp
     */
    public function verifySignupOTP(Request $request){
        // return $request->cookie('otp_email');
        try {
            $user = User::where('email',$request->cookie('otp_email'))->first();
            if($request->input() != '' && $user->otp == $request->input('otp')){
                User::where('id',$user->id)->update([
                    'otp' => '',
                    'is_verified' => 1
                ]);
                return response()->json(
                    [
                        'status' => true,
                        'message' => "Account successfully verified",
                    ],
                    200
                )->cookie('otp_email','',-40,'/'); 
            }else{
                return response()->json(
                    [
                        'status' => false,
                        'message' => "OTP not matched"
                    ],
                    400
                );
            }
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Authentication fail",
                    "error" => $th->getMessage()
                ],
                401
            );
        }
    }


    /**
     * Login the user with jwt token
     */
    public function login(Request $request)
    {
        $validator = Validator(
            $request->all(),
            [
                'email' => 'required|email|exists:users,email',
                "password" => 'required'
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Authentication fail",
                    "errors" => $validator->errors()
                ],
                401
            );
        }

        $user = User::where('email', $request->input('email'))->first();
        if($user){
            if(password_verify($request->input('password'), $user->password)){
                if(!$user->is_verified){
                    return response()->json([
                        'status' => false,
                        'message' => 'Your account is not verified'
                    ],400);
                }elseif($user->is_blocked){
                    return response()->json([
                        'status' => false,
                        'message' => 'Your account has been blocked'
                    ],400);
                }else{
                    $token = JWTAuth::createToken($user->role,1,$user->id,$user->email);
                    return response()->json([
                        'status' => true,
                        'message' => 'Login successful',
                        'token_type' => 'Bearer',
                        'token' => $token
                    ],200)->cookie($user->role."_token",$token,+3600,'/',null,false,true,false,null);
                }
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Incorrect password'
                ],400);
            }
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Login failed'
            ],400);
        }
    }

    public function adminAuth(){
        return response()->json([
            'status' => true,
            'message' => 'Admin authenticated'
        ],200);
    }

    public function logOut(Request $request){
        $request->cookie('token','',-1,'/');
        $request->headers->set('token' , null);
        return response()->json([
            'status' => true,
            'message' => 'Logout successful'
        ],200);
    }

    public function webLogOut(Request $request){
        $request->cookie('token','',-1,'/');
        $request->headers->set('token' , null);
        return redirect('/user/login')->with(true,'You have successfully logout');
    }

    public function dashboard(){
        try {
            JWTAuth::verifyToken('token');
            return view('pages.users.dashboard');
        } catch (\Throwable $th) {
           return redirect('/user/login');
        }
    }
}
