<?php

use App\Models\BarangTidakHabisPakai;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\QRScannerController;
use App\Http\Controllers\SessionDebugController;
use App\Http\Controllers\AdminLoginDebugController;

// Redirect root ke panel admin Filament
Route::get('/', function () {
    return redirect('/admin');
});

// ========================================
// ROUTES DEBUG SESSION
// ========================================
Route::get('/debug/session', [SessionDebugController::class, 'index'])->name('debug.session');
Route::get('/debug/session/clear', function () {
    session()->flush();
    return redirect()->route('debug.session')->with('success', 'Session cleared!');
})->name('debug.session.clear');

// Debug session khusus untuk admin routes (akan menggunakan AdminSessionCookie)
Route::get('/admin/debug/session', [SessionDebugController::class, 'index'])->name('admin.debug.session');

// Debug endpoint untuk test CSRF token
Route::get('/admin/debug/login-test', [AdminLoginDebugController::class, 'testGet'])->name('admin.debug.login.get');
Route::post('/admin/debug/login-test', [AdminLoginDebugController::class, 'testPost'])->name('admin.debug.login.post');

// Handle GET request untuk admin/logout (redirect ke login)
Route::get('/admin/logout', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('admin.logout.get');

// Custom logout handler untuk admin yang tidak mempengaruhi session mahasiswa
Route::post('/admin/logout-custom', function (\Illuminate\Http\Request $request) {
    // Simpan status login mahasiswa sebelum logout admin
    $mahasiswaLoggedIn = \Illuminate\Support\Facades\Auth::guard('mahasiswa')->check();
    $mahasiswaSessionData = null;
    
    if ($mahasiswaLoggedIn) {
        // Simpan data session mahasiswa sementara
        $mahasiswaSessionData = [
            'mahasiswa_id' => $request->session()->get('mahasiswa_id'),
            'mahasiswa_authenticated' => $request->session()->get('mahasiswa_authenticated'),
            'login_mahasiswa_key' => 'login_mahasiswa_' . sha1('mahasiswa'),
            'login_mahasiswa_value' => $request->session()->get('login_mahasiswa_' . sha1('mahasiswa')),
        ];
    }
    
    // Logout hanya dari guard web (admin)
    \Illuminate\Support\Facades\Auth::guard('web')->logout();
    
    // Hapus session data admin saja
    $request->session()->forget('login_web_' . sha1('web'));
    
    // Restore session data mahasiswa jika ada
    if ($mahasiswaLoggedIn && $mahasiswaSessionData) {
        $request->session()->put('mahasiswa_id', $mahasiswaSessionData['mahasiswa_id']);
        $request->session()->put('mahasiswa_authenticated', $mahasiswaSessionData['mahasiswa_authenticated']);
        if ($mahasiswaSessionData['login_mahasiswa_value']) {
            $request->session()->put($mahasiswaSessionData['login_mahasiswa_key'], $mahasiswaSessionData['login_mahasiswa_value']);
        }
    }
    
    // Regenerate CSRF token saja, jangan regenerate session ID
    $request->session()->regenerateToken();
    
    return redirect()->route('filament.admin.auth.login');
})->middleware('auth')->name('admin.logout.custom');

Route::get('/laporan/barang-tidak/pdf', function () {
    $data = BarangTidakHabisPakai::all();
    $pdf = Pdf::loadView('exports.laporan-barang-tidak-habis', compact('data'));
    return $pdf->download('laporan-barang-tidak-habis.pdf');
})->name('laporan.barang.tidak.pdf');

// ========================================
// ROUTES AUTH MAHASISWA
// ========================================
use App\Http\Controllers\MahasiswaAuthController;

Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    // Public routes (guest only)
    Route::middleware('guest:mahasiswa')->group(function () {
        Route::get('/register', [MahasiswaAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [MahasiswaAuthController::class, 'register'])->name('register.store');
        Route::get('/login', [MahasiswaAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [MahasiswaAuthController::class, 'login'])->name('login.store');
    });

    // Protected routes (auth mahasiswa)
    Route::middleware('auth:mahasiswa')->group(function () {
        Route::post('/logout', [MahasiswaAuthController::class, 'logout'])->name('logout');
        Route::get('/profile', [MahasiswaAuthController::class, 'profile'])->name('profile');
        Route::put('/profile', [MahasiswaAuthController::class, 'updateProfile'])->name('profile.update');
        Route::post('/change-password', [MahasiswaAuthController::class, 'changePassword'])->name('password.change');
        
        // Peminjaman routes
        Route::get('/', [MahasiswaController::class, 'index'])->name('index');
        Route::post('/add-to-cart', [MahasiswaController::class, 'addToCart'])->name('add-to-cart');
        Route::post('/remove-from-cart', [MahasiswaController::class, 'removeFromCart'])->name('remove-from-cart');
        Route::post('/clear-cart', [MahasiswaController::class, 'clearCart'])->name('clear-cart');
        Route::get('/pinjam', [MahasiswaController::class, 'create'])->name('create');
        Route::post('/pinjam', [MahasiswaController::class, 'store'])->name('store');
        Route::get('/peminjaman/{id}', [MahasiswaController::class, 'show'])->name('show');
        Route::get('/history', [MahasiswaController::class, 'history'])->name('history');
    });
    
    // API Public (untuk QR verification)
    Route::get('/verify/{qrCode}', [MahasiswaController::class, 'verify'])->name('verify');
});

// ========================================
// ROUTES ADMIN QR SCANNER
// ========================================
Route::middleware(['auth'])->prefix('admin/qr-scanner')->name('qr.')->group(function () {
    Route::get('/', [QRScannerController::class, 'index'])->name('scanner');
    Route::get('/detail/{qrCode}', [QRScannerController::class, 'detail'])->name('detail');
    Route::post('/approve/{id}', [QRScannerController::class, 'approve'])->name('approve');
    Route::post('/reject/{id}', [QRScannerController::class, 'reject'])->name('reject');
    Route::post('/scan', [QRScannerController::class, 'scan'])->name('scan');
});

