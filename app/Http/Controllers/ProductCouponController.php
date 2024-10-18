<?php

namespace App\Http\Controllers;

use App\Models\ProductCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductCouponController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        return response()->json( [
            'status'  => true,
            'message' => 'Coupon code successfully retrieved',
            'data'    => ProductCoupon::all(),
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
        $validate = Validator::make( $request->all(), [
            'title'        => 'required|string|unique:product_coupons',
            'code'         => 'required|string|unique:product_coupons,coupon',
            'amount'       => 'required|string',
            'coupon_type'  => 'required|string',
            'started_at'   => 'required',
            'expired_at'   => 'required',
            'status'       => 'required',
            'min_purchase' => 'required',
            'banner'       => 'file|mimes:jpeg,jpg,png,webp',
        ] );

        if ( $validate->fails() ) {
            return response()->json( [
                'status'  => 'fail',
                'message' => 'Coupon couldn\'t create',
                'errors'  => $validate->errors(),
            ], 400 );
        }

        try {

            ProductCoupon::create( [
                'title'        => $request->input( 'title' ),
                'sub_title'    => $request->input( 'sub_title' ) ?? null,
                'coupon'       => $request->input( 'code' ),
                'amount'       => $request->input( 'amount' ),
                'coupon_type'  => $request->input( 'coupon_type' ),
                'started_at'   => $request->input( 'started_at' ),
                'expired_at'   => $request->input( 'expired_at' ),
                'status'       => $request->input( 'status' ),
                'min_purchase' => $request->input( 'min_purchase' ),
                'banner'       => $request->hasFile( 'banner' ) ? Storage::disk( 'public' )->put( "uploads/" . date( 'Y' ) . "/" . date( 'F' ) . "/" . date( 'd' ), $request->file( 'banner' ) ) : null,
            ] );

            return response()->json( [
                'status'  => 'success',
                'message' => 'Coupon code successfully saved',
            ], 200 );
        } catch ( \Throwable $th ) {
            return response()->json( [
                'status'  => 'fail',
                'message' => 'Coupon code couldn\'t save',
                'error'   => $th->getMessage(),
            ], 400 );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( ProductCoupon $productCoupon,$id=null ) {
        if($id !== null){
            $coupons = ProductCoupon::find($id);
        }else{
            $coupons = ProductCoupon::all();
        }
        return response()->json( [
            'status'  => true,
            'message' => 'Coupon code successfully retrieved',
            'data'    => $coupons,
        ], 200 );

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( ProductCoupon $productCoupon ) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Request $request, $id ) {
        $validate = Validator::make( $request->all(), [
            'title'        => 'required|string|unique:product_coupons,title,' . $id . ',id',
            'code'         => 'required|string|unique:product_coupons,coupon,' . $id . ',id',
            'amount'       => 'required|string',
            'coupon_type'  => 'required|string',
            'started_at'   => 'required',
            'expired_at'   => 'required',
            'status'       => 'required',
            'min_purchase' => 'required',
            'banner'       => 'file|mimes:jpeg,jpg,png,webp',
        ] );

        if ( $validate->fails() ) {
            return response()->json( [
                'status'  => 'fail',
                'message' => 'Coupon code couldn\'t update',
                'errors'  => $validate->errors(),
            ], 400 );
        }

        try {
            $productCoupon = ProductCoupon::find($id);
            $productCoupon->update( [
                'title'        => $request->input( 'title' ) ?? $productCoupon->title,
                'sub_title'    => $request->input( 'sub_title' ) ?? $productCoupon->sub_title,
                'coupon'       => $request->input( 'code' ),
                'amount'       => $request->input( 'amount' ),
                'coupon_type'  => $request->input( 'coupon_type' ),
                'started_at'   => $request->input( 'started_at' ),
                'expired_at'   => $request->input( 'expired_at' ),
                'status'       => $request->input( 'status' ),
                'min_purchase' => $request->input( 'min_purchase' ),
                'banner'       => $request->hasFile( 'banner' ) ? Storage::disk( 'public' )->put( "uploads/" . date( 'Y' ) . "/" . date( 'F' ) . "/" . date( 'd' ), $request->file( 'banner' ) ) : $productCoupon->banner,
            ] );
            ( $request->hasFile( 'banner' ) && $productCoupon->banner != null ) ? Storage::disk( 'public' )->delete( $productCoupon->banner ) : null;

            return response()->json( [
                'status'  => 'success',
                'message' => 'Coupon code successfully updated',
            ], 200 );
        } catch ( \Throwable $th ) {
            return response()->json( [
                'status'  => 'fail',
                'message' => 'Coupon code couldn\'t update',
                'error'   => $th->getMessage(),
            ], 400 );
        }
    }

    /**
     * Get the single coupon
     */
    public function singleCoupon($id){
        return response()->json( [
            'status'  => true,
            'message' => 'Coupon code successfully retrieved',
            'data'    => ProductCoupon::find($id),
        ], 200 );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( ProductCoupon $productCoupon, $id ) {
        $productCoupon = ProductCoupon::find($id);
        if ( $productCoupon ) {
            $productCoupon->banner != null ? Storage::disk( 'public' )->delete( $productCoupon->banner ) : null;
            $productCoupon->delete();
            return response()->json( [
                'status'  => 'success',
                'message' => 'Coupon code successfully removed',
            ], 200 );
        } else {
            return response()->json( [
                'status'  => 'fail',
                'message' => 'Coupon code couldn\'t found',
            ], 400 );
        }
    }

    /**
     * Verify user coupon
     */
    public function couponVerify( Request $request ) {
        if ( !empty( $request->input( 'coupon_code' ) ) ) {
            $coupon = ProductCoupon::where( 'coupon_code', $request->input( 'coupon_code' ) )->first();
            if ( $coupon ) {
                if ( strtotime( $coupon->expired_at ) > time() && time() > strtotime( $coupon->started_at ) ) {
                    return response()->json( [
                        'status'  => 'success',
                        'message' => 'Coupon code verified',
                        'data'    => $coupon,
                    ], 200 );
                } else {
                    return response()->json( [
                        'status'  => 'fail',
                        'message' => 'Coupon code expired or not begin',
                    ], 400 );
                }
            } else {
                return response()->json( [
                    'status'  => 'fail',
                    'message' => 'Coupon code not exist',
                ], 400 );
            }
        } else {
            return response()->json( [
                'status'  => 'fail',
                'message' => 'Coupon code is required',
            ], 400 );
        }
    }
}
