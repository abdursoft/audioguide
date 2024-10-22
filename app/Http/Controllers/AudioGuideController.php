<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helper\Helper;
use App\Models\AudioContent;
use App\Models\AudioDescription;
use App\Models\AudioFaq;
use App\Models\AudioGuide;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AudioGuideController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        return response()->json( [
            'status'  => true,
            'message' => 'Audio guide successfully retrieved',
            'data'    => AudioGuide::all(),
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
        if ( $request->input( 'data_type' ) === 'form' ) {
            $validator = Validator::make( $request->all(), [
                'category' => 'required',
            ] );
        } else {
            $validator = Validator::make( $request->all(), [
                'file'          => 'required|file',
                'cover'         => 'required|file|mimes:jpeg,jpg,png,webp',
                'description'   => 'required',
                'title'         => 'required|unique:audio_guides,title',
                'call_to_action' => 'required',
            ] );
        }

        if ( $validator->fails() ) {
            return response()->json( [
                'status'  => false,
                'message' => 'Audio guide couldn\'t create',
                'errors'  => $validator->errors(),
            ], 400 );
        }

        if ( $request->hasFile( 'file' ) ) {
            $file      = $request->file( 'file' );
            $extension = $file->getClientOriginalExtension();
            $file_name = $file->getClientOriginalName();
            $file_name = explode( ',', $file_name );
            $key_name  = strtolower( str_replace( ' ', '_', $file_name[0] ) );

            if ( 'csv' == $extension ) {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            } else {
                return response()->json( [
                    'status'  => false,
                    'message' => 'File type must be CSV',
                ], 400 );
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $reader->setReadDataOnly( true );
            }
            $reader->setInputEncoding( 'UTF-8' );
            $reader->setDelimiter( ',' );
            $reader->setEnclosure( '' );
            $reader->setSheetIndex( 0 );
            $spreadsheet = $reader->load( $file );

            $data      = $spreadsheet->getActiveSheet()->toArray();
            $keys      = $data[0];
            $sheetData = [];

            for ( $i = 1; $i < count( $data ); $i++ ) {
                $sheetData[] = $this->combineData( $data[$i], $keys );
            }
            // return response()->json($sheetData);

            try {
                DB::beginTransaction();
                $audio_guide = null;
                foreach ( $sheetData as $key => $item ) {
                    if ( $key === 0 ) {
                        $category = 1;
                        if ( !empty( $item->categoria ) ) {
                            $new_category = str_replace( ' ', '_', strtolower( $category ) );
                            $new_category = str_replace( '/', '_', $new_category );
                            $exist        = Category::where( 'category', $new_category )->first();

                            if ( $exist ) {
                                return $exist->id;
                            } else {
                                $category = Category::create( [
                                    'category' => strtolower( $new_category ),
                                    'name'     => $category,
                                ] );
                            }
                        }
                        $audio_guide = AudioGuide::create( [
                            "name"           => $key_name,
                            "title"          => $request->input( 'title' ) ?? $file_name[0],
                            "status"         => $request->input( 'status' ),
                            "price"          => $request->input( 'price' ),
                            "cover"          => Storage::disk('public')->put('guides',$request->file('cover')),
                            "category_id"    => $category,
                            "cal_to_action" => $request->input( 'call_to_action' ),
                        ] );
                    }
                    $item['audio_guide_id'] = $audio_guide->id;
                    AudioContent::create( $item );
                }
                if ( $audio_guide !== null && !empty( $request->input( 'description' ) ) ) {
                    $desc        = Helper::descriptionSanitize( $request->input( 'description' ) );
                    $description = AudioDescription::create( [
                        'files'          => $desc['files'],
                        'description'    => $desc['description'],
                        'audio_guide_id' => $audio_guide->id,
                    ] );
                    if ( !empty( $request->input( 'questions' ) ) ) {
                        $questions = $request->input( 'questions' );
                        $answers   = $request->input( 'answers' );

                        foreach ( $questions as $key => $question ) {
                            AudioFaq::create( [
                                'question'             => $question,
                                'answer'               => $answers[$key],
                                'audio_description_id' => $description->id,
                            ] );
                        }
                    }
                }
                DB::commit();
                return response()->json( [
                    'status'  => true,
                    'message' => 'Audio guide successfully created',
                ], 200 );
            } catch ( \Throwable $th ) {
                DB::rollBack();
                return response()->json( [
                    'status'  => false,
                    'message' => $th->getMessage(),
                ], 400 );
            }
        } else {
            return response()->json( [
                'status'  => false,
                'message' => "Please select a CSV | XLSX file",
            ], 400 );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( AudioGuide $audioGuide ) {
        return response()->json( [
            'status'  => true,
            'message' => 'Audio guide successfully retrieved',
            'data'    => $audioGuide,
        ], 200 );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( AudioGuide $audioGuide ) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( AudioGuide $audioGuide, Request $request ) {
        $validator = Validator::make( $request->all(), [
            'category' => 'required|unique:categories,name',
            'title'    => 'required',
            'cover'    => 'file|mimes:jpeg,jpg,png,webp',
            'status'   => 'required',
            'price'    => 'required',
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'status'  => false,
                'message' => 'Audio guide couldn\'t update',
                'errors'  => $validator->errors(),
            ] );
        }

        try {
            $cover = $audioGuide->cover;
            if ( $request->hasFile( 'cover' ) ) {
                $cover = Storage::disk( 'public' )->put( 'audio-guide', $request->file( 'cover' ) );
            }
            $audioGuide->update( [
                "title"       => $request->input( 'title' ),
                "status"      => $request->input( 'status' ),
                "price"       => $request->input( 'price' ),
                "cover"       => $cover,
                "category_id" => $request->input( 'category' ),
            ] );
            return response()->json( [
                'status'  => true,
                'message' => 'Audio guide successfully updated',
            ], 200 );
        } catch ( \Throwable $th ) {
            return response()->json( [
                'status'  => false,
                'message' => 'Audio guide couldn\'t update',
                'errors'  => $th->getMessage(),
            ], 400 );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( AudioGuide $audioGuide ) {
        try {
            if ( !empty( $audioGuide->cover ) ) {
                Storage::disk( 'public' )->delete( $audioGuide->cover );
            }
            $audioGuide->delete();
            return response()->json( [
                'status'  => true,
                'message' => 'Audio guide successfully removed',
            ], 200 );
        } catch ( \Throwable $th ) {
            return response()->json( [
                'status'  => false,
                'message' => 'Audio guide couldn\'t remove',
                'errors'  => $th->getMessage(),
            ], 400 );
        }
    }

    /**
     * Sanitize header data
     */
    public function sanitizeTitle( $data ) {
        $titles = [];

        foreach ( $data as $item ) {
            $item     = strtolower( $item );
            $item     = str_replace( ' ', '_', $item );
            $item     = str_replace( '/', '_', $item );
            $titles[] = $item;
        }
        return $titles;
    }

    /**
     * Combine the data
     */
    public function combineData( $row, $keys ) {
        $row     = $row;
        $newKeys = $this->sanitizeTitle( $keys );
        if ( count( $newKeys ) > count( $row ) ) {
            $newKeys = array_slice( $newKeys, 0, count( $row ) );
        }
        if ( count( $newKeys ) < count( $row ) ) {
            $row = array_slice( $row, 0, count( $newKeys ) );
        }
        $filter = [];
        foreach ( $newKeys as $index => $key ) {
            if ( $row[$index] !== null ) {
                $filter[$key] = $row[$index];
            }
        }
        return $filter;
    }

    public function parseAffiliate( $url ) {
    }

    public function getAudioGuide( $id ) {
        $guide = AudioGuide::with( ['Category', 'AudioContent'] )->find( $id );
        return response()->json( [
            'status' => true,
            'data'   => $guide,
        ], 200 );
    }

    // return the audio guide list
    public function onlyGuide() {
        return AudioGuide::paginate(
            $perPage = 1,
            $column = ['*'],
            $pageName = 'page'
        );
    }

    // return audio content by guide
    public function audioByContent( $id ) {
        return AudioContent::where( 'audio_guide_id', $id )->paginate(
            $perPage = 10,
            $column = ['*'],
            $pageName = 'page'
        );
    }
}
