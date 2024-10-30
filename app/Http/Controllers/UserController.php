<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\JWTAuth;
use App\Mail\LoginOTP;
use App\Models\AudioHistory;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\ProductCart;
use App\Models\ProductReview;
use App\Models\ProductWish;
use App\Models\Profile;
use App\Models\Update;
use App\Models\User;
use App\Models\UserBilling;
use App\Models\UserGuide;
use App\Models\UserShipping;
use App\Models\UserSubscription;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
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
            $token = rand(1000, 9999);
            $user = User::create(
                [
                    "name" => $request->input('name'),
                    "email" => $request->input('email'),
                    "otp" => $token,
                    "password" => password_hash($request->input('password'), PASSWORD_DEFAULT)
                ]
            );
            Mail::to($request->input('email'))->send(new LoginOTP($token, "Use the OTP to verify your account"));

            DB::commit();
            return response([
                'status' => true,
                'message' => 'A verification Code has been sent to your email',
            ], 201)->cookie('otp_email', $request->input('email'), time() + 3600, '/', null, false, true, false, null);
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
        try {
            $user = User::find($request->header('id'));
            User::where('id', $request->header('id'))->update([
                'name' => $request->input('name') ?? $user->name,
                'email' => $request->input('email') ?? $user->email,
                'password' => password_hash($request->input('password'), PASSWORD_DEFAULT) ?? $user->password,
                'image' => Storage::disk('public')->put("uploads/profile", $request->file('image')) ?? $user->image
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Profile successfully updated'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Profile couldn\'t update',
            ], 400);
        }
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
    public function setupVerification($token)
    {
        try {
            $token = JWTAuth::verifyToken($token, false);
            $user = User::where('email', $token->email)->first();
            if ($user->is_verified) {
                return redirect('/user/login')->with(true, "Account already verified");
            } else {
                User::where('email', $token->email)->update([
                    'is_verified' => 1,
                    'otp' => null
                ]);
                return redirect('/user/login')->with(true, "Account successfully verified");
            }
        } catch (Exception $e) {
            return redirect('/user/login')->with('error', "Account couldn\'t verified");
        }
    }

    /** 
     * Verifying signup otp
     */
    public function verifySignupOTP(Request $request)
    {
        // return $request->cookie('otp_email');
        try {
            $user = User::where('email', $request->cookie('otp_email'))->first();
            if ($request->input() != '' && $user->otp == $request->input('otp')) {
                User::where('id', $user->id)->update([
                    'otp' => '',
                    'is_verified' => 1
                ]);
                return response()->json(
                    [
                        'status' => true,
                        'message' => "Account successfully verified",
                    ],
                    200
                )->cookie('otp_email', '', -40, '/');
            } else {
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
        if ($user) {
            if (password_verify($request->input('password'), $user->password)) {
                if (!$user->is_verified) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Your account is not verified'
                    ], 400);
                } elseif ($user->is_blocked) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Your account has been blocked'
                    ], 400);
                } else {
                    $token = JWTAuth::createToken($user->role, 1, $user->id, $user->email);
                    return response()->json([
                        'status' => true,
                        'message' => 'Login successful',
                        'token_type' => 'Bearer',
                        'token' => $token
                    ], 200)->cookie($user->role . "_token", $token, +3600, '/', null, false, true, false, null);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Incorrect password'
                ], 400);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Login failed'
            ], 400);
        }
    }

    public function adminAuth()
    {
        return response()->json([
            'status' => true,
            'message' => 'Authenticated'
        ], 200);
    }

    public function logOut(Request $request)
    {
        $user = User::find($request->header('id'));
        return response()->json([
            'status' => true,
            'message' => 'Logout successful'
        ], 200)->cookie($user->role . "_token", '', -3600, '/', null, false, true, false, null);
    }

    public function webLogOut(Request $request)
    {
        $request->cookie('token', '', -1, '/');
        $request->headers->set('token', null);
        return redirect('/user/login')->with(true, 'You have successfully logout');
    }

    public function dashboard()
    {
        try {
            JWTAuth::verifyToken('token');
            return view('pages.users.dashboard');
        } catch (\Throwable $th) {
            return redirect('/user/login');
        }
    }

    public function delete(Request $request)
    {
        $user = User::find($request->header('id'));
        if (password_verify($request->input('password'), $user->password)) {
            Profile::where('user_id', $request->header('id'))->delete();
            ProductCart::where('user_id', $request->header('id'))->delete();
            ProductWish::where('user_id', $request->header('id'))->delete();
            UserGuide::where('user_id', $request->header('id'))->delete();
            UserBilling::where('user_id', $request->header('id'))->delete();
            UserShipping::where('user_id', $request->header('id'))->delete();
            InvoiceProduct::where('user_id', $request->header('id'))->delete();
            Invoice::where('user_id', $request->header('id'))->delete();
            AudioHistory::where('user_id', $request->header('id'))->delete();
            ProductReview::where('user_id', $request->header('id'))->delete();
            UserSubscription::where('user_id', $request->header('id'))->delete();
            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'Account has been deleted'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 400);
        }
    }

    public function profileData(Request $request)
    {
        try {
            $user = User::with('Profile', 'ProductCart', 'ProductCart.AudioGuide', 'ProductWish', 'ProductWish.AudioGuide', 'UserGuide', 'UserGuide.AudioGuide', 'UserSubscription','UserSubscription.Subscription')->find($request->header('id'));
            return response()->json([
                'status' => true,
                'data' => $user
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
