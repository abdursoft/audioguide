<?php

namespace App\Http\Controllers;

use App\Models\PersonEvent;
use App\Models\PersonObject;
use Illuminate\Http\Request;

class PersonEventController extends Controller
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
                'data' => PersonEvent::all()
            ],200);
        }else{
            $event = PersonEvent::with(['audioGuide','object','object.person','object.personLocation'])->find($id);
            return response()->json([
                'status' => true,
                'message' => 'Event successfully retrieved',
                'data' => $event,
            ],200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PersonEvent $personEvent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PersonEvent $personEvent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            PersonEvent::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Person event successfully deleted'
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Person event couldn\'t deleted'
            ],400);
        }
    }
}
