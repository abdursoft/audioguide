<?php

namespace App\Http\Controllers;

use App\Models\AudioGuide;
use App\Models\ProductCart;
use Illuminate\Http\Request;

class ProductCartController extends Controller
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
        try {
            $exist = AudioGuide::find($request->input('guide_id'));
            $quantity = $request->input('quantity') ?? 1;
            ProductCart::updateOrCreate(
                [
                    'user_id' => $request->header('id'),
                    'audio_guide_id' => $request->input(['guide_id'])
                ],
                [
                    'user_id' => $request->header('id'),
                    'audio_guide_id' => $request->input(['guide_id']),
                    'quantity' => $quantity,
                    'price' => $quantity * $exist->price,
                ]

            );
            return response()->json([
                'status' => true,
                'message' => 'Product successfully configured in your cart'
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Audio guide couldn\'t add to your cart',
                'errors' => $th->getMessage()
            ],400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCart $productCart,Request $request, $id=null)
    {
        if($id == null){
            return response()->json([
                'status' => true,
                'message' => 'Audio guide successfully retrieved',
                'data' => ProductCart::with('AudioGuide')->where('user_id',$request->header('id'))->get()
            ],200);
        }else{
            return response()->json([
                'status' => true,
                'message' => 'Audio guide successfully retrieved',
                'data' => ProductCart::with('AudioGuide')->where('id',$id)->where('user_id',$request->header('id'))->first()
            ],200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCart $productCart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductCart $productCart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCart $productCart, Request $request,$id)
    {

        // try {
        //     if($productCart->user_id === $request->header('id')){
        //         $productCart->delete();
        //         return response()->json([
        //             'status' => 'success',
        //             'message' => 'Product cart successfully removed'
        //         ],200);
        //     }
        // } catch (\Throwable $th) {
        //     return response()->json([
        //         'status' => 'fail',
        //         'message' => "Unauthorized Access"
        //     ],400);
        // }

        $exist = ProductCart::where('id',$id)->where('user_id',$request->header('id'))->first();
        if($exist){
            $exist->delete();
            return response()->json([
                'status' => 'success',
                'carts' => 'Product cart successfully removed'
            ],200);
        }else{
            return response()->json([
                'status' => 'fail',
                'carts' => "Unauthorized Access"
            ],401);
        }
    }
}
