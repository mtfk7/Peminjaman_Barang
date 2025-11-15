<?php

namespace App\Http\Middleware;

use Illuminate\Session\Middleware\StartSession as BaseStartSession;
use Illuminate\Http\Request;
use Closure;

/**
 * Middleware untuk Mahasiswa Session Cookie
 * Menggunakan cookie name: mhs_session
 */
class MahasiswaSessionCookie extends BaseStartSession
{
    /**
     * Get the session configuration to use.
     *
     * @return array
     */
    protected function getSessionConfiguration()
    {
        $config = parent::getSessionConfiguration();
        
        // Override cookie name dengan mhs_session
        // Ini dipanggil sebelum session manager di-instantiate
        $config['cookie'] = config('session.cookie_mahasiswa', 'mhs_session');
        
        return $config;
    }
}
