@extends('mahasiswa.layout')

@section('title', 'Status Peminjaman')

@section('content')
<div class="max-w-4xl mx-auto">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">
        <i class="fas fa-search text-purple-600 mr-2"></i>Cek Status Peminjaman
    </h2>

    <!-- Search Form -->
    <div class="bg-white rounded-lg card-shadow p-6 mb-6">
        <form action="{{ route('mahasiswa.status') }}" method="GET" class="flex gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Masukkan NIM Anda
                </label>
                <input type="text" name="nim" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="Contoh: 1234567890" value="{{ $nim }}">
            </div>
            <div class="flex items-end">
                <button type="submit" 
                        class="btn-primary text-white px-6 py-3 rounded-lg font-semibold whitespace-nowrap">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
            </div>
        </form>
    </div>

    <!-- Results -->
    @if($nim)
        @if($peminjaman->isEmpty())
            <div class="bg-white rounded-lg card-shadow p-12 text-center">
                <i class="fas fa-inbox text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Data Peminjaman</h3>
                <p class="text-gray-500">Tidak ditemukan peminjaman dengan NIM tersebut</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($peminjaman as $item)
                <div class="bg-white rounded-lg card-shadow overflow-hidden hover:shadow-xl transition-all">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">{{ $item->nama_barang }}</h3>
                                <p class="text-sm text-gray-600">{{ $item->jumlah }} {{ $item->satuan }}</p>
                            </div>
                            <div>
                                @if($item->status === 'pending')
                                    <span class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-full text-sm font-semibold">
                                        <i class="fas fa-clock mr-1"></i>Pending
                                    </span>
                                @elseif($item->status === 'approved')
                                    <span class="bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-semibold">
                                        <i class="fas fa-check-circle mr-1"></i>Disetujui
                                    </span>
                                @elseif($item->status === 'rejected')
                                    <span class="bg-red-100 text-red-800 px-4 py-2 rounded-full text-sm font-semibold">
                                        <i class="fas fa-times-circle mr-1"></i>Ditolak
                                    </span>
                                @elseif($item->status === 'dikembalikan')
                                    <span class="bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-semibold">
                                        <i class="fas fa-undo mr-1"></i>Dikembalikan
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <span class="text-xs text-gray-500">Tanggal Pinjam</span>
                                <p class="font-semibold">{{ $item->tanggal_pinjam->format('d M Y') }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Tanggal Kembali</span>
                                <p class="font-semibold">{{ $item->tanggal_kembali->format('d M Y') }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Diajukan</span>
                                <p class="font-semibold">{{ $item->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        @if($item->keperluan)
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <span class="text-xs text-gray-500">Keperluan:</span>
                            <p class="text-sm">{{ $item->keperluan }}</p>
                        </div>
                        @endif

                        @if($item->status === 'approved' && $item->approver)
                        <div class="mb-4 p-3 bg-green-50 rounded-lg">
                            <span class="text-xs text-green-700">Disetujui oleh:</span>
                            <p class="text-sm font-semibold text-green-800">{{ $item->approver->nama_user }}</p>
                            <p class="text-xs text-green-600">{{ $item->approved_at->format('d M Y H:i') }}</p>
                        </div>
                        @endif

                        @if($item->status === 'rejected' && $item->rejection_reason)
                        <div class="mb-4 p-3 bg-red-50 rounded-lg">
                            <span class="text-xs text-red-700">Alasan Penolakan:</span>
                            <p class="text-sm text-red-800">{{ $item->rejection_reason }}</p>
                        </div>
                        @endif

                        <div class="flex gap-3">
                            @if($item->status === 'pending' || $item->status === 'approved')
                            <a href="{{ route('mahasiswa.show', $item->id) }}" 
                               class="flex-1 bg-purple-600 hover:bg-purple-700 text-white text-center py-2 rounded-lg text-sm font-semibold transition">
                                <i class="fas fa-qrcode mr-1"></i>Lihat QR Code
                            </a>
                            @endif
                            
                            @if($item->status === 'pending')
                            <span class="flex-1 bg-gray-100 text-gray-600 text-center py-2 rounded-lg text-sm">
                                <i class="fas fa-hourglass-half mr-1"></i>Menunggu Admin
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg card-shadow p-12 text-center">
            <i class="fas fa-search text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Cari Status Peminjaman</h3>
            <p class="text-gray-500">Masukkan NIM Anda untuk melihat status peminjaman</p>
        </div>
    @endif
</div>
@endsection



