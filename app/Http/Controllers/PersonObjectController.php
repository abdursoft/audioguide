<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\PersonEvent;
use App\Models\PersonLocation;
use App\Models\PersonObject;
use Illuminate\Http\Request;

class PersonObjectController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id=null)
    {
        if($id === null){
            return response()->json([
                'status' => true,
                'data' => PersonObject::all()
            ],200);
        }else{
            $object = PersonObject::with(['audioGuide','audioGuide.AudioDescription','audioGuide.person','audioGuide.personEvent','audioGuide.personLocation'])->find($id);
            return response()->json([
                'status' => true,
                'message' => 'Object successfully retrieved',
                'data' => $object,
            ],200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PersonObject $personObject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PersonObject $personObject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            PersonObject::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Person object successfully deleted'
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Person object couldn\'t deleted'
            ],400);
        }
    }
}
