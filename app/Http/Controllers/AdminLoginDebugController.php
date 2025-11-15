<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AdminLoginDebugController extends Controller
{
    public function testGet(Request $request)
    {
        Log::info('Admin Login GET Request', [
            'path' => $request->path(),
            'session_id' => session()->getId(),
            'session_name' => session()->getName(),
            'csrf_token' => csrf_token(),
            'cookies' => array_keys($request->cookies->all()),
        ]);
        
        return response()->json([
            'status' => 'GET Request Received',
            'session_id' => session()->getId(),
            'session_name' => session()->getName(),
            'csrf_token' => csrf_token(),
            'cookies' => array_keys($request->cookies->all()),
            'session_data' => session()->all(),
        ]);
    }
    
    public function testPost(Request $request)
    {
        Log::info('Admin Login POST Request', [
            'path' => $request->path(),
            'session_id' => session()->getId(),
            'session_name' => session()->getName(),
            'csrf_token' => csrf_token(),
            'request_csrf' => $request->input('_token'),
            'cookies' => array_keys($request->cookies->all()),
        ]);
        
        return response()->json([
            'status' => 'POST Request Received',
            'session_id' => session()->getId(),
            'session_name' => session()->getName(),
            'csrf_token' => csrf_token(),
            'request_csrf' => $request->input('_token'),
            'csrf_match' => csrf_token() === $request->input('_token'),
            'cookies' => array_keys($request->cookies->all()),
            'session_data' => session()->all(),
        ]);
    }
}


