@extends('mahasiswa.layout')

@section('title', 'Detail Peminjaman')

@section('content')
<!-- Status Header -->
<div class="bg-white px-4 py-4 border-b">
    <div class="flex justify-between items-center">
        <a href="{{ route('mahasiswa.history') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <h2 class="text-lg font-semibold text-gray-800">Borrowing Details</h2>
        <div class="w-6"></div> <!-- Spacer -->
    </div>
</div>

<!-- Status Badge -->
<div class="bg-white px-4 py-6 border-b">
    <div class="text-center">
        @php
            $statusKeseluruhan = $peminjaman->status_keseluruhan;
        @endphp
        @if($statusKeseluruhan === 'pending')
            <div class="inline-flex items-center bg-yellow-100 text-yellow-800 px-6 py-3 rounded-full mb-3">
                <i class="fas fa-clock mr-2 text-2xl"></i>
                <span class="text-lg font-semibold">Pending Approval</span>
            </div>
            <p class="text-sm text-gray-600">Waiting for admin to scan your QR code</p>
        @elseif($statusKeseluruhan === 'approved')
            <div class="inline-flex items-center bg-green-100 text-green-800 px-6 py-3 rounded-full mb-3">
                <i class="fas fa-check-circle mr-2 text-2xl"></i>
                <span class="text-lg font-semibold">Approved</span>
            </div>
            <p class="text-sm text-gray-600">Your borrowing request has been approved!</p>
        @elseif($statusKeseluruhan === 'rejected')
            <div class="inline-flex items-center bg-red-100 text-red-800 px-6 py-3 rounded-full mb-3">
                <i class="fas fa-times-circle mr-2 text-2xl"></i>
                <span class="text-lg font-semibold">Rejected</span>
            </div>
            <p class="text-sm text-red-600">Some items were rejected</p>
        @elseif($statusKeseluruhan === 'dikembalikan')
            <div class="inline-flex items-center bg-blue-100 text-blue-800 px-6 py-3 rounded-full mb-3">
                <i class="fas fa-undo mr-2 text-2xl"></i>
                <span class="text-lg font-semibold">All Returned</span>
            </div>
            <p class="text-sm text-gray-600">All equipment has been returned</p>
        @endif
    </div>
</div>

<!-- QR Code (only show if pending or approved) -->
@if($peminjaman->status_keseluruhan === 'pending' || $peminjaman->status_keseluruhan === 'approved')
<div class="bg-white px-4 py-6 border-b">
    <h3 class="text-center text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">
        Your QR Code
    </h3>
    <div class="flex justify-center mb-4">
        <div class="bg-white p-4 rounded-lg shadow-lg">
            {!! $qrCode !!}
        </div>
    </div>
    <p class="text-center text-xs text-gray-500">
        Show this QR code to admin for verification
    </p>
</div>
@endif

<!-- Borrowing Information -->
<div class="bg-white mt-2">
    <div class="px-4 py-3 border-b">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Equipment Details ({{ $peminjaman->items->count() }} items)</h3>
    </div>
    
    <div class="divide-y">
        @foreach($peminjaman->items as $item)
        <div class="px-4 py-3 border-b">
            <div class="flex justify-between items-start mb-2">
                <div class="flex-1">
                    <span class="text-xs text-gray-500 block mb-1">Item Name</span>
                    <span class="text-gray-800 font-semibold">{{ $item->nama_barang }}</span>
                </div>
                <span class="px-2 py-1 rounded text-xs font-semibold 
                    {{ $item->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                       ($item->status === 'approved' ? 'bg-green-100 text-green-800' : 
                       ($item->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                    {{ ucfirst($item->status) }}
                </span>
            </div>
            
            <div class="grid grid-cols-2 gap-2 mt-2">
                <div>
                    <span class="text-xs text-gray-500 block">Quantity</span>
                    <span class="text-gray-800 font-semibold text-sm">{{ $item->jumlah }} {{ $item->satuan }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Type</span>
                    <span class="text-gray-800 font-semibold text-sm">
                        {{ $item->jenis_barang === 'habis_pakai' ? 'Consumable' : 'Non-consumable' }}
                    </span>
                </div>
            </div>
            
            @if($item->jam_kembali)
            <div class="mt-2">
                <span class="text-xs text-gray-500 block">Return Time</span>
                <span class="text-gray-800 font-semibold text-sm">{{ date('H:i', strtotime($item->jam_kembali)) }}</span>
            </div>
            @endif
        </div>
        @endforeach
        
        <div class="px-4 py-3">
            <span class="text-xs text-gray-500 block mb-1">Borrow Date</span>
            <span class="text-gray-800 font-semibold">{{ $peminjaman->tanggal_pinjam->format('d M Y') }}</span>
        </div>
        
        @if($peminjaman->jam_pinjam)
        <div class="px-4 py-3">
            <span class="text-xs text-gray-500 block mb-1">Borrow Time</span>
            <span class="text-gray-800 font-semibold">{{ date('H:i', strtotime($peminjaman->jam_pinjam)) }}</span>
        </div>
        @endif
        
        @if($peminjaman->mata_kuliah)
        <div class="px-4 py-3">
            <span class="text-xs text-gray-500 block mb-1">Mata Kuliah</span>
            <span class="text-gray-800 font-semibold">{{ $peminjaman->mata_kuliah }}</span>
        </div>
        @endif
        
        @if($peminjaman->kelas)
        <div class="px-4 py-3">
            <span class="text-xs text-gray-500 block mb-1">Kelas</span>
            <span class="text-gray-800 font-semibold">{{ $peminjaman->kelas }}</span>
        </div>
        @endif
        
        @if($peminjaman->keperluan)
        <div class="px-4 py-3">
            <span class="text-xs text-gray-500 block mb-1">Purpose</span>
            <span class="text-gray-800">{{ $peminjaman->keperluan }}</span>
        </div>
        @endif
    </div>
</div>

<!-- Student Information -->
<div class="bg-white mt-2">
    <div class="px-4 py-3 border-b">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Student Information</h3>
    </div>
    
    <div class="divide-y">
        <div class="px-4 py-3">
            <span class="text-xs text-gray-500 block mb-1">Pemilik Akun</span>
            <span class="text-gray-800 font-semibold">{{ $peminjaman->nama_mahasiswa }}</span>
        </div>
        
        @if($peminjaman->nama_peminjam)
        <div class="px-4 py-3">
            <span class="text-xs text-gray-500 block mb-1">Nama Peminjam</span>
            <span class="text-gray-800 font-semibold">{{ $peminjaman->nama_peminjam }}</span>
        </div>
        @endif
        
        <div class="px-4 py-3">
            <span class="text-xs text-gray-500 block mb-1">NIM</span>
            <span class="text-gray-800 font-semibold">{{ $peminjaman->nim }}</span>
        </div>
        
        <div class="px-4 py-3">
            <span class="text-xs text-gray-500 block mb-1">Email</span>
            <span class="text-gray-800">{{ $peminjaman->email }}</span>
        </div>
        
        <div class="px-4 py-3">
            <span class="text-xs text-gray-500 block mb-1">Phone</span>
            <span class="text-gray-800">{{ $peminjaman->no_telp }}</span>
        </div>
    </div>
</div>

<!-- Approval Info (if approved or returned) -->
@if($peminjaman->status_keseluruhan === 'approved' || $peminjaman->status_keseluruhan === 'dikembalikan')
<div class="bg-white mt-2 mb-4">
    <div class="px-4 py-3 border-b">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Approval Information</h3>
    </div>
    
    <div class="divide-y">
        @if($peminjaman->approver)
        <div class="px-4 py-3">
            <span class="text-xs text-gray-500 block mb-1">Approved By</span>
            <span class="text-gray-800 font-semibold">{{ $peminjaman->approver->nama_user }}</span>
        </div>
        @endif
        
        @if($peminjaman->approved_at)
        <div class="px-4 py-3">
            <span class="text-xs text-gray-500 block mb-1">Approved At</span>
            <span class="text-gray-800">{{ $peminjaman->approved_at->format('d M Y H:i') }}</span>
        </div>
        @endif
    </div>
</div>
@endif

<!-- Back Button -->
<div class="px-4 py-4">
    <a href="{{ route('mahasiswa.history') }}" 
       class="block text-center bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 rounded-md font-medium transition">
        <i class="fas fa-arrow-left mr-2"></i>Back to History
    </a>
</div>
@endsection
