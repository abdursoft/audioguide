<?php

namespace App\Http\Controllers;

use App\Models\Browser;
use Illuminate\Http\Request;
use Jenssegers\Agent\Facades\Agent;

class DeviceController extends Controller
{
    /**
     * Device storage
     */
    public function device(Request $request)
    {
        $browser = Agent::browser();
        $version = Agent::version($browser);
        $os = Agent::platform();

        Browser::create([
            'os' => $os,
            'ip' => $request->server('REMOTE_ADDR'),
            'uri' => $request->server('SCRIPT_URI') ?? $request->server('REQUEST_URI'),
            'month' => date('M'),
            'year' => date('Y'),
            'method' => $request->server('REQUEST_METHOD'),
            'browser' => $browser,
            'version' => $version,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Device information successfully saved'
        ], 200);
    }

    /**
     * monthly report
     */
    public function report()
    {
        $report = Browser::where('year', date('Y'))->orderBy('id','desc')
            ->get()
            ->groupBy('month');
        return $report;
    }
}
