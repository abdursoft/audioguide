<?php

namespace App\Http\Controllers;

use App\Models\PersonLocation;
use App\Models\PersonObject;
use Illuminate\Http\Request;

class PersonLocationController extends Controller
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
                'data' => PersonLocation::all()
            ],200);
        }else{
            $location = PersonLocation::with(['audioGuide','object','object.personEvent','object.person'])->find($id);
            return response()->json([
                'status' => true,
                'message' => 'Location successfully retrieved',
                'data' => $location
            ],200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PersonLocation $personLocation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PersonLocation $personLocation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            PersonLocation::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Person location successfully deleted'
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Person location couldn\'t deleted'
            ],400);
        }
    }
}
