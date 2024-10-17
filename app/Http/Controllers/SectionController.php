<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SectionController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        return response()->json( [
            'status' => true,
            'data'   => Section::all(),
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
            'name'              => 'required|max:200|unique:sections,name',
            'title'             => 'required|max:300',
            'sub_title'         => 'required|max:300',
            'description'       => 'required',
            'button_title'      => 'required|max:300',
            'button_action'     => 'required|max:300',
            'mobile_image'      => 'file|mimes:jpeg,jpg,png,webp',
            'short_description' => 'required|max:300',
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'status'  => false,
                'message' => "Section couldn't create",
                'errors'  => $validator->errors(),
            ], 400 );
        }

        try {

            $images = '';
            if ( $request->hasFile( 'image' ) ) {
                $allowedFileExtension = ['jpeg', 'jpg', 'png', 'webp'];
                $files                = $request->file( 'image' );
                if ( is_array( $request->file( 'image' ) ) ) {
                    foreach ( $files as $key => $file ) {
                        $extension = $file->getClientOriginalExtension();
                        $check     = in_array( $extension, $allowedFileExtension );
                        if ( $check ) {
                            $images .= Storage::disk( 'public' )->put( "uploads/" . date( 'Y' ) . "/" . date( 'F' ) . "/" . date( 'd' ), $files[$key] ) . ",";
                        }
                    }
                    $images = trim( $images, ',' );
                } else {
                    $images = Storage::disk( 'public' )->put( "uploads/" . date( 'Y' ) . "/" . date( 'F' ) . "/" . date( 'd' ), $request->file( 'image' ) );
                }
            }

            Section::create( [
                'name'              => $request->name,
                'title'             => $request->title,
                'sub_title'         => $request->sub_title,
                'description'       => $request->description,
                'button_title'      => $request->button_title,
                'button_action'     => $request->button_action,
                'image'             => $images,
                'short_description' => $request->short_description,
            ] );
            return response()->json( [
                'status'  => true,
                'message' => 'Section successfully created',
            ], 201 );
        } catch ( \Throwable $th ) {
            return response()->json( [
                'status'  => false,
                'message' => 'Section couldn\'t create',
                'errors'  => $th->getMessage(),
            ], 400 );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( Section $section ) {
        return response()->json( [
            'status' => true,
            'data'   => $section,
        ], 200 );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( Section $section ) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Section $section, Request $request ) {
        $validator = Validator::make( $request->all(), [
            'name'              => 'required|max:200|unique:sections,name,' . $section->id . ',id',
            'title'             => 'required|max:300',
            'sub_title'         => 'required|max:300',
            'description'       => 'required',
            'button_title'      => 'required|max:300',
            'button_action'     => 'required|max:300',
            'mobile_image'      => 'file|mimes:jpeg,jpg,png,webp',
            'short_description' => 'required|max:300',
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'status'  => false,
                'message' => "Section couldn't create",
                'errors'  => $validator->errors(),
            ], 400 );
        }

        try {
            $images = '';
            if ( $request->hasFile( 'image' ) ) {
                $allowedFileExtension = ['jpeg', 'jpg', 'png', 'webp'];
                $files                = $request->file( 'image' );
                if ( is_array( $request->file( 'image' ) ) ) {
                    foreach ( $files as $key => $file ) {
                        $extension = $file->getClientOriginalExtension();
                        $check     = in_array( $extension, $allowedFileExtension );
                        if ( $check ) {
                            $images .= Storage::disk( 'public' )->put( "uploads/" . date( 'Y' ) . "/" . date( 'F' ) . "/" . date( 'd' ), $files[$key] ) . ",";
                        }
                    }
                    $images = trim( $images, ',' );
                } else {
                    $images = Storage::disk( 'public' )->put( "uploads/" . date( 'Y' ) . "/" . date( 'F' ) . "/" . date( 'd' ), $request->file( 'image' ) );
                }

                $files = explode( ',', $section->image );
                foreach ( $files as $file ) {
                    Storage::disk( 'public' )->delete( $file );
                }
            }

            $section->update( [
                'name'              => $request->name,
                'title'             => $request->title,
                'sub_title'         => $request->sub_title,
                'description'       => $request->description,
                'button_title'      => $request->button_title,
                'button_action'     => $request->button_action,
                'image'             => $images != '' ? $images : $section->image,
                'short_description' => $request->short_description,
            ] );
            return response()->json( [
                'status'  => true,
                'message' => 'Section successfully updated',
            ], 200 );
        } catch ( \Throwable $th ) {
            return response()->json( [
                'status'  => false,
                'message' => 'Section couldn\'t create',
                'errors'  => $th->getMessage(),
            ], 400 );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( Section $section ) {
        try {
            $files = explode( ',', $section->image );
            foreach ( $files as $file ) {
                Storage::disk( 'public' )->delete( $file );
            }
            $section->delete();
            return response()->json( [
                'status'  => true,
                'message' => 'Section successfully deleted',
            ], 200 );
        } catch ( \Throwable $th ) {
            return response()->json( [
                'status'  => false,
                'message' => 'Section successfully deleted',
                'errors'  => $th->getMessage()
            ], 400 );
        }
    }

    /**
     * Single section
     */
    public function singleSection($id){
        return response()->json( [
            'status' => true,
            'data'   => Section::find($id),
        ], 200 ); 
    }
}
