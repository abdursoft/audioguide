<?php

namespace App\Http\Controllers;

use App\Models\AudioGuide;
use App\Models\ProductWish;
use Illuminate\Http\Request;

class ProductWishController extends Controller {
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
        $exist = AudioGuide::find( $request->input( 'guide_id' ) );
        if ( $exist ) {
            try {
                ProductWish::updateOrCreate(
                    [
                        'user_id'        => $request->header( 'id' ),
                        'audio_guide_id' => $request->input( ['guide_id'] ),
                    ],
                    [
                        'user_id'        => $request->header( 'id' ),
                        'audio_guide_id' => $request->input( ['guide_id'] ),
                    ]

                );
                return response()->json( [
                    'status'  => true,
                    'message' => 'Audio guide successfully configured in your wishlist',
                ], 200 );
            } catch ( \Throwable $th ) {
                return response()->json( [
                    'status'  => false,
                    'message' => 'Audio guide couldn\'t add in your wishlist',
                    'errors'  => $th->getMessage(),
                ], 400 );
            }
        } else {
            return response()->json( [
                'status'  => false,
                'message' => 'Audio guide not found',
            ], 400 );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( Request $request, $id=null ) {
        if($id == null){
            return response()->json([
                'status' => true,
                'message' => 'Audio guide successfully retrieved',
                'data' => ProductWish::with('AudioGuide')->where('user_id',$request->header('id'))->get()
            ],200);
        }else{
            return response()->json([
                'status' => true,
                'message' => 'Audio guide successfully retrieved',
                'data' => ProductWish::with('AudioGuide')->where('id',$id)->where('user_id',$request->header('id'))->first()
            ],200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( ProductWish $productWish ) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Request $request, ProductWish $productWish ) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( Request $request, $id) {
        // try {
        //     if ( $productWish->user_id === $request->header( 'id' ) ) {
        //         $productWish->delete();
        //         return response()->json( [
        //             'status' => 'success',
        //             'carts'  => 'Audio guide successfully removed from wish list',
        //         ], 200 );
        //     }
        // } catch ( \Throwable $th ) {
        //     return response()->json( [
        //         'status' => 'fail',
        //         'carts'  => "Unauthorized Access",
        //     ], 400 );
        // }


        $exist = ProductWish::where('id',$id)->where('user_id',$request->header('id'))->first();
        if($exist){
            $exist->delete();
            return response()->json([
                'status' => 'success',
                'carts' => 'Audio guide successfully removed from wishlist'
            ],200);
        }else{
            return response()->json([
                'status' => 'fail',
                'carts' => "Unauthorized Access"
            ],401);
        }
    }
}
