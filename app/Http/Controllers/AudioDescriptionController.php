<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helper\Helper;
use App\Models\AudioDescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AudioDescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json( [
            'status'  => true,
            'message' => 'Audio description successfully retrieved',
            'data'    => AudioDescription::all(),
        ], 200 );
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
            'description' => 'required',
            'audio_guide_id' => 'required|exists:audio_guides,id'
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Audio description couldn\'t configure',
                'errors' => $validate->errors()
            ],400);
        }

        try {
            $description = Helper::descriptionSanitize($request->input('description'));
            AudioDescription::updateOrCreate([
                "audio_guide_id" => $request->input('audio_guide_id')
            ],[
                'files' => $description['files'],
                'description' => $description['description'],
                'audio_guide_id' => $request->input('audio_guide_id')
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Audio Description successfully saved'
            ],201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Audio Description couldn\'t save',
                'errors' => $th->getMessage()
            ],400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AudioDescription $audioDescription)
    {
        return response()->json( [
            'status'  => true,
            'message' => 'Audio description successfully retrieved',
            'data'    => $audioDescription,
        ], 200 );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AudioDescription $audioDescription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AudioDescription $audioDescription)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AudioDescription $audioDescription)
    {
        try {
            if ( !empty( $audioDescription->files ) ) {
                $explode = explode(',',$audioDescription->files);
                foreach($explode as $item){
                    Storage::disk( 'public' )->delete( $item );
                }
            }
            $audioDescription->delete();
            return response()->json( [
                'status'  => true,
                'message' => 'Audio description successfully removed',
            ], 200 );
        } catch ( \Throwable $th ) {
            return response()->json( [
                'status'  => false,
                'message' => 'Audio description couldn\'t remove',
                'errors' => $th->getMessage()
            ], 400 );
        }
    }
}
