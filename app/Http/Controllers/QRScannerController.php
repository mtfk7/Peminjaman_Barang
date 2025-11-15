<?php

namespace App\Http\Controllers;

use App\Models\PeminjamanMahasiswa;
use App\Models\PeminjamanMahasiswaItem;
use App\Models\BarangHabisPakai;
use App\Models\BarangTidakHabisPakai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QRScannerController extends Controller
{
    // Halaman scanner QR untuk admin
    public function index()
    {
        // List peminjaman pending (berdasarkan status_keseluruhan)
        $pendingPeminjaman = PeminjamanMahasiswa::with('items')
            ->whereHas('items', function($query) {
                $query->where('status', 'pending');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function($peminjaman) {
                return $peminjaman->status_keseluruhan === 'pending';
            });
        
        return view('admin-qr.scanner', compact('pendingPeminjaman'));
    }

    // Detail peminjaman untuk approval
    public function detail($qrCode)
    {
        $peminjaman = PeminjamanMahasiswa::with('items')
            ->where('qr_code', $qrCode)
            ->firstOrFail();
        
        return view('admin-qr.detail', compact('peminjaman'));
    }

    // Approve peminjaman (semua items sekaligus)
    public function approve(Request $request, $id)
    {
        $peminjaman = PeminjamanMahasiswa::with('items')->findOrFail($id);
        
        if ($peminjaman->status_keseluruhan !== 'pending') {
            return back()->with('error', 'Peminjaman ini sudah diproses sebelumnya.');
        }

        // Validasi stok untuk semua items
        $itemsToApprove = $peminjaman->items->where('status', 'pending');
        
        foreach ($itemsToApprove as $item) {
            if ($item->jenis_barang === 'habis_pakai') {
                $barang = BarangHabisPakai::find($item->barang_id);
                if (!$barang) {
                    return back()->with('error', "Barang {$item->nama_barang} tidak ditemukan.");
                }
                
                if ($barang->total_stok < $item->jumlah) {
                    return back()->with('error', "Stok tidak mencukupi untuk {$item->nama_barang}. Stok tersedia: {$barang->total_stok} {$barang->satuan}");
                }
            } else {
                $barang = BarangTidakHabisPakai::find($item->barang_id);
                if (!$barang) {
                    return back()->with('error', "Barang {$item->nama_barang} tidak ditemukan.");
                }
                
                if ($barang->total_stok < $item->jumlah) {
                    return back()->with('error', "Stok tidak mencukupi untuk {$item->nama_barang}. Stok tersedia: {$barang->total_stok} {$barang->satuan}");
                }
            }
        }

        // Update status semua items (Observer akan handle pengurangan stok)
        DB::beginTransaction();
        try {
            foreach ($itemsToApprove as $item) {
                $item->update([
                    'status' => 'approved',
                ]);
            }

            // Update header
            $peminjaman->update([
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('qr.scanner')
                ->with('success', 'Peminjaman berhasil disetujui! Stok barang telah dikurangi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Reject peminjaman (semua items sekaligus)
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $peminjaman = PeminjamanMahasiswa::with('items')->findOrFail($id);
        
        if ($peminjaman->status_keseluruhan !== 'pending') {
            return back()->with('error', 'Peminjaman ini sudah diproses sebelumnya.');
        }

        // Update status semua items menjadi rejected
        DB::beginTransaction();
        try {
            foreach ($peminjaman->items->where('status', 'pending') as $item) {
                $item->update([
                    'status' => 'rejected',
                ]);
            }

            // Update header
            $peminjaman->update([
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            DB::commit();

            return redirect()->route('qr.scanner')
                ->with('success', 'Peminjaman berhasil ditolak.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // API untuk scan QR via mobile/ajax
    public function scan(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $peminjaman = PeminjamanMahasiswa::with('items')
            ->where('qr_code', $request->qr_code)
            ->first();
        
        if (!$peminjaman) {
            return response()->json(['error' => 'QR Code tidak valid'], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $peminjaman->id,
                'nama_mahasiswa' => $peminjaman->nama_mahasiswa,
                'nama_peminjam' => $peminjaman->nama_peminjam,
                'nim' => $peminjaman->nim,
                'email' => $peminjaman->email,
                'no_telp' => $peminjaman->no_telp,
                'tanggal_pinjam' => $peminjaman->tanggal_pinjam->format('d M Y'),
                'jam_pinjam' => $peminjaman->jam_pinjam,
                'mata_kuliah' => $peminjaman->mata_kuliah,
                'kelas' => $peminjaman->kelas,
                'keperluan' => $peminjaman->keperluan,
                'status_keseluruhan' => $peminjaman->status_keseluruhan,
                'qr_code' => $peminjaman->qr_code,
                'items' => $peminjaman->items->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nama_barang' => $item->nama_barang,
                        'jenis_barang' => $item->jenis_barang,
                        'jumlah' => $item->jumlah,
                        'satuan' => $item->satuan,
                        'status' => $item->status,
                    ];
                }),
            ],
            'redirect' => route('qr.detail', $peminjaman->qr_code),
        ]);
    }
}
