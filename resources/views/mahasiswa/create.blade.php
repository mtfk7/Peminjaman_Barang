@extends('mahasiswa.layout')

@section('title', 'Borrow Equipment')

@section('content')
<!-- Header -->
<div class="bg-white px-4 py-4 border-b">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('mahasiswa.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h2 class="text-lg font-semibold text-gray-800">Borrow Equipment</h2>
        </div>
        @if(!empty($cart))
        <form action="{{ route('mahasiswa.clear-cart') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="text-red-500 hover:text-red-700 text-sm">
                <i class="fas fa-trash mr-1"></i>Clear Cart
            </button>
        </form>
        @endif
    </div>
</div>

@if(session('success'))
<div class="px-4 py-3 bg-green-50 border-l-4 border-green-500 text-green-700">
    <p>{{ session('success') }}</p>
</div>
@endif

@if(session('error'))
<div class="px-4 py-3 bg-red-50 border-l-4 border-red-500 text-red-700">
    <p>{{ session('error') }}</p>
</div>
@endif

<!-- Cart Items -->
@if(!empty($cart))
<div class="bg-white px-4 py-4 border-b">
    <h3 class="text-sm font-semibold text-gray-600 mb-3">Items in Cart ({{ count($cart) }})</h3>
    <div class="space-y-3">
        @foreach($cart as $itemKey => $item)
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-800">{{ $item['nama_barang'] }}</h4>
                    <p class="text-xs text-gray-500">{{ $item['kode_barang'] }}</p>
                    <div class="mt-2 flex items-center gap-4 text-sm">
                        <span class="text-gray-600">
                            <strong>Jumlah:</strong> {{ $item['jumlah'] }} {{ $item['satuan'] }}
                        </span>
                        <span class="px-2 py-1 rounded text-xs font-semibold 
                            {{ $item['jenis_barang'] === 'habis_pakai' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $item['jenis_barang'] === 'habis_pakai' ? 'Consumable' : 'Non-consumable' }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Stok tersedia: {{ $item['total_stok'] }} {{ $item['satuan'] }}</p>
                </div>
                <form action="{{ route('mahasiswa.remove-from-cart') }}" method="POST" class="ml-4">
                    @csrf
                    <input type="hidden" name="item_key" value="{{ $itemKey }}">
                    <button type="submit" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@else
<div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 px-4 py-3">
    <p class="text-sm">Cart kosong! Silakan tambahkan barang terlebih dahulu.</p>
    <a href="{{ route('mahasiswa.index') }}" class="text-sm underline mt-2 inline-block">Kembali ke daftar barang</a>
</div>
@endif

<!-- Form -->
@if(!empty($cart))
<form action="{{ route('mahasiswa.store') }}" method="POST" class="bg-white">
    @csrf
    
    <!-- Student Info (Auto-filled, read-only display) -->
    <div class="px-4 py-4 border-b">
        <h3 class="text-sm font-semibold text-gray-600 mb-3">Your Information</h3>
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <span class="text-xs text-gray-500 block">Name</span>
                    <span class="font-semibold text-gray-800">{{ $mahasiswa->nama_lengkap }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">NIM</span>
                    <span class="font-semibold text-gray-800">{{ $mahasiswa->nim }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Email</span>
                    <span class="font-semibold text-gray-800">{{ $mahasiswa->email }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Phone</span>
                    <span class="font-semibold text-gray-800">{{ $mahasiswa->no_telp }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Borrowing Details -->
    <div class="px-4 py-4">
        <h3 class="text-sm font-semibold text-gray-600 mb-3">Borrowing Details</h3>
        
        <!-- Nama Peminjam -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Nama Peminjam <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nama_peminjam" id="nama_peminjam" 
                   required
                   value="{{ old('nama_peminjam', $mahasiswa->nama_lengkap) }}"
                   placeholder="Nama orang yang akan meminjam (bisa berbeda dari pemilik akun)"
                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nama_peminjam') border-red-500 @enderror">
            @error('nama_peminjam')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tanggal Pinjam (Readonly, hari ini) -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Tanggal Pinjam
            </label>
            <input type="date" name="tanggal_pinjam" readonly
                   value="{{ date('Y-m-d') }}"
                   class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-md cursor-not-allowed">
            <p class="text-xs text-gray-500 mt-1">Tanggal hari ini (tidak bisa diubah)</p>
        </div>

        <!-- Jam Pinjam -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Jam Pinjam <span class="text-red-500">*</span>
            </label>
            <input type="time" name="jam_pinjam" required
                   value="{{ old('jam_pinjam') }}"
                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent @error('jam_pinjam') border-red-500 @enderror">
            @error('jam_pinjam')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
            <p class="text-xs text-gray-500 mt-1">Jam pengembalian akan otomatis terisi saat barang dikembalikan</p>
        </div>

        <!-- Mata Kuliah -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Mata Kuliah
            </label>
            <input type="text" name="mata_kuliah" id="mata_kuliah" 
                   value="{{ old('mata_kuliah') }}"
                   placeholder="Nama mata kuliah (optional)"
                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent @error('mata_kuliah') border-red-500 @enderror">
            @error('mata_kuliah')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Kelas -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Kelas
            </label>
            <input type="text" name="kelas" id="kelas" 
                   value="{{ old('kelas') }}"
                   placeholder="Nama kelas (optional)"
                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent @error('kelas') border-red-500 @enderror">
            @error('kelas')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Purpose -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Purpose
            </label>
            <textarea name="keperluan" rows="4"
                      placeholder="Why do you need this equipment? (optional)"
                      class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent @error('keperluan') border-red-500 @enderror">{{ old('keperluan') }}</textarea>
            @error('keperluan')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" 
                class="w-full bg-green-500 hover:bg-green-600 text-white py-4 rounded-md font-semibold text-base transition shadow-lg">
            <i class="fas fa-paper-plane mr-2"></i>Submit Request
        </button>

        <!-- Cancel Button -->
        <a href="{{ route('mahasiswa.index') }}" 
           class="block text-center mt-3 text-gray-600 hover:text-gray-800 py-2 text-sm">
            Cancel
        </a>
    </div>
</form>
@endif

@if($errors->any())
<div class="px-4 pb-4">
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded text-sm">
        <p class="font-semibold mb-1">Please fix the following errors:</p>
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif
@endsection
