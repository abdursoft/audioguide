<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Payment\StripeController;
use App\Models\Subscription;
use App\Models\Update;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => true,
            'message' => 'Subscription plan successfully retrieved',
            'data' => Subscription::orderBy('id','desc')->get()
        ],200);
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
            DB::beginTransaction();
            $stripe = new StripeController();
            $product = $stripe->productCreate($request->input('title'),$request->input('description'),$request->input('price'));
            $price = null;
            if($request->input('duration') != '0'){
                $price = $stripe->productPrice($product->id,$request->input('price'),$request->input('currency'));
            }
            $data = array_merge($validator->validate(),[
                'stripe_id' => $product->id,
                'stripe_price' => $price->id ?? $request->input('price'),
                'type' => $price ? 'autorenew' : 'lifetime'
            ]);

            $subscription = Subscription::create($data);
            Update::create([
                'image' => $request->hasFile('image') ? Storage::disk('public')->put('guides', $request->file('cover')) : null,
                'title' => $request->input('title'),
                'sub_title' => $request->input('description'),
                'reference_id' => $subscription->id,
                'type' => 'offer'
            ]);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Subscription plan successfully created'
            ],201);
        } catch (\Throwable $th) {
            DB::rollBack();
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
    public function show(Subscription $subscription)
    {
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
            "title" => 'required|unique:subscriptions,title,'.$subscription->id.',id',
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
            $subscription->update($validator->validate());
            return response()->json([
                'status' => true,
                'message' => 'Subscription plan successfully updated'
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
    public function destroy( Subscription $subscription)
    {
        try {
            $subscription->delete();
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

    /**
     * Deactive subscription
     */
    public function deactiveSubscription(Request $request){
        try {
            $plan = Subscription::find($request->input('plan_id'));
            Subscription::where('id',$request->input('plan_id'))->update([
                'status' => $plan->status == 'inactive' ? 'active' : 'inactive'
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Subscription plan\'s status changed'
            ],200);
        } catch (\Throwable $th) {
           return response()->json([
                'status' => false,
                'message' => 'Subscription plan\'s status couldn\'t change'
            ],400);
        }
    }


    /**
     * Single subscription
     */
    public function singleSubScription($id){
        return response()->json([
            'status' => true,
            'message' => 'Subscription plan successfully retrieved',
            'data' => Subscription::find($id)
        ],200);
    }
}
