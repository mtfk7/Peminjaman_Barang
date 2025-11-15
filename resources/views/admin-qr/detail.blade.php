<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Peminjaman - Admin</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #09090b;
        }
        .card-bg {
            background-color: #18181b;
        }
        .btn-primary {
            background-color: #f59e0b;
        }
        .btn-primary:hover {
            background-color: #d97706;
        }
        .btn-approve {
            background-color: #16a34a;
        }
        .btn-approve:hover {
            background-color: #15803d;
        }
    </style>
</head>
<body class="text-gray-100 min-h-screen" style="background-color: #09090b;">
    <!-- Header -->
    <div class="shadow-sm border-b border-gray-700" style="background-color: #18181b;">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <div class="p-2 rounded-lg" style="background-color: rgba(245, 158, 11, 0.1);">
                        <i class="fas fa-file-alt text-2xl" style="color: #f59e0b;"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">Detail Peminjaman</h1>
                        <p class="text-sm text-gray-400">Verifikasi & Approve Peminjaman</p>
                    </div>
                </div>
                <a href="{{ route('qr.scanner') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-600 rounded-lg text-sm font-medium text-gray-300 hover:bg-gray-700 transition"
                   style="background-color: #27272a;">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6 max-w-4xl">
        <!-- Alert Messages -->
        @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-400 p-4 rounded-lg mb-6" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <p>{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-400 p-4 rounded-lg mb-6" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <p>{{ session('error') }}</p>
            </div>
        </div>
        @endif

        <!-- Status Card -->
        <div class="card-bg shadow-sm rounded-xl border border-gray-700 p-6 mb-6">
            <div class="text-center mb-6">
                @if($peminjaman->status === 'pending')
                    <div class="inline-flex items-center bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400 px-8 py-3 rounded-full font-bold text-lg">
                        <i class="fas fa-clock mr-2"></i>Menunggu Approval
                    </div>
                @elseif($peminjaman->status === 'approved')
                    <div class="inline-flex items-center bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 px-8 py-3 rounded-full font-bold text-lg">
                        <i class="fas fa-check-circle mr-2"></i>Sudah Disetujui
                    </div>
                @elseif($peminjaman->status === 'rejected')
                    <div class="inline-flex items-center bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 px-8 py-3 rounded-full font-bold text-lg">
                        <i class="fas fa-times-circle mr-2"></i>Ditolak
                    </div>
                @endif
            </div>

            <!-- Data Mahasiswa -->
            <div class="mb-6">
                <h3 class="text-base font-semibold text-white mb-4 flex items-center">
                    <i class="fas fa-user mr-2" style="color: #f59e0b;"></i>
                    Data Mahasiswa
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 rounded-lg p-4 border border-gray-700" style="background-color: #09090b;">
                    <div>
                        <span class="text-xs text-gray-400 block mb-1">Pemilik Akun</span>
                        <p class="font-semibold text-white">{{ $peminjaman->nama_mahasiswa }}</p>
                    </div>
                    @if($peminjaman->nama_peminjam)
                    <div>
                        <span class="text-xs text-gray-400 block mb-1">Nama Peminjam</span>
                        <p class="font-semibold text-white">{{ $peminjaman->nama_peminjam }}</p>
                    </div>
                    @endif
                    <div>
                        <span class="text-xs text-gray-400 block mb-1">NIM</span>
                        <p class="font-semibold text-white">{{ $peminjaman->nim }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block mb-1">Email</span>
                        <p class="font-semibold text-white">{{ $peminjaman->email }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block mb-1">No. Telepon</span>
                        <p class="font-semibold text-white">{{ $peminjaman->no_telp }}</p>
                    </div>
                </div>
            </div>

            <!-- Data Barang -->
            <div class="mb-6">
                <h3 class="text-base font-semibold text-white mb-4 flex items-center">
                    <i class="fas fa-box text-blue-500 mr-2"></i>
                    Data Barang yang Dipinjam ({{ $peminjaman->items->count() }} items)
                </h3>
                <div class="space-y-3">
                    @foreach($peminjaman->items as $item)
                    <div class="rounded-lg p-4 border border-gray-700" style="background-color: #18181b;">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <p class="font-bold text-white text-base">{{ $item->nama_barang }}</p>
                                <p class="text-xs text-gray-400 mt-1">ID: {{ $item->barang_id }}</p>
                            </div>
                            <span class="px-2 py-1 rounded text-xs font-semibold 
                                {{ $item->status === 'pending' ? 'bg-yellow-900/30 text-yellow-400' : 
                                   ($item->status === 'approved' ? 'bg-green-900/30 text-green-400' : 
                                   ($item->status === 'rejected' ? 'bg-red-900/30 text-red-400' : 'bg-blue-900/30 text-blue-400')) }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <span class="text-xs text-gray-400 block">Jumlah</span>
                                <p class="font-semibold text-white">{{ $item->jumlah }} {{ $item->satuan }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400 block">Jenis</span>
                                <p class="font-semibold text-white">
                                    {{ $item->jenis_barang === 'habis_pakai' ? 'Habis Pakai' : 'Tidak Habis Pakai' }}
                                </p>
                            </div>
                        </div>
                        @if($item->jam_kembali)
                        <div class="mt-2">
                            <span class="text-xs text-gray-400 block">Jam Kembali</span>
                            <p class="font-semibold text-white">{{ date('H:i', strtotime($item->jam_kembali)) }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Informasi Peminjaman -->
            <div class="mb-6">
                <h3 class="text-base font-semibold text-white mb-4 flex items-center">
                    <i class="fas fa-calendar text-green-500 mr-2"></i>
                    Informasi Peminjaman
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="rounded-lg p-4 border border-gray-700" style="background-color: #09090b;">
                        <span class="text-xs text-gray-400 block mb-1">Tanggal Pinjam</span>
                        <p class="font-semibold text-white">{{ $peminjaman->tanggal_pinjam->format('d M Y') }}</p>
                    </div>
                    @if($peminjaman->jam_pinjam)
                    <div class="rounded-lg p-4 border border-gray-700" style="background-color: #09090b;">
                        <span class="text-xs text-gray-400 block mb-1">
                            Jam Pinjam
                            @if($peminjaman->jam_kembali)
                                - Kembali
                            @endif
                        </span>
                        <p class="font-semibold text-white">
                            {{ date('H:i', strtotime($peminjaman->jam_pinjam)) }}
                        </p>
                    </div>
                    @endif
                    @if($peminjaman->mata_kuliah)
                    <div class="rounded-lg p-4 border border-gray-700" style="background-color: #09090b;">
                        <span class="text-xs text-gray-400 block mb-1">Mata Kuliah</span>
                        <p class="font-semibold text-white">{{ $peminjaman->mata_kuliah }}</p>
                    </div>
                    @endif
                    @if($peminjaman->kelas)
                    <div class="rounded-lg p-4 border border-gray-700" style="background-color: #09090b;">
                        <span class="text-xs text-gray-400 block mb-1">Kelas</span>
                        <p class="font-semibold text-white">{{ $peminjaman->kelas }}</p>
                    </div>
                    @endif
                    <div class="md:col-span-2 rounded-lg p-4 border border-gray-700" style="background-color: #09090b;">
                        <span class="text-xs text-gray-400 block mb-1">Diajukan pada</span>
                        <p class="font-semibold text-white">{{ $peminjaman->created_at->format('d M Y H:i') }}</p>
                    </div>
                    @if($peminjaman->keperluan)
                    <div class="md:col-span-2 rounded-lg p-4 border border-blue-800" style="background-color: rgba(59, 130, 246, 0.1);">
                        <span class="text-xs text-blue-400 font-semibold block mb-1">Keperluan</span>
                        <p class="text-gray-100">{{ $peminjaman->keperluan }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Approval Info (if already processed) -->
            @php
                $statusKeseluruhan = $peminjaman->status_keseluruhan;
            @endphp
            @if($statusKeseluruhan !== 'pending')
            <div class="mb-6">
                <h3 class="text-base font-semibold text-white mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Informasi Approval
                </h3>
                <div class="rounded-lg p-4 border border-gray-700" style="background-color: #09090b;">
                    @if($peminjaman->approver)
                    <div class="mb-2">
                        <span class="text-xs text-gray-400 block mb-1">Diproses oleh</span>
                        <p class="font-semibold text-white">{{ $peminjaman->approver->nama_user }}</p>
                    </div>
                    @endif
                    <div class="mb-2">
                        <span class="text-xs text-gray-400 block mb-1">Tanggal Proses</span>
                        <p class="font-semibold text-white">{{ $peminjaman->approved_at->format('d M Y H:i') }}</p>
                    </div>
                    @if($peminjaman->status === 'rejected' && $peminjaman->rejection_reason)
                    <div class="mt-4 border border-red-800 rounded-lg p-3" style="background-color: rgba(220, 38, 38, 0.1);">
                        <span class="text-xs font-semibold text-red-400 block mb-1">Alasan Penolakan</span>
                        <p class="text-red-300">{{ $peminjaman->rejection_reason }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            @if($statusKeseluruhan === 'pending')
            <div class="pt-6 border-t border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Approve Button -->
                    <form action="{{ route('qr.approve', $peminjaman->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui peminjaman ini?');">
                        @csrf
                        <button type="submit" class="btn-approve w-full inline-flex items-center justify-center text-white py-4 rounded-lg font-bold text-base transition shadow-sm">
                            <i class="fas fa-check-circle mr-2"></i>SETUJUI PEMINJAMAN
                        </button>
                    </form>

                    <!-- Reject Button -->
                    <button onclick="showRejectModal()" class="w-full inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white py-4 rounded-lg font-bold text-base transition shadow-sm">
                        <i class="fas fa-times-circle mr-2"></i>TOLAK PEMINJAMAN
                    </button>
                </div>
            </div>
            @else
            <div class="pt-6 border-t border-gray-700 text-center">
                <p class="text-gray-400 mb-4">Peminjaman ini sudah diproses</p>
                <a href="{{ route('qr.scanner') }}" class="btn-primary inline-flex items-center px-6 py-3 text-white rounded-lg font-semibold transition shadow-sm">
                    <i class="fas fa-qrcode mr-2"></i>Scan QR Lainnya
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="reject-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="card-bg rounded-xl shadow-2xl max-w-md w-full border border-gray-700">
            <div class="px-6 py-4 border-b border-gray-700">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-times-circle text-red-500 mr-2"></i>
                    Tolak Peminjaman
                </h3>
            </div>
            <form action="{{ route('qr.reject', $peminjaman->id) }}" method="POST" class="p-6">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-medium text-white mb-2">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="rejection_reason" required rows="4"
                              class="w-full px-4 py-3 border border-gray-600 rounded-lg text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                              style="background-color: #09090b;"
                              placeholder="Jelaskan alasan penolakan..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="hideRejectModal()" 
                            class="flex-1 px-4 py-3 border border-gray-600 hover:bg-gray-700 text-gray-200 rounded-lg font-semibold transition"
                            style="background-color: #27272a;">
                        Batal
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition shadow-sm">
                        <i class="fas fa-times-circle mr-2"></i>Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showRejectModal() {
            document.getElementById('reject-modal').classList.remove('hidden');
            document.getElementById('reject-modal').classList.add('flex');
        }

        function hideRejectModal() {
            document.getElementById('reject-modal').classList.add('hidden');
            document.getElementById('reject-modal').classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('reject-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideRejectModal();
            }
        });

        // Close with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideRejectModal();
            }
        });
    </script>
</body>
</html>
