<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller {
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
        $validator = Validator::make( $request->all(), [
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:50',
            'phone' => 'required',
            'current_address' => 'required',
            'permanent_address' => 'required',
            'profile_image' => 'file|mimes:jpeg,jpg,webp,png',
        ] );

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Profile couldn\'t save',
                'errors' => $validator->errors()
            ],400);
        }

        try {
            $exist = Profile::where('user_id',$request->header('id'))->first();
            $cover = $exist->profile_image;
            if($request->hasFile('profile_image')){
                $cover = Storage::disk('public')->put('profile',$request->file('logo'));
            }
            Profile::updateOrCreate([],[
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'phone' => $request->input('phone'),
                'current_address' => $request->input('current_address'),
                'permanent_address' => $request->input('permanent_address'),
                'profile_image' => $cover,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Profile successfully saved',
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Profile couldn\'t save',
            ],400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( Request $request, Profile $profile ) {
        return response()->json([
            'status' => true,
            'data' => $profile
        ],200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( Profile $profile ) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Request $request, Profile $profile ) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( Profile $profile ) {
        //
    }

    public function profile(Request $request){
        return response()->json([
            'status' => true,
            'data' => Profile::find('id',$request->header('id'))
        ],200);
    }
}
