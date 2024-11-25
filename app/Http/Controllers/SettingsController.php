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
            'data' => Settings::find(1)
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
            'logo' => 'file|mimes:jpeg,jpg,png,webp',
            'brand_logo' => 'file|mimes:jpeg,jpg,png,webp',
            'mobile_logo' => 'file|mimes:jpeg,jpg,png,webp',
            'icon' => 'file|mimes:png',
            'title' => 'max:300',
            'phone' => 'max:15',
            'email' => 'max:140',
            'address' => 'max:240',
            'primary_color' => 'max:150',
            'secondary_color' => 'max:150',
            'short_description' => 'max:250',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Settings data couldn't save",
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $exists = Settings::find(1);
            Settings::updateOrCreate(
                [
                    'id' => 1
                ],
                [
                    'logo' => $request->hasFile('logo') ? Storage::disk('public')->put('settings',$request->file('logo')) : (empty($exists) ? Null : $exists->logo),
                    'brand_logo' => $request->hasFile('brand_logo') ? Storage::disk('public')->put('settings',$request->file('brand_logo')) : (empty($exists) ? Null : $exists->brand_logo),
                    'mobile_logo' => $request->hasFile('mobile_logo') ? Storage::disk('public')->put('settings',$request->file('mobile_logo')) : (empty($exists) ? Null : $exists->mobile_logo),
                    'icon' => $request->hasFile('icon') ? Storage::disk('public')->put('settings',$request->file('icon')) : Null,
                    'title' => !empty($request->input('title')) ? $request->input('title') : (empty($exists) ? Null : $exists->title),
                    'phone' => !empty($request->input('phone')) ? $request->input('phone') : (empty($exists) ? Null : $exists->phone),
                    'email' => !empty($request->input('email')) ? $request->input('email') : (empty($exists) ? Null : $exists->email),
                    'address' => !empty($request->input('address')) ? $request->input('address') : (empty($exists) ? Null : $exists->address),
                    'description' => !empty($request->input('description')) ? $request->input('description') : (empty($exists) ? Null : $exists->description),
                    'primary_color' => $request->input('primary_color') ?? Null,
                    'secondary_color' => $request->input('secondary_color') ?? Null,
                    'short_description' => $request->input('short_description') ?? Null,
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
