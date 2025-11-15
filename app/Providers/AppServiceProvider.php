<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Auth;
use App\Models\Peminjaman;
use App\Observers\PeminjamanObserver;
use App\Models\BarangMasuk;
use App\Observers\BarangMasukObserver;
use App\Models\PeminjamanMahasiswa;
use App\Observers\PeminjamanMahasiswaObserver;
use App\Models\PeminjamanMahasiswaItem;
use App\Observers\PeminjamanMahasiswaItemObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        Peminjaman::observe(PeminjamanObserver::class);
        BarangMasuk::observe(BarangMasukObserver::class);
        PeminjamanMahasiswa::observe(PeminjamanMahasiswaObserver::class);
        PeminjamanMahasiswaItem::observe(PeminjamanMahasiswaItemObserver::class);
        
        // Listen to Logout event untuk memastikan hanya logout guard yang sesuai
        Event::listen(Logout::class, function (Logout $event) {
            \Illuminate\Support\Facades\Log::info('Logout event triggered', [
                'guard' => $event->guard,
                'user_id' => $event->user ? $event->user->id : null,
                'path' => request()->path(),
            ]);
            
            // Jika logout dari guard 'web' (admin), preserve session mahasiswa
            if ($event->guard === 'web') {
                // Simpan status login mahasiswa sebelum logout admin
                $mahasiswaLoggedIn = Auth::guard('mahasiswa')->check();
                $mahasiswaSessionData = null;
                
                if ($mahasiswaLoggedIn) {
                    // Simpan data session mahasiswa sementara
                    $mahasiswaSessionData = [
                        'mahasiswa_id' => session()->get('mahasiswa_id'),
                        'mahasiswa_authenticated' => session()->get('mahasiswa_authenticated'),
                        'login_mahasiswa_key' => 'login_mahasiswa_' . sha1('mahasiswa'),
                        'login_mahasiswa_value' => session()->get('login_mahasiswa_' . sha1('mahasiswa')),
                    ];
                }
                
                // Hapus session data admin saja (jangan invalidate seluruh session)
                session()->forget('login_web_' . sha1('web'));
                
                // Restore session data mahasiswa jika ada
                if ($mahasiswaLoggedIn && $mahasiswaSessionData) {
                    session()->put('mahasiswa_id', $mahasiswaSessionData['mahasiswa_id']);
                    session()->put('mahasiswa_authenticated', $mahasiswaSessionData['mahasiswa_authenticated']);
                    if ($mahasiswaSessionData['login_mahasiswa_value']) {
                        session()->put($mahasiswaSessionData['login_mahasiswa_key'], $mahasiswaSessionData['login_mahasiswa_value']);
                    }
                }
                
                // Regenerate CSRF token saja, jangan regenerate session ID
                session()->regenerateToken();
            }
            // Jika logout dari guard 'mahasiswa', biarkan default behavior
        });
    }
}
