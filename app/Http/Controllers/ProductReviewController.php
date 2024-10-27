<?php

namespace App\Http\Controllers;

use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductReviewController extends Controller
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
        $validate = Validator::make($request->all(),[
            'description' => 'required|string|max:1000',
            'guide_id' => 'required|exists:audio_guides,id',
            'star' => 'required',
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => 'fail',
                'message' => 'Review coludn\'t save',
                'errors' => $validate->errors()
            ],400);
        }

        try {
            ProductReview::updateOrCreate(
                [
                    'user_id' => $request->header('id'),
                    'audio_guide_id' => $request->input('product_id')
                ],
                [
                    'description' => $request->input('description'),
                    'star' => $request->input('star'),
                    'user_id' => $request->header('id'),
                    'audio_guide_id' => $request->input('product_id')
                ]
            );
            return response()->json([
                'status' => 'success',
                'message' => 'Thanks! for your feedback',
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Coludn\'t post your review',
                'errors' => $th->getMessage()
            ],400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductReview $productReview)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductReview $productReview)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductReview $productReview)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ProductReview $productReview)
    {
        try {
            if($productReview->user_id == $request->header('id')){
                $productReview->delete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Review successfully removed',
                ],200);
            }else{
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Unauthorized action',
                ],400);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Review ID or Profile couldn\'t match for the review',
            ],400);
        }
    }
}
