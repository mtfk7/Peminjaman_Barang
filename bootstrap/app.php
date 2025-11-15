<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Replace default StartSession dengan SmartStartSession untuk memisahkan session
        $middleware->web(replace: [
            \Illuminate\Session\Middleware\StartSession::class => \App\Http\Middleware\SmartStartSession::class,
        ]);
        
        // Redirect guests to the appropriate login page based on guard
        // IMPORTANT: Jangan redirect untuk admin routes karena Filament sudah handle sendiri
        $middleware->redirectGuestsTo(function ($request) {
            // Skip untuk AJAX requests (biarkan SPA navigation handle sendiri)
            if ($request->ajax() || $request->wantsJson()) {
                return null;
            }
            
            // CRITICAL: Skip SEMUA admin routes - Filament sudah handle authentication sendiri
            // Jangan ganggu Filament dengan redirectGuestsTo
            if ($request->is('admin') || $request->is('admin/*')) {
                return null; // Biarkan Filament handle sendiri
            }
            
            // Skip untuk route login, register, dan logout (biarkan handle sendiri)
            if ($request->is('mahasiswa/login') || 
                $request->is('mahasiswa/register') ||
                $request->is('mahasiswa/logout')) {
                return null;
            }
            
            // Skip untuk root route (akan dihandle oleh route sendiri)
            if ($request->is('/')) {
                return null;
            }
            
            // Jika request untuk mahasiswa routes, cek guard mahasiswa
            if ($request->is('mahasiswa') || $request->is('mahasiswa/*')) {
                // Hanya redirect jika TIDAK login sebagai mahasiswa
                if (!auth()->guard('mahasiswa')->check()) {
                    return route('mahasiswa.login');
                }
                // Jika sudah login sebagai mahasiswa, jangan redirect
                return null;
            }
            
            // Untuk route lainnya, tidak redirect
            return null;
        });
        
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle unauthenticated exception
        $exceptions->render(function (AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            
            // Redirect berdasarkan route yang diminta
            if ($request->is('admin') || $request->is('admin/*')) {
                return redirect()->route('filament.admin.auth.login');
            }
            
            if ($request->is('mahasiswa') || $request->is('mahasiswa/*')) {
                return redirect()->route('mahasiswa.login');
            }
            
            // Default redirect ke admin login
            return redirect()->route('filament.admin.auth.login');
        });
    })->create();
