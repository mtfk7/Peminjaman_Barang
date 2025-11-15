<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MahasiswaAuthController extends Controller
{
    // Tampilkan halaman register
    public function showRegister()
    {
        if (Auth::guard('mahasiswa')->check()) {
            return redirect()->route('mahasiswa.index');
        }
        
        return view('mahasiswa-auth.register');
    }

    // Proses register
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nim' => 'required|string|unique:mahasiswas,nim|max:50',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:mahasiswas,email',
            'no_telp' => 'required|string|max:20',
            'jurusan' => 'nullable|string|max:255',
            'prodi' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'nim.unique' => 'NIM sudah terdaftar',
            'email.unique' => 'Email sudah terdaftar',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        $mahasiswa = Mahasiswa::create([
            'nim' => $validated['nim'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'no_telp' => $validated['no_telp'],
            'jurusan' => $validated['jurusan'] ?? null,
            'prodi' => $validated['prodi'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        // Redirect ke halaman login setelah register berhasil
        return redirect()->route('mahasiswa.login')
            ->with('success', 'Pendaftaran berhasil! Silakan login dengan NIM dan password Anda.');
    }

    // Tampilkan halaman login
    public function showLogin()
    {
        if (Auth::guard('mahasiswa')->check()) {
            return redirect()->route('mahasiswa.index');
        }
        
        return view('mahasiswa-auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nim' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('mahasiswa')->attempt($credentials, $request->filled('remember'))) {
            $mahasiswa = Auth::guard('mahasiswa')->user();
            
            // Regenerate session ID untuk security (prevent session fixation)
            // Session sudah terpisah dengan cookie berbeda, jadi tidak perlu preserve admin session
            $request->session()->regenerate();
            
            // Regenerate CSRF token setelah regenerate session
            $request->session()->regenerateToken();
            
            // Pastikan session tersimpan dengan key yang spesifik untuk mahasiswa
            $request->session()->put('mahasiswa_id', $mahasiswa->id);
            $request->session()->put('mahasiswa_authenticated', true);
            
            // Simpan session untuk memastikan data tersimpan
            $request->session()->save();
            
            // Redirect dengan session yang sudah di-regenerate
            return redirect()->intended(route('mahasiswa.index'))
                ->with('success', 'Selamat datang, ' . $mahasiswa->nama_lengkap . '!');
        }

        return back()->withErrors([
            'nim' => 'NIM atau password salah.',
        ])->onlyInput('nim');
    }

    // Proses logout
    public function logout(Request $request)
    {
        // Logout dari guard mahasiswa (hanya menghapus data auth mahasiswa)
        Auth::guard('mahasiswa')->logout();
        
        // Hapus session data mahasiswa saja (jangan invalidate seluruh session)
        $request->session()->forget('mahasiswa_id');
        $request->session()->forget('mahasiswa_authenticated');
        $request->session()->forget('login_mahasiswa_' . sha1('mahasiswa'));

        // Regenerate CSRF token untuk security (tidak regenerate session ID)
        $request->session()->regenerateToken();

        return redirect()->route('mahasiswa.login')
            ->with('success', 'Anda telah logout.');
    }

    // Halaman profile mahasiswa
    public function profile()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        return view('mahasiswa-auth.profile', compact('mahasiswa'));
    }

    // Update profile
    public function updateProfile(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:mahasiswas,email,' . $mahasiswa->id,
            'no_telp' => 'required|string|max:20',
            'jurusan' => 'nullable|string|max:255',
            'prodi' => 'nullable|string|max:255',
        ]);

        $mahasiswa->update($validated);

        return back()->with('success', 'Profile berhasil diperbarui!');
    }

    // Change password
    public function changePassword(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $mahasiswa->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak cocok']);
        }

        $mahasiswa->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return back()->with('success', 'Password berhasil diubah!');
    }
}
