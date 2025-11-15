<?php

namespace App\Http\Controllers;

use App\Models\BarangHabisPakai;
use App\Models\BarangTidakHabisPakai;
use App\Models\PeminjamanMahasiswa;
use App\Models\PeminjamanMahasiswaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MahasiswaController extends Controller
{
    // Halaman utama - List barang tersedia
    public function index()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $barangHabis = BarangHabisPakai::where('total_stok', '>', 0)->get();
        $barangTidakHabis = BarangTidakHabisPakai::where('total_stok', '>', 0)->get();
        
        // Calculate statistics
        $totalItems = $barangHabis->count() + $barangTidakHabis->count();
        $availableItems = $barangHabis->where('total_stok', '>', 0)->count() + $barangTidakHabis->where('total_stok', '>', 0)->count();
        $consumableItems = $barangHabis->count();
        $nonConsumableItems = $barangTidakHabis->count();
        
        return view('mahasiswa.index', compact('mahasiswa', 'barangHabis', 'barangTidakHabis', 'totalItems', 'availableItems', 'consumableItems', 'nonConsumableItems'));
    }

    // Add item to cart
    public function addToCart(Request $request)
    {
        $request->validate([
            'barang_id' => 'required',
            'jenis_barang' => 'required|in:habis_pakai,tidak_habis_pakai',
            'jumlah' => 'required|integer|min:1',
        ]);

        $jenisBarang = $request->jenis_barang;
        $barangId = $request->barang_id;
        $jumlah = $request->jumlah;

        // Get barang data
        if ($jenisBarang === 'habis_pakai') {
            $barang = BarangHabisPakai::findOrFail($barangId);
        } else {
            $barang = BarangTidakHabisPakai::findOrFail($barangId);
        }

        // Cek stok
        if ($barang->total_stok < $jumlah) {
            return back()->with('error', 'Stok tidak mencukupi!');
        }

        // Get cart from session
        $cart = session()->get('peminjaman_cart', []);

        // Check if item already exists in cart (same barang_id and jenis_barang)
        $itemKey = $jenisBarang . '_' . $barangId;
        if (isset($cart[$itemKey])) {
            // Update jumlah
            $newJumlah = $cart[$itemKey]['jumlah'] + $jumlah;
            if ($barang->total_stok < $newJumlah) {
                return back()->with('error', 'Stok tidak mencukupi untuk menambah jumlah!');
            }
            $cart[$itemKey]['jumlah'] = $newJumlah;
        } else {
            // Add new item
            $cart[$itemKey] = [
                'barang_id' => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'kode_barang' => $barang->kode_barang,
                'jenis_barang' => $jenisBarang,
                'jumlah' => $jumlah,
                'satuan' => $barang->satuan,
                'total_stok' => $barang->total_stok,
            ];
        }

        session()->put('peminjaman_cart', $cart);

        return back()->with('success', 'Barang ditambahkan ke keranjang!');
    }

    // Remove item from cart
    public function removeFromCart(Request $request)
    {
        $itemKey = $request->item_key;
        $cart = session()->get('peminjaman_cart', []);
        
        if (isset($cart[$itemKey])) {
            unset($cart[$itemKey]);
            session()->put('peminjaman_cart', $cart);
            return back()->with('success', 'Barang dihapus dari keranjang!');
        }

        return back()->with('error', 'Item tidak ditemukan!');
    }

    // Clear cart
    public function clearCart()
    {
        session()->forget('peminjaman_cart');
        return back()->with('success', 'Keranjang dikosongkan!');
    }

    // Form peminjaman (dengan cart)
    public function create()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $cart = session()->get('peminjaman_cart', []);
        
        return view('mahasiswa.create', compact('mahasiswa', 'cart'));
    }

    // Simpan peminjaman dan generate QR (dengan multiple items)
    public function store(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        // Validasi form
        $validated = $request->validate([
            'nama_peminjam' => 'required|string|max:255',
            'tanggal_pinjam' => 'required|date',
            'jam_pinjam' => 'required|date_format:H:i',
            'mata_kuliah' => 'nullable|string|max:255',
            'kelas' => 'nullable|string|max:255',
            'keperluan' => 'nullable|string',
        ]);

        // Get cart from session
        $cart = session()->get('peminjaman_cart', []);
        
        if (empty($cart)) {
            return back()->with('error', 'Keranjang kosong! Silakan tambahkan barang terlebih dahulu.');
        }

        // Validasi stok untuk semua items di cart
        foreach ($cart as $itemKey => $item) {
            if ($item['jenis_barang'] === 'habis_pakai') {
                $barang = BarangHabisPakai::find($item['barang_id']);
            } else {
                $barang = BarangTidakHabisPakai::find($item['barang_id']);
            }

            if (!$barang || $barang->total_stok < $item['jumlah']) {
                return back()->with('error', "Stok tidak mencukupi untuk {$item['nama_barang']}!");
            }
        }

        // Mulai transaction
        DB::beginTransaction();
        
        try {
            // Buat header peminjaman
            $peminjaman = PeminjamanMahasiswa::create([
                'mahasiswa_id' => $mahasiswa->id,
                'nama_mahasiswa' => $mahasiswa->nama_lengkap,
                'nama_peminjam' => $validated['nama_peminjam'],
                'nim' => $mahasiswa->nim,
                'email' => $mahasiswa->email,
                'no_telp' => $mahasiswa->no_telp,
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'tanggal_kembali' => null,
                'jam_pinjam' => $validated['jam_pinjam'],
                'jam_kembali' => null,
                'mata_kuliah' => $validated['mata_kuliah'] ?? null,
                'kelas' => $validated['kelas'] ?? null,
                'keperluan' => $validated['keperluan'] ?? null,
                'status' => 'pending', // Status header (akan di-calculate dari items)
            ]);

            // Buat items untuk setiap barang di cart
            foreach ($cart as $itemKey => $item) {
                PeminjamanMahasiswaItem::create([
                    'peminjaman_mahasiswa_id' => $peminjaman->id,
                    'barang_id' => $item['barang_id'],
                    'nama_barang' => $item['nama_barang'],
                    'jenis_barang' => $item['jenis_barang'],
                    'jumlah' => $item['jumlah'],
                    'satuan' => $item['satuan'],
                    'status' => 'pending',
                    'jam_kembali' => null,
                ]);
            }

            DB::commit();

            // Clear cart setelah berhasil
            session()->forget('peminjaman_cart');

            return redirect()->route('mahasiswa.show', $peminjaman->id)
                ->with('success', 'Pengajuan peminjaman berhasil! Silakan tunjukkan QR Code ke admin.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Tampilkan QR Code (dengan multiple items)
    public function show($id)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        // Hanya bisa melihat peminjaman sendiri
        $peminjaman = PeminjamanMahasiswa::with('items')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->findOrFail($id);
        
        // Generate QR Code image
        $qrCode = QrCode::size(300)
            ->format('svg')
            ->generate(route('mahasiswa.verify', $peminjaman->qr_code));
        
        return view('mahasiswa.show', compact('peminjaman', 'qrCode'));
    }

    // Halaman history peminjaman (per mahasiswa yang login)
    public function history()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        $peminjaman = PeminjamanMahasiswa::with('items')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('mahasiswa.history', compact('peminjaman'));
    }

    // API untuk verifikasi QR Code (dengan multiple items)
    public function verify($qrCode)
    {
        $peminjaman = PeminjamanMahasiswa::with('items')
            ->where('qr_code', $qrCode)
            ->first();
        
        if (!$peminjaman) {
            return response()->json(['error' => 'QR Code tidak valid'], 404);
        }
        
        return response()->json([
            'id' => $peminjaman->id,
            'nama_mahasiswa' => $peminjaman->nama_mahasiswa,
            'nama_peminjam' => $peminjaman->nama_peminjam,
            'nim' => $peminjaman->nim,
            'tanggal_pinjam' => $peminjaman->tanggal_pinjam->format('d M Y'),
            'jam_pinjam' => $peminjaman->jam_pinjam,
            'mata_kuliah' => $peminjaman->mata_kuliah,
            'kelas' => $peminjaman->kelas,
            'status_keseluruhan' => $peminjaman->status_keseluruhan,
            'items' => $peminjaman->items->map(function ($item) {
                return [
                    'nama_barang' => $item->nama_barang,
                    'jenis_barang' => $item->jenis_barang,
                    'jumlah' => $item->jumlah,
                    'satuan' => $item->satuan,
                    'status' => $item->status,
                ];
            }),
        ]);
    }
}
