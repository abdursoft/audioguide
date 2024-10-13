<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        return response()->json( [
            'status'  => true,
            'message' => 'Category successfully retrieved',
            'data'    => Category::all(),
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
        $validator = Validator( $request->all(), [
            'category' => 'required|string|unique:categories,name',
            'image'    => 'file|mimes:jpeg,jpg,png',
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'status'  => false,
                'message' => 'Category couldn\'t create',
                'errors'  => $validator->errors(),
            ], 400 );
        }

        $file = "";
        if ( $request->hasFile( 'image' ) ) {
            $file = Storage::disk( 'public' )->put( 'category', $request->file( 'image' ) );
        }

        try {
            $new_category = str_replace( ' ', '_', strtolower( $request->input( 'category' ) ) );
            $new_category = str_replace( '/', '_', $new_category );
            Category::create( [
                'category' => $new_category,
                'name'     => $request->input( 'category' ),
                'image'    => $file,
            ] );
            return response()->json( [
                'status'  => true,
                'message' => 'Category successfully created',
            ], 200 );
        } catch ( \Throwable $th ) {
            return response()->json( [
                'status'  => false,
                'message' => 'Category couldn\'t create',
                'errors'  => $th->getMessage(),
            ], 200 );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( Category $category ) {
        return response()->json( [
            'status'  => true,
            'message' => 'Category successfully retrieved',
            'data'    => $category,
        ], 200 );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( Category $category ) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Request $request, Category $category) {
        
        $validator = Validator( $request->all(), [
            'category'    => "required|string|unique:categories,category,$category->id,id",
            'image'       => 'file|mimes:jpeg,jpg,png',
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'status'  => false,
                'message' => 'Category couldn\'t update',
                'errors'  => $validator->errors(),
            ], 400 );
        }

        try {
            $file  = $category->image;
            if ( $request->hasFile( 'image' ) ) {
                $file = Storage::disk( 'public' )->put( 'category', $request->file( 'image' ) );
            }

            $new_category = str_replace( ' ', '_', strtolower( $request->input( 'category' ) ) );
            $new_category = str_replace( '/', '_', $new_category );
            $category->update( [
                'category' => $new_category,
                'name'     => $request->input( 'category' ),
                'image'    => $file,
            ] );
            return response()->json( [
                'status'  => true,
                'message' => 'Category successfully updated',
            ], 200 );
        } catch ( \Throwable $th ) {
            return response()->json( [
                'status'  => false,
                'message' => 'Category couldn\'t update',
                'errors'  => $th->getMessage(),
            ], 400 );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( Category $category ) {
        try {
            if ( !empty( $category->image ) ) {
                Storage::disk( 'public' )->delete( $category->image );
            }
            $category->delete();
            return response()->json( [
                'status'  => true,
                'message' => 'Category successfully removed',
            ], 200 );
        } catch ( \Throwable $th ) {
            return response()->json( [
                'status'  => false,
                'message' => 'Category couldn\'t remove',
                'errors'  => $th->getMessage(),
            ], 400 );
        }
    }

    /**
     * Get Or Create category
     */
    public function getOrCreateCategory( $category ) {
        $new_category = str_replace( ' ', '_', strtolower( $category ) );
        $new_category = str_replace( '/', '_', $new_category );
        $exist        = Category::where( 'category', $new_category )->first();

        if ( $exist ) {
            return $exist->id;
        } else {
            $cat = Category::create( [
                'category' => strtolower( $new_category ),
                'name'     => $category,
            ] );
            return $cat->id;
        }
    }
}
