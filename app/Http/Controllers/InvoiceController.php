<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helper\Helper;
use App\Http\Controllers\Payment\StripeController;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\ProductCart;
use App\Models\ProductCoupon;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class InvoiceController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index( Request $request ) {
        return response()->json( [
            'status'  => true,
            'message' => 'Invoices successfully retrieved',
            'data'    => Invoice::with( 'AudioGuide' )->where( 'user_id', $request->header( 'id' ) )->get(),
        ], 200 );
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
        try {
            $carts = ProductCart::with( 'AudioGuide' )->where( 'user_id', $request->header( 'id' ) )->get();
            $total = ProductCart::where( 'user_id', $request->header( 'id' ) )->sum( 'price' );
            $discount = ProductCart::where('user_id', $request->header('id'))->sum('discount');

            if(!empty($request->input('coupon_code'))){
                $coupon = ProductCoupon::where('coupon', $request->input('coupon_code'))->first();

                $invoice = Invoice::where('user_id',$request->header('id'))->where('coupon',$request->input('coupon_code'))->count();

                $total = Helper::couponPrice($coupon,$invoice,$total);
            }

            DB::beginTransaction();
            $transID = uniqid();
            $invoice = Invoice::create( [
                'total'           => $total,
                'sub_total'       => $total,
                'payable'         => $total,
                'discount'        => $discount,
                'trans_id'        => $transID,
                'coupon_code'     => $request->input('coupon_code') ?? null,
                'delivery_status' => 'pending',
                'payment_status'  => 'pending',
                'user_id'         => $request->header('id'),
            ] );

            foreach ( $carts as $cart ) {
                InvoiceProduct::create( [
                    'quantity'       => $cart->quantity,
                    'sale_price'     => $cart->price,
                    'user_id'        => $request->header( 'id' ),
                    'invoice_id'     => $invoice->id,
                    'audio_guide_id' => $cart->audio_guide_id,
                ] );
            }

            if ( strtolower( $request->input( 'payment_type' ) ) === 'stripe' ) {
                $stripe  = new StripeController();
                $payment = $stripe->payment( $request->header( 'email' ), $total, 'eur', 'Purchase from ' . env( 'SITE_NAME' ), [],$transID );
                $invoice->update([
                    'gateway' => 'stripe',
                    'payment_id' => $payment->id
                ]);
            } elseif ( strtolower( $request->input( 'payment_type' ) ) === 'paypal' ) {

            } else {

            }

            $profile = Profile::where( 'user_id', $request->header( 'id' ) )->first();
            Mail::to( $request->header( 'email' ) )->send( new InvoiceMail( $carts, $profile->first_name ?? 'Abdur', $profile->street_address ?? 'NA', $profile->shipping_address ?? 'NA', $invoice->trans_id, 0, $total ) );
            DB::commit();

            return response()->json( [
                'status'   => true,
                'message'  => "Invoice successfully created",
                'payment_url' => $payment->url,
            ], 201 );
        } catch ( \Throwable $th ) {
            return response()->json( [
                'status'  => false,
                'message' => "Invoice couldn't create",
                'errors'  => $th->getMessage(),
            ], 400 );
        }

    }

    /**
     * Display the specified resource.
     */
    public function show( Request $request, Invoice $invoice ) {
        return response()->json( [
            'status'  => true,
            'message' => 'Invoices successfully retrieved',
            'data'    => $invoice->with( 'AudioGuide' )->where( 'user_id', $request->header( 'id' ) )->first(),
        ], 200 );
    }

    /**
     * Get invoices by users
     */
    public function getInvoices(){
        try {
            return response()->json([
                'status' => true,
                'message' => "Invoice successfully retrieved",
                'data' => Invoice::with('users')->orderBy('id','desc')->get()
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ],400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( Invoice $invoice ) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Request $request, Invoice $invoice ) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( Request $request, Invoice $invoice ) {
        try {
            if ( $invoice->user_id === $request->header( 'id' ) ) {
                $invoice->delete();
                return response()->json( [
                    'status'  => 'success',
                    'message' => 'Invoice successfully removed',
                ], 200 );
            }
        } catch ( \Throwable $th ) {
            return response()->json( [
                'status' => 'fail',
                'carts'  => "Unauthorized Access",
                'errors' => $th->getMessage(),
            ], 400 );
        }
    }
}
