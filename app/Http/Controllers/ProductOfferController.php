<?php

namespace App\Http\Controllers;

use App\Models\AudioGuide;
use App\Models\ProductOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductOfferController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        return response()->json( [
            'status'  => true,
            'message' => 'Audio offer successfully retrieved',
            'data'    => ProductOffer::all(),
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
        $validator = Validator::make( $request->all(), [
            'status'         => 'required',
            'offer_type'     => 'required',
            'offer_amount'   => 'required',
            'audio_guide_id' => 'required|exists:audio_guides,id',
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'status'  => false,
                'message' => 'Audio offer couldn\'t save',
                'errors'  => $validator->errors(),
            ], 400 );
        }

        try {
            $audio = AudioGuide::find( $request->input( 'audio_guide_id' ) );
            if ( $request->input( 'offer_type' ) === 'percent' ) {
                $price = $audio->price * $request->input( 'offer_amount' ) / 100;
            } else {
                $price = $audio->price - $request->input( 'offer_amount' );
            }
            if($price > $audio->price){
                return response()->json([
                    'status' => false,
                    'message' => 'Audio offer couldn\'t be more than audio price',
                ],400);
            }
            ProductOffer::updateOrCreate( [
                'audio_guide_id' => $request->input( 'audio_guide_id' ),
            ], [
                'status'         => $request->input('status'),
                'offer_type'     => $request->input('offer_type'),
                'offer_amount'   => $request->input('offer_amount'),
                'price_amount'   => $price,
                'audio_guide_id' => $request->input('audio_guide_id'),
            ] );

            return response()->json([
                'status' => true,
                'message' => 'Audio offer successfully saved'
            ],200);
        } catch ( \Throwable $th ) {
            return response()->json([
                'status' => false,
                'message' => 'Audio offer couldn\'t save',
                'errors' => $th->getMessage()
            ],400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( ProductOffer $productOffer ) {
        return response()->json( [
            'status'  => true,
            'message' => 'Audio offer successfully retrieved',
            'data'    => $productOffer,
        ], 200 );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( ProductOffer $productOffer ) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Request $request, ProductOffer $productOffer ) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( ProductOffer $productOffer ) {
        try {
            $productOffer->delete();
            return response()->json( [
                'status'  => true,
                'message' => 'Audio offer successfully removed',
            ], 200 );
        } catch ( \Throwable $th ) {
            return response()->json( [
                'status'  => false,
                'message' => 'Audio offer couldn\'t remove',
                'errors'  => $th->getMessage(),
            ], 400 );
        }
    }
}
