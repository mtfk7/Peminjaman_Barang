<?php

namespace App\Http\Middleware;

use Illuminate\Session\Middleware\StartSession as BaseStartSession;
use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\Log;

/**
 * Smart StartSession Middleware
 * Memisahkan session cookie berdasarkan route:
 * - Admin routes → SKIP (gunakan middleware di Filament)
 * - Mahasiswa routes → mhs_session
 * - Route lainnya → laravel_session (default)
 */
class SmartStartSession extends BaseStartSession
{
    /**
     * Handle an incoming request.
     * Skip untuk admin routes karena Filament sudah menggunakan middleware sendiri
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $path = $request->path();
        $method = $request->method();
        
        // Untuk admin routes, skip karena Filament sudah menggunakan AdminSessionCookie
        // yang akan start session dengan cookie admin_session
        // TAPI kita tetap perlu start session untuk ShareErrorsFromSession middleware
        // yang berjalan di web middleware group SEBELUM Filament middleware
        // CATATAN: Double session start bisa menyebabkan session data ter-overwrite
        // Jadi kita perlu memastikan session sudah di-start dengan benar
        if ($this->isAdminRoute($request)) {
            // Set cookie name untuk admin
            config(['session.cookie' => 'admin_session']);
            
            // Log untuk debugging
            Log::info('SmartStartSession: Admin route detected', [
                'path' => $path,
                'method' => $method,
                'cookie_admin_session' => $request->cookie('admin_session') ? 'exists' : 'missing',
                'session_id_before' => $request->hasSession() ? $request->session()->getId() : 'no_session',
                'has_session_before' => $request->hasSession(),
            ]);
            
            // Start session dengan cookie admin_session
            // Catatan: AdminSessionCookie di Filament akan check apakah session sudah di-start
            // dan akan skip jika sudah, jadi tidak akan terjadi double session start
            // Tapi kita tetap perlu start session di sini untuk ShareErrorsFromSession middleware
            $response = parent::handle($request, $next);
            
            // Log setelah session start
            if ($request->hasSession()) {
                $session = $request->session();
                Log::info('SmartStartSession: Session started for admin route', [
                    'path' => $path,
                    'session_id' => $session->getId(),
                    'session_name' => $session->getName(),
                    'is_authenticated' => auth('web')->check(),
                    'user_id' => auth('web')->check() ? auth('web')->id() : null,
                    'session_data_keys' => array_keys($session->all()),
                    'login_web_key' => 'login_web_' . sha1('web'),
                    'has_login_web' => $session->has('login_web_' . sha1('web')),
                ]);
            }
            
            return $response;
        }
        
        // Untuk Livewire requests dari admin, kita perlu start session
        // karena Livewire requests tidak melalui Filament middleware
        // Check ini HARUS dilakukan SEBELUM check admin routes lainnya
        if (($path === 'livewire/update' || str_starts_with($path, 'livewire/')) && $request->cookie('admin_session')) {
            // Set cookie name untuk admin
            config(['session.cookie' => 'admin_session']);
            
            // Log untuk debugging
            Log::info('SmartStartSession: Livewire request from admin detected', [
                'path' => $path,
                'method' => $method,
                'cookie_admin_session' => 'exists',
            ]);
            
            // Start session dengan cookie admin_session
            return parent::handle($request, $next);
        }
        
        // Check isLivewireFromAdmin untuk fallback
        if ($this->isLivewireFromAdmin($request)) {
            // Set cookie name untuk admin
            config(['session.cookie' => 'admin_session']);
            
            // Log untuk debugging
            Log::info('SmartStartSession: Livewire from admin (fallback)', [
                'path' => $path,
                'method' => $method,
            ]);
            
            // Start session dengan cookie admin_session
            return parent::handle($request, $next);
        }
        
        // Tentukan cookie name berdasarkan route
        $cookieName = $this->getCookieName($request);
        config(['session.cookie' => $cookieName]);
        
        // Untuk route lainnya, start session dengan cookie yang sesuai
        return parent::handle($request, $next);
    }
    
    /**
     * Get cookie name based on route
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function getCookieName($request)
    {
        $path = $request->path();
        
        // PRIORITY 1: Check untuk Livewire requests dari admin context
        // Ini harus di-check pertama karena Livewire requests tidak memiliki path 'admin'
        if ($path === 'livewire/update' || str_starts_with($path, 'livewire/')) {
            // PRIORITY 1.1: Check jika ada cookie admin_session (indikasi user sedang di admin)
            // Ini adalah check yang paling reliable
            if ($request->cookie('admin_session')) {
                return 'admin_session';
            }
            
            // PRIORITY 1.2: Check referer untuk menentukan context
            $referer = $request->header('referer');
            if ($referer && str_contains($referer, '/admin')) {
                return 'admin_session';
            }
            
            // PRIORITY 1.3: Check X-Livewire-Component header
            $livewireComponent = $request->header('X-Livewire-Component');
            if ($livewireComponent && str_contains($livewireComponent, 'Filament')) {
                return 'admin_session';
            }
            
            // PRIORITY 1.4: Check Origin header
            $origin = $request->header('origin');
            if ($origin && str_contains($origin, '/admin')) {
                return 'admin_session';
            }
        }
        
        // PRIORITY 2: Jika request untuk admin routes, gunakan admin session
        if ($this->isAdminRoute($request)) {
            return 'admin_session';
        }
        
        // PRIORITY 3: Jika request untuk mahasiswa routes, gunakan cookie mahasiswa
        if ($request->is('mahasiswa') || $request->is('mahasiswa/*')) {
            return 'mhs_session';
        }
        
        // Route lainnya menggunakan default
        return config('session.cookie', 'laravel_session');
    }
    
    /**
     * Check if Livewire request is from admin context
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isLivewireFromAdmin($request)
    {
        $path = $request->path();
        
        // Check jika path adalah livewire/update atau livewire/*
        if ($path !== 'livewire/update' && !str_starts_with($path, 'livewire/')) {
            return false;
        }
        
        // PRIORITY 1: Check jika ada cookie admin_session (indikasi user sedang di admin)
        // Ini adalah check yang paling reliable
        if ($request->cookie('admin_session')) {
            return true;
        }
        
        // PRIORITY 2: Check referer untuk menentukan context
        $referer = $request->header('referer');
        if ($referer && str_contains($referer, '/admin')) {
            return true;
        }
        
        // PRIORITY 3: Check X-Livewire-Component header
        $livewireComponent = $request->header('X-Livewire-Component');
        if ($livewireComponent && str_contains($livewireComponent, 'Filament')) {
            return true;
        }
        
        // PRIORITY 4: Check Origin header
        $origin = $request->header('origin');
        if ($origin && str_contains($origin, '/admin')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if request is for admin routes
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isAdminRoute($request)
    {
        $path = $request->path();
        $url = $request->url();
        
        // Check jika path dimulai dengan 'admin'
        if (str_starts_with($path, 'admin')) {
            return true;
        }
        
        // Check jika URL mengandung '/admin'
        if (str_contains($url, '/admin')) {
            return true;
        }
        
        // Check dengan is() method
        if ($request->is('admin') || $request->is('admin/*')) {
            return true;
        }
        
        // Check untuk Livewire requests dari admin (jika referer adalah admin)
        $referer = $request->header('referer');
        if ($referer && str_contains($referer, '/admin')) {
            return true;
        }
        
        // Check untuk Livewire update requests (biasanya dari admin jika ada cookie admin_session)
        // IMPORTANT: Check ini harus dilakukan untuk semua livewire/* paths, bukan hanya livewire/update
        if (str_starts_with($path, 'livewire/') && $request->cookie('admin_session')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get the session configuration to use.
     * Override untuk set cookie name berdasarkan route
     *
     * @return array
     */
    protected function getSessionConfiguration()
    {
        $config = parent::getSessionConfiguration();
        
        $request = request();
        $path = $request->path();
        
        // PRIORITY 1: Check untuk Livewire requests dari admin context
        if ($path === 'livewire/update' || str_starts_with($path, 'livewire/')) {
            // PRIORITY 1.1: Check jika ada cookie admin_session (indikasi user sedang di admin)
            // Ini adalah check yang paling reliable
            if ($request->cookie('admin_session')) {
                $config['cookie'] = 'admin_session';
                return $config;
            }
            
            // PRIORITY 1.2: Check referer untuk menentukan context
            $referer = $request->header('referer');
            if ($referer && str_contains($referer, '/admin')) {
                $config['cookie'] = 'admin_session';
                return $config;
            }
            
            // PRIORITY 1.3: Check X-Livewire-Component header
            $livewireComponent = $request->header('X-Livewire-Component');
            if ($livewireComponent && str_contains($livewireComponent, 'Filament')) {
                $config['cookie'] = 'admin_session';
                return $config;
            }
            
            // PRIORITY 1.4: Check Origin header
            $origin = $request->header('origin');
            if ($origin && str_contains($origin, '/admin')) {
                $config['cookie'] = 'admin_session';
                return $config;
            }
        }
        
        // PRIORITY 2: Jika request untuk admin routes, gunakan admin session
        if ($this->isAdminRoute($request)) {
            $config['cookie'] = 'admin_session';
        } elseif ($request->is('mahasiswa') || $request->is('mahasiswa/*')) {
            // PRIORITY 3: Jika request untuk mahasiswa routes, gunakan cookie mahasiswa
            $config['cookie'] = 'mhs_session';
        } else {
            // Route lainnya menggunakan default
            $config['cookie'] = config('session.cookie', 'laravel_session');
        }
        
        return $config;
    }
}
