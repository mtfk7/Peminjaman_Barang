<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class SessionDebugController extends Controller
{
    public function index(Request $request)
    {
        // Log untuk debugging
        Log::info('Session Debug Accessed', [
            'path' => $request->path(),
            'method' => $request->method(),
            'session_id' => session()->getId(),
            'session_name' => session()->getName(),
            'cookies' => array_keys($request->cookies->all()),
        ]);
        
        $data = [
            'current_route' => $request->path(),
            'is_admin_route' => $request->is('admin') || $request->is('admin/*'),
            'is_mahasiswa_route' => $request->is('mahasiswa') || $request->is('mahasiswa/*'),
            'session_config' => [
                'driver' => config('session.driver'),
                'cookie' => config('session.cookie'),
                'cookie_admin' => config('session.cookie_admin'),
                'cookie_mahasiswa' => config('session.cookie_mahasiswa'),
                'lifetime' => config('session.lifetime'),
            ],
            'session_id' => session()->getId(),
            'session_name' => session()->getName(),
            'session_data' => session()->all(),
            'cookies' => $request->cookies->all(),
            'middleware' => $this->getMiddlewareInfo($request),
            'session_files' => $this->getSessionFiles(),
            'expected_cookie' => $this->getExpectedCookie($request),
            'csrf_token' => csrf_token(),
            'csrf_token_from_session' => session()->token(),
            'request_method' => $request->method(),
            'request_url' => $request->url(),
            'request_path' => $request->path(),
        ];

        return view('debug.session', $data);
    }
    
    private function getExpectedCookie($request)
    {
        if ($request->is('admin') || $request->is('admin/*')) {
            return 'admin_session';
        }
        
        if ($request->is('mahasiswa') || $request->is('mahasiswa/*')) {
            return 'mhs_session';
        }
        
        return 'laravel_session';
    }

    private function getMiddlewareInfo(Request $request)
    {
        $middleware = [];
        
        try {
            $route = Route::current();
            if ($route) {
                $middleware['route_middleware'] = $route->middleware();
            }
        } catch (\Exception $e) {
            $middleware['route_middleware'] = 'Error: ' . $e->getMessage();
        }

        $middleware['web_middleware'] = app('router')->getMiddlewareGroups()['web'] ?? [];
        
        return $middleware;
    }

    private function getSessionFiles()
    {
        $files = [];
        
        if (config('session.driver') === 'file') {
            $path = storage_path('framework/sessions');
            if (is_dir($path)) {
                $files = array_slice(scandir($path), 2);
                $files = array_filter($files, function($file) {
                    return strpos($file, '.') !== 0;
                });
                $files = array_slice($files, 0, 10); // Limit to 10 files
            }
        }
        
        return $files;
    }
}
