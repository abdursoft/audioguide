<?php

namespace App\Http\Controllers;

use App\Models\AudioGuide;
use App\Models\ProductCart;
use App\Models\ProductOffer;
use Illuminate\Http\Request;

class ProductCartController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index( Request $request ) {
        return response()->json( [
            'status'  => true,
            'message' => 'Audio guide successfully retrieved',
            'data'    => ProductCart::with( 'AudioGuide', 'AudioGuide.ProductOffer' )->where( 'user_id', $request->header( 'id' ) )->get(),
            'price'   => ProductCart::where( 'user_id', $request->header( 'id' ) )->sum('price')
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
            $exist         = AudioGuide::find( $request->input( 'guide_id' ) );
            $product_offer = ProductOffer::where( 'audio_guide_id', $request->input( 'guide_id' ) )->first();

            $quantity = $request->input( 'quantity' ) ?? 1;
            $discount = 0;
            if ( !empty( $product_offer ) && $product_offer->status == 'active' ) {
                $price = $quantity * $product_offer->price_amount;
                $discount = ($exist->price - $product_offer->price_amount) * $quantity;
            } else {
                $price = $quantity * $exist->price;
            }

            ProductCart::updateOrCreate(
                [
                    'user_id'        => $request->header( 'id' ),
                    'audio_guide_id' => $request->input( ['guide_id'] ),
                ],
                [
                    'user_id'        => $request->header( 'id' ),
                    'audio_guide_id' => $request->input( ['guide_id'] ),
                    'quantity'       => $quantity,
                    'discount'       => $discount,
                    'price'          => number_format($price,2),
                ]

            );
            return response()->json( [
                'status'  => true,
                'message' => 'Product successfully configured in your cart',
                'data'    => ProductCart::with( 'AudioGuide' )->where( 'user_id', $request->header( 'id' ) )->get(),
            ], 200 );
        } catch ( \Throwable $th ) {
            return response()->json( [
                'status'  => false,
                'message' => 'Audio guide couldn\'t add to your cart',
                'errors'  => $th->getMessage(),
            ], 400 );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( ProductCart $productCart, Request $request, $id = null ) {
        return response()->json( [
            'status'  => true,
            'message' => 'Audio guide successfully retrieved',
            'data'    => $productCart->with( 'AudioGuide' )->where( 'id', $id )->where( 'user_id', $request->header( 'id' ) )->first(),
        ], 200 );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( ProductCart $productCart ) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Request $request, ProductCart $productCart ) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( Request $request, ProductCart $productCart ) {
        try {
            if ( $productCart->user_id == $request->header( 'id' ) ) {
                $productCart->delete();
                return response()->json( [
                    'status'  => 'success',
                    'message' => 'Product cart successfully removed',
                    'data'    => ProductCart::with( 'AudioGuide' )->where( 'user_id', $request->header( 'id' ) )->get(),
                    'price'   => ProductCart::where( 'user_id', $request->header( 'id' ) )->sum('price')
                ], 200 );
            }else{
                return response()->json($productCart);
            }
        } catch ( \Throwable $th ) {
            return response()->json( [
                'status'  => 'fail',
                'message' => "Unauthorized Access",
                'errors'  => $th->getMessage(),
            ], 400 );
        }
    }

        /**
     * Remove the specified resource from storage.
     */
    public function delete( Request $request, $id=null ) {
        if($id !== null){
            $productCart = ProductCart::find($id);
            if ( $productCart->user_id == $request->header( 'id' ) ) {
                $productCart->delete();
                return response()->json( [
                    'status'  => 'success',
                    'message' => 'Product cart successfully removed',
                    'data'    => ProductCart::with( 'AudioGuide' )->where( 'user_id', $request->header( 'id' ) )->get(),
                    'price'   => ProductCart::where( 'user_id', $request->header( 'id' ) )->sum('price')
                ], 200 );
            }else{
                return response()->json( [
                    'status'  => 'fail',
                    'message' => "Unauthorized Access",
                ], 400 );
            }
        }
    }
}
