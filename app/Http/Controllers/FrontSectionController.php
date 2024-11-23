<?php

namespace App\Http\Controllers;

use App\Models\FrontSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FrontSectionController extends Controller
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

        try {

            $images = '';
            if ($request->hasFile('image')) {
                $allowedFileExtension = ['jpeg', 'jpg', 'png', 'webp'];
                $files                = $request->file('image');
                if (is_array($request->file('image'))) {
                    foreach ($files as $key => $file) {
                        $extension = $file->getClientOriginalExtension();
                        $check     = in_array($extension, $allowedFileExtension);
                        if ($check) {
                            $images .= Storage::disk('public')->put("uploads/" . date('Y') . "/" . date('F') . "/" . date('d'), $files[$key]) . ",";
                        }
                    }
                    $images = trim($images, ',');
                } else {
                    $images = Storage::disk('public')->put("uploads/" . date('Y') . "/" . date('F') . "/" . date('d'), $request->file('image'));
                }
            }

            FrontSection::create([
                'pagename'              => $request->pagename,
                'section_title'             => $request->section_title,
                'section_title_two'         => $request->section_title_two,
                'heading'       => $request->heading,
                'heading_part_two'      => $request->heading_part_two,
                'short_description'     => $request->short_description,
                'description'             => $request->description,
                'short_description_two' => $request->short_description_two,
                'image' => $images,
                'faqs' => $request->faqs
            ]);
            return response()->json([
                'status'  => true,
                'message' => 'Front Section successfully created',
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Front Section couldn\'t create',
                'errors'  => $th->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id=null)
    {
        if($id !== null){
            $front = FrontSection::find($id);
        }else{
            $front = FrontSection::all();
        }
        return response()->json([
            'status' => true,
            'message' => "Front Section successfully retrieved",
            'data' => $front
        ],200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FrontSection $frontSection)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FrontSection $frontSection)
    {
        try {

            $images = '';
            if ($request->hasFile('image')) {
                $allowedFileExtension = ['jpeg', 'jpg', 'png', 'webp'];
                $files                = $request->file('image');
                if (is_array($request->file('image'))) {
                    foreach ($files as $key => $file) {
                        $extension = $file->getClientOriginalExtension();
                        $check     = in_array($extension, $allowedFileExtension);
                        if ($check) {
                            $images .= Storage::disk('public')->put("uploads/" . date('Y') . "/" . date('F') . "/" . date('d'), $files[$key]) . ",";
                        }
                    }
                    $images = trim($images, ',');
                } else {
                    $images = Storage::disk('public')->put("uploads/" . date('Y') . "/" . date('F') . "/" . date('d'), $request->file('image'));
                }
            }

            FrontSection::where('id',$request->input('id'))->update([
                'pagename'              => $request->pagename,
                'section_title'             => $request->section_title,
                'section_title_two'         => $request->section_title_two,
                'heading'       => $request->heading,
                'heading_part_two'      => $request->heading_part_two,
                'subheading'     => $request->subheading,
                'subheading_part_two'     => $request->subheading_part_two,
                'description'             => $request->description,
                'short_description_two' => $request->short_description_two,
                'image' => $images,
                'faqs' => json_encode($request->faqs)
            ]);
            return response()->json([
                'status'  => true,
                'message' => 'Front Section successfully updated',
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Front Section couldn\'t create',
                'errors'  => $th->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $front= FrontSection::find($id);
            if(!empty($front->image)){
                $images = explode(',',$front->image);
                foreach($images as $image){
                    if(file_exists(Storage::url($image))){
                        Storage::public()->delete($image);
                    }
                }
            }
            $front->delete();
            return response()->json([
                'status' => true,
                'message' => 'Front Section successfully removed'
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Front Section couldn\'t remove'
            ],400);
        }
    }
}
