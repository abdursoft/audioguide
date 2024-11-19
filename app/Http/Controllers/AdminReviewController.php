<?php

namespace App\Http\Controllers;

use App\Models\AdminReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminReviewController extends Controller
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
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:admin_reviews,title',
            'description' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => "Couldn't add your review",
                'errors' => $validator->errors()
            ],400);
        }

        if($request->hasFile('image')){
            $image = Storage::disk('public')->put('reviews',$request->file('image'));
        }

        try {
            AdminReview::create([
                "title" => $request->title,
                "description" => $request->description,
                "image" => $image ?? Null
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Admin review successfully added'
            ],201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Admin review couldn\'t add'
            ],400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id=null)
    {
        if($id === null){
            $review = AdminReview::all();
        }else{
            $review = AdminReview::find($id);
        }
        return response()->json([
            'status' => true,
            'message' => 'Admin review successfully retrieved',
            'data' => $review
        ],200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdminReview $adminReview)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdminReview $adminReview)
    {
        $validator = Validator::make($request->all(), [
            'title' => "required|exists:admin_reviews,title,$request->review_id,id",
            'description' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => "Couldn't add your review",
                'errors' => $validator->errors()
            ],400);
        }

        $exist = AdminReview::find($request->review_id);

        if($request->hasFile('image')){
            $exist ? $image = Storage::disk('public')->put('reviews',$request->file('image')) : null;
        }

        try {
            AdminReview::create([
                "title" => $request->title,
                "description" => $request->description,
                "image" => $image ?? $exist->image
            ]);
            if($exist){
                Storage::disk('public')->delete($exist->image);
            }
            return response()->json([
                'status' => true,
                'message' => 'Admin review successfully updated'
            ],201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Admin review couldn\'t update'
            ],400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $review = AdminReview::find($id);
        if($review){
            Storage::disk('public')->delete($review->image);
            $review->delete();
            return response()->json([
                'status' => true,
                'message' => 'Admin review successfully deleted'
            ],200);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Admin review couldn\'t delete'
            ],400);
        }
    }
}
