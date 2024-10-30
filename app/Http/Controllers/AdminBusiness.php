<?php

namespace App\Http\Controllers;

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
            'email' => 'required|uniquie:users,email',
            'password' => 'min:8|max:20|alphanumberic',
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
     * All revenue
     */
    public function revenue(){
        $invoice = Invoice::where('payment_status','paid')->orWhere('payment_status','completed')->sum('total');
        // $subscription = 
    }
}
