<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Payment\StripeController;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'User subscriptions retrieved',
            'data' => UserSubscription::where('user_id', $request->header('id'))->get(),
        ], 200);
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
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:subscriptions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Subscription couldn\'t create',
                'errors' => $validator->errors(),
            ], 400);
        }
        $customer = null;
        $stripe = new StripeController();
        $plan = Subscription::find($request->input('plan_id'));
        $user = User::find($request->header('id'));
        if ($plan->type === 'autorenew') {
            DB::beginTransaction();
            try {
                $customer_id = $user->customer_id;
                if ($customer_id === null) {
                    $customer = $stripe->customerCreate($user->name, $user->email);
                    User::where('id', $user->id)->update([
                        'customer_id' => $customer->id,
                    ]);
                    $customer_id = $customer->id;
                }

                $subscription = $stripe->subscriptionCreate($customer_id, [
                    'price' => $plan->stripe_price,
                ], []);
                $price = $stripe->productRetrievePrice($plan->stripe_price);
                UserSubscription::create([
                    'paid_amount' => $price->unit_amount / 100,
                    'currency' => $price->currency,
                    'user_id' => $user->id,
                    'guide_id' => 'all',
                    'type' => $plan->duration == null ? 'lifetime' : 'autorenew',
                    'started_at' => date('Y-m-d'),
                    'ended_at' => date('Y-m-d', strtotime("+$plan->duration months")),
                    'invoice_url' => $subscription->latest_invoice->invoice_pdf,
                    'stripe_subscription_id' => $subscription->id,
                ]);

                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Invoice successfully created',
                    'payment_url' => $subscription->latest_invoice->hosted_invoice_url,
                    'invoice_url' => $subscription->latest_invoice->invoice_pdf,
                ], 201);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Subscription couldn\'t create',
                    'errors' => $th->getMessage(),
                ], 400);
            }
        } else {
            DB::beginTransaction();
            try {
                $total = $plan->price;
                $transID = uniqid();
                $invoice = Invoice::create([
                    'total'           => $total,
                    'sub_total'       => $total,
                    'payable'         => $total,
                    'discount'        => '0',
                    'trans_id'        => $transID,
                    'coupon_code'     => $request->input('coupon_code') ?? null,
                    'delivery_status' => 'pending',
                    'type'            => 'lifetime',
                    'payment_status'  => 'pending',
                    'user_id'         => $request->header('id'),
                ]);

                $stripe  = new StripeController();
                $payment = $stripe->payment($request->header('email'), $total, 'eur', 'Purchase from ' . env('SITE_NAME'), [], $transID);
                $invoice->update([
                    'gateway' => 'stripe',
                    'payment_id' => $payment->id
                ]);
                DB::commit();
                return response()->json([
                    'status'   => true,
                    'message'  => "Invoice successfully created",
                    'payment_url' => $payment->url,
                ], 201);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Subscription couldn\'t create',
                    'errors' => $th->getMessage(),
                ], 400);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, UserSubscription $userSubscription)
    {
        if ($request->header('id') == $userSubscription->user_id) {
            return response()->json([
                'status' => true,
                'message' => 'User subscriptions retrieved',
                'data' => $userSubscription,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized data fetching...',
            ], 403);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserSubscription $userSubscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserSubscription $userSubscription)
    {
        //
    }

    /**
     * Get all subscription users
     */
    public function getSubscriptions(){
        try {
            return response()->json([
                'status' => true,
                'message' => "User subscription successfully retrieved",
                'data' => UserSubscription::with('user')->orderBy('id','desc')->get()
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ],400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, UserSubscription $userSubscription)
    {
        $stripe = new StripeController();
        if ($userSubscription->user_id == $request->header('id')) {
            $stripe->subscriptionCancel($userSubscription->stripe_subscription_id);
            return response()->json([
                'status' => true,
                'message' => 'Your subscription has been deleted',
            ]);
        }
    }

    public function cancel(Request $request)
    {
        try {
            $stripe = new StripeController();
            $subscription = UserSubscription::where('stripe_subscription_id', $request->input('subscription_id'))->where('user_id', $request->header('id'))->first();
            $stripe->subscriptionCancel($subscription->stripe_subscription_id);
            return response()->json([
                'status' => true,
                'message' => 'Your subscription has been canceled',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => true,
                'message' => 'Your subscription couldn\'t cancel',
                'errors' => $th->getMessage(),
            ], 400);
        }
    }

    public function resume(Request $request)
    {
        try {
            $stripe = new StripeController();
            $subscription = UserSubscription::where('stripe_subscription_id', $request->input('subscription_id'))->where('user_id', $request->header('id'))->first();
            $stripe->subscriptionResume($subscription->stripe_subscription_id);
            return response()->json([
                'status' => true,
                'message' => 'Your subscription has been resumed',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => true,
                'message' => 'Your subscription couldn\'t resume',
                'errors' => $th->getMessage(),
            ], 400);
        }
    }
}
