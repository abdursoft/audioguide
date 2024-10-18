<?php

namespace App\Http\Controllers;

use App\Models\UserShipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserShippingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'shipping_email' => 'required|string',
            'phone' => 'required|string',
            'country' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'post_code' => 'required|string',
            'street' => 'required|string',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => "Shipping address couldn't added",
                'errors' => $validate->errors()
            ], 400);
        }

        try {
            UserShipping::updateOrCreate([
                'user_id' => $request->header('id')
            ], [
                'first_name' =>$request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'shipping_email' => $request->input('shipping_email'),
                'company' => $request->input('company') ?? null,
                'phone' => $request->input('phone'),
                'country' => $request->input('country'),
                'address' =>$request->input('address'),
                'city' => $request->input('city'),
                'district' => $request->input('district'),
                'post_code' => $request->input('post_code'),
                'street' => $request->input('street'),
                'user_id' => $request->header('id'),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Shipping address successfully saved'
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Shipping address couldn\'t saved',
                'error' => $th->getMessage()
            ],400);
        }
    }


    /**
     * Show the user Shipping address
     */
    public function show(Request $request){
        $bill = UserShipping::where('user_id',$request->header('id'))->first();
        if($bill) {
            return response([
                'status' => 'success',
                'data' => $bill
            ],200);
        } else{
            return response()->json([
                'status' => 'fail',
                'message' => 'Shipping address couldn\'t found'
            ],400);
        }
    }

    /**
     * Destroy the Shipping address
     */
    public function destroy(Request $request){
        $exist = UserShipping::where('user_id',$request->header('id'))->first();
        if($exist){
            $exist->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Shipping address successfully removed'
            ],200);
        }else{
            return response()->json([
                'status' => 'fail',
                'message' => 'Shipping address couldn\'t saved'
            ],400);
        }
    }
}