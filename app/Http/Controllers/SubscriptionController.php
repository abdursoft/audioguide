<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
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
        $validator = Validator::make($request->all(),[
            "title" => 'required|unique:subscriptions,title',
            'description' => 'required|max:300',
            'price' => 'required',
            'currency' => 'required',
            'duration' => 'required|int',
            'status' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Subscription plan couldn\'t create',
                'errors'=> $validator->errors()
            ],400);
        }

        try {
            Subscription::create($validator->validate());
            return response()->json([
                'status' => true,
                'message' => 'Subscription plan successfully created'
            ],201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Subscription plan couldn\'t create',
                'errors'=> $th->getMessage()
            ],400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id=null)
    {
        $subscription = null;
        if($id !== null){
            $subscription = Subscription::find($id);
        }else{
            $subscription = Subscription::all();
        }

        return response()->json([
            'status' => true,
            'message' => 'Subscription plan successfully retrieved',
            'data' => $subscription
        ],200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscription $subscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subscription $subscription)
    {
        $validator = Validator::make($request->all(),[
            "title" => 'required|unique:subscriptions,title,'.$request->input('subscription_id').',id',
            'description' => 'required|max:300',
            'price' => 'required',
            'currency' => 'required',
            'duration' => 'required|int',
            'status' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Subscription plan couldn\'t update',
                'errors'=> $validator->errors()
            ],400);
        }

        try {
            Subscription::where('id',$request->input('subscription_id'))->update($validator->validate());
            return response()->json([
                'status' => true,
                'message' => 'Subscription plan successfully update'
            ],201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Subscription plan couldn\'t update',
                'errors'=> $th->getMessage()
            ],400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( Request $request,$id)
    {
        try {
            Subscription::where('id',$id)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Subscription plan has been deleted'
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Subscription plan couldn\'t delete',
                'errors'=> $th->getMessage()
            ],400);
        }
        
    }
}
