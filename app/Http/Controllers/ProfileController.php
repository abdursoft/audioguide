<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
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
    }

    /**
     * Display the specified resource.
     */
    public function show( Profile $profile ) {
        //
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
}
