<?php

namespace App\Http\Middleware;

use Illuminate\Session\Middleware\StartSession as BaseStartSession;
use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\Log;

/**
 * Middleware untuk Admin Session Cookie
 * Menggunakan cookie name: admin_session
 * Hanya digunakan untuk admin routes (Filament)
 */
class AdminSessionCookie extends BaseStartSession
{
    /**
     * Handle an incoming request.
     * Override untuk memastikan cookie name di-set sebelum session dimulai
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $path = $request->path();
        $method = $request->method();
        
        // Set cookie name SEBELUM parent::handle() dipanggil
        // Ini memastikan session manager menggunakan cookie yang benar dari awal
        config(['session.cookie' => 'admin_session']);
        
        // Log untuk debugging
        Log::info('AdminSessionCookie: Starting session', [
            'path' => $path,
            'method' => $method,
            'cookie_admin_session' => $request->cookie('admin_session') ? 'exists' : 'missing',
            'session_id_before' => $request->hasSession() ? $request->session()->getId() : 'no_session',
        ]);
        
        // Check jika session sudah di-start (oleh SmartStartSession di web middleware group)
        // Jika sudah, jangan start lagi untuk menghindari double session start yang bisa menyebabkan
        // session data ter-overwrite
        if ($request->hasSession()) {
            $session = $request->session();
            Log::info('AdminSessionCookie: Session already started, skipping', [
                'path' => $path,
                'session_id' => $session->getId(),
                'session_name' => $session->getName(),
                'is_authenticated' => auth('web')->check(),
                'user_id' => auth('web')->check() ? auth('web')->id() : null,
                'session_data_keys' => array_keys($session->all()),
                'login_web_key' => 'login_web_' . sha1('web'),
                'has_login_web' => $session->has('login_web_' . sha1('web')),
            ]);
            return $next($request);
        }
        
        // Panggil parent::handle() untuk start session dengan cookie yang benar
        $response = parent::handle($request, $next);
        
        // Log setelah session start
        if ($request->hasSession()) {
            $session = $request->session();
            Log::info('AdminSessionCookie: Session started', [
                'path' => $path,
                'session_id' => $session->getId(),
                'session_name' => $session->getName(),
                'is_authenticated' => auth('web')->check(),
                'user_id' => auth('web')->check() ? auth('web')->id() : null,
                'session_data_keys' => array_keys($session->all()),
                'login_web_key' => 'login_web_' . sha1('web'),
                'has_login_web' => $session->has('login_web_' . sha1('web')),
            ]);
        } else {
            Log::warning('AdminSessionCookie: Session not started', [
                'path' => $path,
            ]);
        }
        
        return $response;
    }
    
    /**
     * Get the session configuration to use.
     * Override untuk set cookie name menjadi admin_session
     *
     * @return array
     */
    protected function getSessionConfiguration()
    {
        $config = parent::getSessionConfiguration();
        
        // Set cookie name untuk admin
        $config['cookie'] = 'admin_session';
        
        // Pastikan session lifetime cukup panjang (24 jam)
        $config['lifetime'] = config('session.lifetime', 1440);
        
        // Pastikan session tidak expire on close
        $config['expire_on_close'] = false;
        
        return $config;
    }
}
