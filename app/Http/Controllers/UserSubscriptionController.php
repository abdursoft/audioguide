<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Payment\StripeController;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserSubscriptionController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store( Request $request ) {
        $validator = Validator::make( $request->all(), [
            'plan_id' => 'required|exists:subscriptions,id',
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'status'  => false,
                'message' => 'Subscription couldn\'t create',
                'errors'  => $validator->errors(),
            ], 400 );
        }
        $customer = null;
        try {
            DB::beginTransaction();
            $stripe      = new StripeController();
            $plan        = Subscription::find( $request->input( 'plan_id' ) );
            $user        = User::find( $request->header( 'id' ) );
            $customer_id = $user->customer_id;
            if ( $customer_id === null ) {
                $customer = $stripe->customerCreate( $user->name, $user->email );
                User::where( 'id', $user->id )->update( [
                    'customer_id' => $customer->id,
                ] );
                $customer_id = $customer->id;
            }

            $subscription = $stripe->subscriptionCreate( $customer_id, [
                'price' => $plan->stripe_price,
            ] );
            return response()->json( [
                'status'      => true,
                'message'     => 'Invoice successfully created',
                'payment_url' => $subscription->latest_invoice->hosted_invoice_url,
                'invoice_url' => $subscription->latest_invoice->invoice_pdf,
            ], 201 );

        } catch ( \Throwable $th ) {
            return response()->json( [
                'status'   => false,
                'message'  => 'Subscription couldn\'t create',
                'errors'   => $th->getMessage(),
                'customer' => $customer,
            ], 400 );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( UserSubscription $userSubscription ) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( UserSubscription $userSubscription ) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Request $request, UserSubscription $userSubscription ) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( UserSubscription $userSubscription ) {
        //
    }
}
