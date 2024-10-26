<?php

namespace App\Http\Controllers;

use App\Models\AudioHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AudioHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'Audio history successfully retrieved',
            'data'   => AudioHistory::where('user_id', $request->header('id'))->get(),
        ], 200);
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
        $validate = Validator::make($request->all(), [
            'guide_id' => 'required|exists:audio_guides,id',
            'status' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Couldn\'t add the history',
                'errors' => $validate->errors()
            ], 400);
        }

        try {
            AudioHistory::updateOrCreate([
                'user_id' => $request->header('id'),
                'audio_guide_id' => $request->input('guide_id')
            ], [
                'status' => $request->input('status'),
                'user_id' => $request->header('id'),
                'audio_guide_id' => $request->input('guide_id')
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Audio history successfully added'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Audio history couldn\'t add',
                'errors' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, AudioHistory $audioHistory)
    {
        if ($request->header('id') == $audioHistory->user_id) {
            return response()->json([
                'status' => true,
                'message' => 'Audio history successfully retrieved',
                'data'   => $audioHistory,
            ], 200);
        } else {
            return response()->json([
                'status'   => false,
                'message'  => 'Unauthorized data fetching...',
            ], 403);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AudioHistory $audioHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AudioHistory $audioHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, AudioHistory $audioHistory)
    {
        if ($request->header('id') == $audioHistory->user_id) {
            $audioHistory->delete();
            return response()->json([
                'status' => true,
                'message' => 'Audio history successfully removed',
                'data'   => $audioHistory,
            ], 200);
        } else {
            return response()->json([
                'status'   => false,
                'message'  => 'Unauthorized access',
            ], 403);
        }
    }

    /**
     * Display a listing of the complete resource.
     */
    public function complete(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'Audio history successfully retrieved',
            'data'   => AudioHistory::where('user_id', $request->header('id'))->where('status', 'finish')->get(),
        ], 200);
    }

    /**
     * Display a listing of the continoue resource.
     */
    public function continoue(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'Audio history successfully retrieved',
            'data'   => AudioHistory::where('user_id', $request->header('id'))->where('status', 'start')->get(),
        ], 200);
    }
}
