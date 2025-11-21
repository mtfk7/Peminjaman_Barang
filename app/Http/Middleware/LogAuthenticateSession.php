<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Filament\Http\Middleware\AuthenticateSession as BaseAuthenticateSession;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk logging AuthenticateSession behavior
 * Digunakan untuk debugging masalah authentication yang hilang
 */
class LogAuthenticateSession extends BaseAuthenticateSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next): Response
    {
        $path = $request->path();
        $method = $request->method();
        
        // Log sebelum AuthenticateSession
        Log::info('LogAuthenticateSession: Before authentication check', [
            'path' => $path,
            'method' => $method,
            'session_id' => $request->hasSession() ? $request->session()->getId() : 'no_session',
            'is_authenticated' => auth('web')->check(),
            'user_id' => auth('web')->check() ? auth('web')->id() : null,
            'cookie_admin_session' => $request->cookie('admin_session') ? 'exists' : 'missing',
        ]);
        
        $response = parent::handle($request, $next);
        
        // Log setelah AuthenticateSession
        Log::info('LogAuthenticateSession: After authentication check', [
            'path' => $path,
            'method' => $method,
            'session_id' => $request->hasSession() ? $request->session()->getId() : 'no_session',
            'is_authenticated' => auth('web')->check(),
            'user_id' => auth('web')->check() ? auth('web')->id() : null,
            'is_redirect' => $response instanceof \Illuminate\Http\RedirectResponse,
            'redirect_to' => $response instanceof \Illuminate\Http\RedirectResponse ? $response->getTargetUrl() : null,
        ]);
        
        return $response;
    }
}




