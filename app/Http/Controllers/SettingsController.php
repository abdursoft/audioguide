<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => Settings::all()
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'required|file|mimes:jpeg,jpg,png,webp',
            'brand_logo' => 'required|file|mimes:jpeg,jpg,png,webp',
            'mobile_logo' => 'required|file|mimes:jpeg,jpg,png,webp',
            'icon' => 'required|file|mimes:png',
            'title' => 'required|max:50',
            'phone' => 'required|max:15',
            'email' => 'required|max:140',
            'address' => 'required|max:240',
            'primary_color' => 'required|max:150',
            'secondary_color' => 'required|max:150',
            'short_description' => 'required|max:250',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Settings data couldn't save",
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            Settings::updateOrCreate(
                [
                    'id' => 1
                ],
                [
                    'logo' => Storage::disk('public')->put('settings',$request->file('logo')),
                    'brand_logo' => Storage::disk('public')->put('settings',$request->file('brand_logo')),
                    'mobile_logo' => Storage::disk('public')->put('settings',$request->file('mobile_logo')),
                    'icon' => Storage::disk('public')->put('settings',$request->file('icon')),
                    'title' => $request->input('title'),
                    'phone' => $request->input('phone'),
                    'email' => $request->input('email'),
                    'address' => $request->input('address'),
                    'primary_color' => $request->input('primary_color'),
                    'secondary_color' => $request->input('secondary_color'),
                    'short_description' => $request->input('short_description'),
                    'description' => $request->input('description'),
                ]
            );
            return response()->json([
                'status' => true,
                'message' => "Settings data successfully save",
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Settings data couldn't save",
                'errors' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Settings $settings)
    {
        return response()->json([
            'status' => true,
            'data' => $settings
        ],200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Settings $settings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Settings $settings)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Settings $settings)
    {
        //
    }
}
