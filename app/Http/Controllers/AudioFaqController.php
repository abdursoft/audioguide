<?php

namespace App\Http\Controllers;

use App\Models\AudioFaq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AudioFaqController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        return response()->json( [
            'status'  => true,
            'message' => 'FAQ successfully retrieved',
            'data'    => AudioFaq::all(),
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
            'question'             => 'required|unique:audio_faqs,question',
            'answer'               => 'required',
            'audio_description_id' => 'required|exists:audio_descriptions,id',
        ] );

        if ( $validate->fails() ) {
            return response()->json( [
                'status'  => false,
                'message' => "FAQ couldn\'t save",
                'errors'  => $validate->errors(),
            ], 400 );
        }

        try {
            AudioFaq::create( $validate->validate() );
            return response()->json( [
                'status'  => true,
                'message' => "FAQ successfully saved",
            ], 201 );
        } catch ( \Throwable $th ) {
            return response()->json( [
                'status'  => false,
                'message' => "FAQ couldn\'t save",
                'errors'  => $th->getMessage(),
            ], 400 );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( AudioFaq $audioFaq ) {
        return response()->json( [
            'status'  => true,
            'message' => 'FAQ successfully retrieved',
            'data'    => $audioFaq,
        ], 200 );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( AudioFaq $audioFaq ) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Request $request, AudioFaq $audioFaq ) {
        $validate = Validator::make( $request->all(), [
            'question'             => "required|unique:audio_faqs,question,$audioFaq->id,id",
            'answer'               => 'required',
            'audio_description_id' => 'required|exists:audio_descriptions,id',
        ] );

        if ( $validate->fails() ) {
            return response()->json( [
                'status'  => false,
                'message' => "FAQ couldn\'t update",
                'errors'  => $validate->errors(),
            ], 400 );
        }

        try {
            $audioFaq->update( $validate->validate() );
            return response()->json( [
                'status'  => true,
                'message' => "FAQ successfully updated",
            ], 200 );
        } catch ( \Throwable $th ) {
            return response()->json( [
                'status'  => false,
                'message' => "FAQ couldn\'t update",
                'errors'  => $th->getMessage(),
            ], 400 );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( AudioFaq $audioFaq ) {
        try {
            $audioFaq->delete();
            return response()->json( [
                'status'  => true,
                'message' => 'FAQ successfully removed',
            ], 200 );
        } catch ( \Throwable $th ) {
            return response()->json( [
                'status'  => false,
                'message' => 'FAQ couldn\'t remove',
                'errors'  => $th->getMessage(),
            ], 400 );
        }
    }
}
