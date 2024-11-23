<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\PersonEvent;
use App\Models\PersonLocation;
use App\Models\PersonObject;
use Illuminate\Http\Request;

class PersonController extends Controller
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

    }

    /**
     * Display the specified resource.
     */
    public function show($id=null)
    {
        if($id === null){
            return response()->json([
                'status' => true,
                'data' => Person::all()
            ],200);
        }else{
            $person = Person::find($id);
            $object = PersonObject::where('person_id',$person->id)->orderBy('id','Desc')->get();
            return response()->json([
                'status' => true,
                'person' => $person,
                'object' => $object
            ],200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Person $person)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Person $person)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            Person::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Person successfully deleted'
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Person couldn\'t deleted'
            ],400);
        }
    }
}
