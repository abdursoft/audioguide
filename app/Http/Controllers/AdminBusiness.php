<?php

namespace App\Http\Controllers;

use App\Models\AudioGuide;
use App\Models\Invoice;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminBusiness extends Controller
{
    /**
     * Create business user
     */
    public function create(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|unique:users,email',
            'password' => 'min:8|max:20',
            'name' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Business user couldn\'t create',
                'errors' => $validator->errors()
            ],400);
        }

        try {
            User::create(
                [
                    "name" => $request->input('name'),
                    "email" => $request->input('email'),
                    'is_verified' => 1,
                    'role' => 'business',
                    'demo' => '1',
                    'pwd' => $request->input('password'),
                    "password" => password_hash($request->input('password'), PASSWORD_DEFAULT)
                ]
            );
            return response()->json([
                'status' => true,
                'message' => 'Business user successfully created'
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ],400);
        }
    }

    /**
     * Delete business profile
     */
    public function delete($id){
        $bussiness = User::find($id);
        if($bussiness->role == 'business'){
            $sub_business = User::where('business_id',$bussiness->id)->get();
            foreach($sub_business as $user){
                $user->delete();
            }
            $bussiness->delete();
            return response()->json([
                'status' => true,
                'message' => 'Business User successfully deleted'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Selected user is not a business user'
            ]);
        }
    }

    /**
     * Inactive demo users permission
     */
    public function businessActivation(Request $request){
        $validator = Validator::make($request->all(),[
            'user_id' => 'required',
            'status' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => "Couldn't change the user status",
                'errors' => $validator->errors()
            ],400);
        }

        try {
            User::where('id',$request->user_id)->update([
                'demo' => $request->status
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Status successfully changed',
                'users' => User::where('role','business')->get()
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Internal server error'
            ],400);
        }
    }

    /**
     * Get business user
     */
    public function showBusinessUser($id=null){
        if($id === null){
            $user = User::where('role','business')->get();
        }else{
            $user = User::where('id',$id)->where('role','business')->first();
        }
        return response()->json([
            'status' => true,
            'message' => 'Business user successfully retrieved',
            'data' => $user
        ]);
    }

    /**
     * All subscriptions
     */
    public function subscriptions($id=null){
        if($id !== null){
            $sub = UserSubscription::find($id);
        }else{
            $sub = UserSubscription::orderBy('id','desc')->get();
        }
        return response()->json([
            'status' => true,
            'message' => 'User subscription successfully retrieved',
            'data' => $sub
        ],200);
    }

    /**
     * All statistics
     */
    public function statistics(){
        $invoice = Invoice::where('payment_status','paid')->orWhere('payment_status','completed')->sum('total');
        $users = User::where('role','user')->count();
        $demo = User::where('role','business')->count();
        $premium = UserSubscription::where('status','paid')->orWhere('status','active')->orWhere('status','completed')->distinct()->get('user_id')->count();
        $general = AudioGuide::where('type','general')->count();
        $special = AudioGuide::where('type','special')->count();
        $visitors = (new DeviceController)->report();

        return response()->json([
            'status' => true,
            'message' => 'Site statistics',
            'invoice' => $invoice,
            'users' => $users,
            'demo_user' => $demo,
            'premium_user' => $premium,
            'general_guide' => $general,
            'special_guide' => $special,
            'visitors' => $visitors
        ],200);
    }

    /**
     * Admin password
     */
    public function adminPassword(Request $request){
        $validator = Validator::make($request->all(),[
            'newPassword' => 'required',
            'oldPassword' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Password couldn\'t change',
                'errors' => $validator->errors()
            ],400);
        }

        try {
            $admin = User::where('id',$request->header('id'))->where('role','admin')->first();
            if(password_verify($request->oldPassword, $admin->password)){
                $admin->update([
                    'password' => password_hash($request->input('newPassword'), PASSWORD_DEFAULT)
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Password successfully changed'
                ],200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Old password not match'
                ],400);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ],400);
        }
    }
}
