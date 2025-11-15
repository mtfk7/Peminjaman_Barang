<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk logging redirect ke login page
 * Digunakan untuk debugging masalah redirect yang tidak diinginkan
 */
class LogAdminRedirects
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Log jika response adalah redirect ke login
        // Check apakah response adalah RedirectResponse dan request untuk admin routes
        if ($response instanceof RedirectResponse && ($request->is('admin') || $request->is('admin/*'))) {
            $targetUrl = $response->getTargetUrl();
            
            // Check jika redirect ke login page
            if (str_contains($targetUrl, '/admin/login') || str_contains($targetUrl, 'login')) {
                Log::warning('Admin redirect to login detected', [
                    'current_path' => $request->path(),
                    'current_url' => $request->url(),
                    'redirect_to' => $targetUrl,
                    'method' => $request->method(),
                    'session_id' => $request->hasSession() ? $request->session()->getId() : 'no_session',
                    'session_name' => $request->hasSession() ? $request->session()->getName() : 'no_session',
                    'cookie_admin_session' => $request->cookie('admin_session') ? 'exists' : 'missing',
                    'is_authenticated' => auth('web')->check(),
                    'user_id' => auth('web')->check() ? auth('web')->id() : null,
                    'referer' => $request->header('referer'),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }
        
        return $response;
    }
}

