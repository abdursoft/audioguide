<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\JWTAuth;
use App\Mail\LoginOTP;
use App\Mail\Message;
use App\Models\AudioGuide;
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
use Jenssegers\Agent\Facades\Agent;

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
            "email" => "required|email",
            "password" => "required|string|min:6"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'User registration failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $exists = User::where('email', $request->input('email'))->first();
        if ($exists && !$exists->is_verified) {
            $token = rand(1000, 9999);
            $user = User::where('email', $exists->email)->update(
                [
                    "otp" => $token,
                ]
            );
            Mail::to($request->input('email'))->send(new LoginOTP($token, "Use the OTP to verify your account"));
            $token = JWTAuth::createToken('otp_email', .5, null, $request->input('email'));
            return response()->json([
                'status' => false,
                'message' => 'A verification code has been sent to your email',
                'is_verified' => false,
                'otp_email' => $token
            ], 400);
        } elseif ($exists && $exists->is_verified) {
            return response()->json([
                'status' => false,
                'message' => 'This email already exist, Please login',
            ], 200);
        } else {
            true;
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
            $token = JWTAuth::createToken('otp_email', .5, null, $request->input('email'));
            DB::commit();
            return response([
                'status' => true,
                'message' => 'A verification Code has been sent to your email',
                'otp_email' => $token
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
     * Profile image upload
     */
    public function profileImage(Request $request)
    {
        if ($request->hasFile('image')) {
            try {
                $user = User::find($request->header('id'));
                User::where('id', $request->header('id'))->update([
                    'image' => Storage::disk('public')->put("uploads/profile", $request->file('image')) ?? $user->image
                ]);
                if (!empty($user->image)) {
                    Storage::disk('public')->delete($user->image);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'Profile image successfully updated'
                ], 200);
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => false,
                    'message' => 'Profile image couldn\'t update',
                    'errors' => $th->getMessage()
                ], 400);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Please provide an image with a post request',
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
        $token = JWTAuth::verifyToken($request->input('otp_email'), false);
        try {
            $user = User::where('email', $token->email)->first();
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
                );
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
            $browser = Agent::browser();
            $os = Agent::platform();
            if ($user->role === 'admin' && !password_verify($request->input('password'), $user->password)) {
                Mail::to($user->email)->send(new Message("Anonymous admin login and password failed",$os,$browser,$request->server('REMOTE_ADDR')));
                return response()->json([
                    'status' => false,
                    'message' => 'Incorrect password'
                ], 400);
            }elseif (password_verify($request->input('password'), $user->password)) {
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
                    if($user->role === 'admin'){
                        Mail::to($user->email)->send(new Message("Admin login successful",$os,$browser,$request->server('REMOTE_ADDR')));
                    }
                    $token = JWTAuth::createToken($user->role, 8740, $user->id, $user->email);
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
            $user = User::with('Profile', 'ProductCart', 'ProductCart.AudioGuide', 'ProductWish', 'ProductWish.AudioGuide','UserSubscription')->find($request->header('id'));
            $billing = UserSubscription::where('user_id', $request->header('id'))->get();
            $guides = [];
            $yearly = $onetime = $lifetime = false;
            $active_plan = "Free membership";
            $active_start = "";
            $active_end = '';
            foreach ($billing as $bill) {
                if ($bill->type === 'onetime') {
                    $onetime = true;
                    $guides[] = AudioGuide::find($bill->guide_id);
                    if($bill->status === 'paid' || $bill->status === 'complete' || $bill->status === 'active'){
                        $active_plan = "Single guide subscription";
                    }
                }if ($bill->type === 'autorenew') {
                    $yearly = true;
                    if($bill->status === 'paid' || $bill->status === 'complete' || $bill->status === 'active'){
                        $active_plan = "Yearly subscription";
                    }
                } else {
                    $lifetime = true;
                    if($bill->status === 'paid' || $bill->status === 'complete' || $bill->status === 'active'){
                        $active_plan = "Lifetime subscription";
                    }
                }
                $active_start = $bill->started_at;
                $active_end = $bill->ended_at;
            }
            return response()->json([
                'status' => true,
                'data' => $user,
                'guides' => $guides,
                'active_plan' => [
                    "name" => $active_plan,
                    "start" => $active_start,
                    "end" => $active_end
                ]
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }


    /**
     * User subscription type
     */
    public function subscriptionStatus($id){
        $billing = UserSubscription::where('user_id', $id)->where('ended_at','>',date('Y-m-d'))->get();
        $status = null;
        foreach ($billing as $bill) {
            if ($bill->type === 'onetime') {
                if($bill->status === 'paid' || $bill->status === 'complete' || $bill->status === 'active'){
                    $status = $bill->type;
                }
            }if ($bill->type === 'autorenew') {
                if($bill->status === 'paid' || $bill->status === 'complete' || $bill->status === 'active'){
                    $status = $bill->type;
                }
            } else {
                if($bill->status === 'paid' || $bill->status === 'complete' || $bill->status === 'active'){
                    $status = $bill->type;
                }
            }
        }
        return $status;
    }

    /**
     * Get user list
     */
    public function getUsers(Request $request)
    {
        try {
            return response()->json([
                'status' => true,
                'message' => 'User list successfully retrieved',
                'data' => User::where('role', '!=', 'admin')->get()
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Export users
     */
    public function export(Request $request)
    {
        try {
            $token = JWTAuth::verifyToken($request->query('token'),false);
            $admin = User::find($token->id);
            if($admin->role == 'admin'){
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="user_export_list_'.date('Y-m-d H:i:s').'_.csv"');
                header('Pragma: no-cache');
                header('Expires: 0');

                $fp = fopen('php://output', 'w');
                $result = User::where('role','!=','admin')->select('name','email')->get()->toArray();
                if (!$result) die("Couldn't fetch records");
                $headers = array_keys($result[0]);

                if ($fp && $result) {
                    fputcsv($fp, $headers);
                    foreach($result as $item){
                        fputcsv($fp,$item);
                    }
                }
                fclose($fp);
                exit;
            }else{
                return response()->json([
                    'status' => false,
                    'Unauthorized access'
                ],400);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'Unauthorized access or Invalid token'
            ],400);
        }
    }
}
