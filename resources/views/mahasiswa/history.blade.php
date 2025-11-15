@extends('mahasiswa.layout')

@section('title', 'History')

@section('content')
<!-- Summary Cards -->
@if(!$peminjaman->isEmpty())
<div class="bg-white px-4 py-4 border-b">
    <div class="grid grid-cols-4 gap-2">
        <div class="text-center">
            <div class="text-2xl font-bold text-gray-800">{{ $peminjaman->count() }}</div>
            <div class="text-xs text-gray-500">Total</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $peminjaman->filter(fn($p) => $p->status_keseluruhan === 'pending')->count() }}</div>
            <div class="text-xs text-gray-500">Pending</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ $peminjaman->filter(fn($p) => $p->status_keseluruhan === 'approved')->count() }}</div>
            <div class="text-xs text-gray-500">Approved</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $peminjaman->filter(fn($p) => $p->status_keseluruhan === 'dikembalikan')->count() }}</div>
            <div class="text-xs text-gray-500">Returned</div>
        </div>
    </div>
</div>
@endif

<!-- History List -->
<div>
    @forelse($peminjaman as $peminjamanItem)
    <div class="item-card">
        <div class="flex justify-between items-start mb-3">
            <div class="flex-1">
                <h3 class="font-semibold text-gray-800 text-base">
                    {{ $peminjamanItem->items->count() }} Items
                </h3>
                <p class="text-sm text-gray-500">
                    {{ $peminjamanItem->tanggal_pinjam->format('d M Y') }} • {{ date('H:i', strtotime($peminjamanItem->jam_pinjam)) }}
                </p>
            </div>
            <div>
                @php
                    $statusKeseluruhan = $peminjamanItem->status_keseluruhan;
                @endphp
                @if($statusKeseluruhan === 'pending')
                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">
                        Pending
                    </span>
                @elseif($statusKeseluruhan === 'approved')
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                        Approved
                    </span>
                @elseif($statusKeseluruhan === 'rejected')
                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold">
                        Rejected
                    </span>
                @elseif($statusKeseluruhan === 'dikembalikan')
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">
                        Returned
                    </span>
                @endif
            </div>
        </div>

        <!-- Items List -->
        <div class="bg-gray-50 rounded-lg p-3 mb-3">
            @foreach($peminjamanItem->items as $item)
            <div class="mb-2 last:mb-0 pb-2 last:pb-0 border-b last:border-0">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-800">{{ $item->nama_barang }}</p>
                        <p class="text-xs text-gray-500">{{ $item->jumlah }} {{ $item->satuan }} • 
                            <span class="capitalize">{{ $item->jenis_barang === 'habis_pakai' ? 'Consumable' : 'Non-consumable' }}</span>
                        </p>
                    </div>
                    <span class="px-2 py-1 rounded text-xs font-semibold 
                        {{ $item->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                           ($item->status === 'approved' ? 'bg-green-100 text-green-800' : 
                           ($item->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="flex flex-wrap gap-3 text-xs text-gray-600 mb-3">
            @if($peminjamanItem->nama_peminjam)
            <div>
                <i class="fas fa-user text-gray-400 mr-1"></i>
                Peminjam: {{ $peminjamanItem->nama_peminjam }}
            </div>
            @endif
            @if($peminjamanItem->mata_kuliah)
            <div>
                <i class="fas fa-book text-gray-400 mr-1"></i>
                {{ $peminjamanItem->mata_kuliah }}
            </div>
            @endif
            @if($peminjamanItem->kelas)
            <div>
                <i class="fas fa-door-open text-gray-400 mr-1"></i>
                Kelas: {{ $peminjamanItem->kelas }}
            </div>
            @endif
        </div>

        @if($peminjamanItem->keperluan)
        <p class="text-sm text-gray-600 mb-3 italic">"{{ $peminjamanItem->keperluan }}"</p>
        @endif

        @if($peminjamanItem->approver)
        <div class="bg-green-50 rounded-lg px-3 py-2 mb-3">
            <p class="text-xs text-green-700">
                <i class="fas fa-check-circle mr-1"></i>
                Approved by {{ $peminjamanItem->approver->nama_user }}
            </p>
        </div>
        @endif

        @if($statusKeseluruhan === 'pending' || $statusKeseluruhan === 'approved')
        <a href="{{ route('mahasiswa.show', $peminjamanItem->id) }}" 
           class="block text-center bg-green-500 hover:bg-green-600 text-white py-2 rounded-md text-sm font-medium transition">
            <i class="fas fa-qrcode mr-1"></i>View QR Code
        </a>
        @endif
    </div>
    @empty
    <div class="p-12 text-center">
        <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
        <h3 class="text-lg font-semibold text-gray-700 mb-2">No History Yet</h3>
        <p class="text-gray-500 mb-6">You haven't made any borrowing requests</p>
        <a href="{{ route('mahasiswa.index') }}" class="inline-block bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition">
            <i class="fas fa-plus mr-2"></i>Borrow Equipment
        </a>
    </div>
    @endforelse
</div>
@endsection
