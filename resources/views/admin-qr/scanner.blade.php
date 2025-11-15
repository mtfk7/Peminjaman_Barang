<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>QR Scanner - Admin</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- QR Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    
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
    </style>
</head>
<body class="text-gray-100 min-h-screen" style="background-color: #09090b;">
    <!-- Header -->
    <div class="shadow-sm border-b border-gray-700" style="background-color: #18181b;">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <div class="p-2 rounded-lg" style="background-color: rgba(245, 158, 11, 0.1);">
                        <i class="fas fa-qrcode text-2xl" style="color: #f59e0b;"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">QR Code Scanner</h1>
                        <p class="text-sm text-gray-400">Scan QR untuk Approve Peminjaman Mahasiswa</p>
                    </div>
                </div>
                <a href="/admin/peminjaman-mahasiswas" 
                   class="inline-flex items-center px-4 py-2 border border-gray-600 rounded-lg text-sm font-medium text-gray-300 hover:bg-gray-700 transition"
                   style="background-color: #27272a;">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- QR Scanner Section -->
            <div class="card-bg shadow-sm rounded-xl border border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-camera mr-2" style="color: #f59e0b;"></i>
                        Scan QR Code
                    </h2>
                </div>
                
                <div class="p-6">
                    <!-- Scanner Container -->
                    <div id="reader" class="w-full mb-4 rounded-lg overflow-hidden" style="background-color: #000;"></div>
                    
                    <div class="flex gap-3 justify-center">
                        <button id="start-scanner" 
                                class="btn-primary inline-flex items-center px-4 py-2 text-white rounded-lg font-medium transition shadow-sm">
                            <i class="fas fa-camera mr-2"></i>Mulai Scan
                        </button>
                        <button id="stop-scanner" 
                                class="hidden inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition shadow-sm">
                            <i class="fas fa-stop mr-2"></i>Stop Scan
                        </button>
                    </div>

                    <!-- Manual Input -->
                    <div class="mt-6 pt-6 border-t border-gray-700">
                        <h3 class="font-semibold text-gray-100 mb-3 text-sm">
                            <i class="fas fa-keyboard mr-2 text-gray-400"></i>Atau Input Manual:
                        </h3>
                        <form id="manual-form" class="flex gap-2">
                            <input type="text" id="qr-input" placeholder="Paste QR Code di sini..." 
                                   class="flex-1 px-4 py-2.5 border border-gray-600 rounded-lg text-gray-100 placeholder-gray-400 focus:ring-2 focus:border-transparent transition" 
                                   style="background-color: #09090b; --tw-ring-color: #f59e0b;">
                            <button type="submit" 
                                    class="btn-primary inline-flex items-center px-4 py-2 text-white rounded-lg font-medium transition shadow-sm">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Pending List -->
            <div class="card-bg shadow-sm rounded-xl border border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-clock text-yellow-500 mr-2"></i>
                        Menunggu Approval
                    </h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-900/30 text-yellow-400">
                        {{ $pendingPeminjaman->count() }} Pending
                    </span>
                </div>
                
                <div class="p-6">
                    <div class="space-y-3 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                        @forelse($pendingPeminjaman as $item)
                        <div class="border border-gray-700 rounded-lg p-4 transition" style="background-color: #09090b; border-color: #27272a;" onmouseover="this.style.borderColor='#f59e0b'" onmouseout="this.style.borderColor='#27272a'">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-white">{{ $item->nama_peminjam ?? $item->nama_mahasiswa }}</h3>
                                    <p class="text-sm text-gray-400">Pemilik: {{ $item->nama_mahasiswa }} | NIM: {{ $item->nim }}</p>
                                </div>
                                <span class="bg-yellow-900/30 text-yellow-400 px-3 py-1 rounded-full text-xs font-semibold">
                                    Pending
                                </span>
                            </div>
                            
                            <div class="mb-3 p-3 rounded-lg border border-gray-700" style="background-color: #18181b;">
                                <div class="text-xs text-gray-400 mb-2">
                                    <i class="fas fa-box mr-1" style="color: #f59e0b;"></i>
                                    {{ $item->items->count() }} Items
                                </div>
                                @foreach($item->items->take(2) as $barangItem)
                                <div class="mb-2 last:mb-0">
                                    <div class="flex items-center text-sm text-gray-300">
                                        <strong>{{ $barangItem->nama_barang }}</strong>
                                        <span class="ml-2 px-2 py-0.5 rounded text-xs 
                                            {{ $barangItem->jenis_barang === 'habis_pakai' ? 'bg-orange-900/30 text-orange-400' : 'bg-blue-900/30 text-blue-400' }}">
                                            {{ $barangItem->jenis_barang === 'habis_pakai' ? 'Consumable' : 'Non-consumable' }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-400 ml-0 mt-1">
                                        {{ $barangItem->jumlah }} {{ $barangItem->satuan }}
                                    </div>
                                </div>
                                @endforeach
                                @if($item->items->count() > 2)
                                <div class="text-xs text-gray-500 mt-2">
                                    +{{ $item->items->count() - 2 }} more items
                                </div>
                                @endif
                            </div>
                            
                            <div class="flex items-center text-xs text-gray-400 mb-3">
                                <i class="fas fa-calendar mr-2"></i>
                                {{ $item->tanggal_pinjam->format('d M Y') }}
                                @if($item->jam_pinjam)
                                    | <i class="fas fa-clock ml-2 mr-1"></i>
                                    {{ date('H:i', strtotime($item->jam_pinjam)) }}
                                    @if($item->jam_kembali)
                                        - {{ date('H:i', strtotime($item->jam_kembali)) }}
                                    @endif
                                @endif
                            </div>
                            @if($item->mata_kuliah || $item->kelas)
                            <div class="flex items-center text-xs text-gray-500 mb-3">
                                @if($item->mata_kuliah)
                                    <i class="fas fa-book mr-1"></i>{{ $item->mata_kuliah }}
                                @endif
                                @if($item->kelas)
                                    <span class="ml-2"><i class="fas fa-door-open mr-1"></i>{{ $item->kelas }}</span>
                                @endif
                            </div>
                            @endif
                            
                            <a href="{{ route('qr.detail', $item->qr_code) }}" 
                               class="btn-primary flex items-center justify-center w-full px-4 py-2 text-white rounded-lg text-sm font-medium transition shadow-sm">
                                <i class="fas fa-eye mr-2"></i>Detail & Approve
                            </a>
                        </div>
                        @empty
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4" style="background-color: #18181b;">
                                <i class="fas fa-inbox text-gray-600 text-3xl"></i>
                            </div>
                            <p class="text-gray-400 font-medium">Tidak ada peminjaman pending</p>
                            <p class="text-sm text-gray-500 mt-1">Semua peminjaman sudah diproses</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgb(31, 41, 55);
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgb(75, 85, 99);
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgb(107, 114, 128);
        }
    </style>

    <script>
        let html5QrcodeScanner;
        const startBtn = document.getElementById('start-scanner');
        const stopBtn = document.getElementById('stop-scanner');
        const manualForm = document.getElementById('manual-form');
        const qrInput = document.getElementById('qr-input');

        // Start Scanner
        startBtn.addEventListener('click', function() {
            html5QrcodeScanner = new Html5Qrcode("reader");
            
            Html5Qrcode.getCameras().then(cameras => {
                if (cameras && cameras.length) {
                    html5QrcodeScanner.start(
                        { facingMode: "environment" },
                        {
                            fps: 10,
                            qrbox: { width: 250, height: 250 }
                        },
                        onScanSuccess
                    ).catch(err => {
                        console.error('Error starting scanner:', err);
                        alert('Gagal memulai scanner. Pastikan Anda memberikan izin kamera.');
                    });
                    
                    startBtn.classList.add('hidden');
                    stopBtn.classList.remove('hidden');
                }
            }).catch(err => {
                console.error('Error getting cameras:', err);
                alert('Tidak dapat mengakses kamera.');
            });
        });

        // Stop Scanner
        stopBtn.addEventListener('click', function() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().then(() => {
                    startBtn.classList.remove('hidden');
                    stopBtn.classList.add('hidden');
                }).catch(err => {
                    console.error('Error stopping scanner:', err);
                });
            }
        });

        // On Scan Success
        function onScanSuccess(decodedText, decodedResult) {
            console.log(`Scan result: ${decodedText}`);
            
            // Extract QR code from URL if needed
            let qrCode = decodedText;
            if (decodedText.includes('verify/')) {
                qrCode = decodedText.split('verify/')[1];
            }
            
            // Redirect to detail page
            window.location.href = `/admin/qr-scanner/detail/${qrCode}`;
        }

        // Manual Form Submit
        manualForm.addEventListener('submit', function(e) {
            e.preventDefault();
            let qrCode = qrInput.value.trim();
            
            if (qrCode) {
                // Extract QR code from URL if needed
                if (qrCode.includes('verify/')) {
                    qrCode = qrCode.split('verify/')[1];
                }
                
                window.location.href = `/admin/qr-scanner/detail/${qrCode}`;
            }
        });
    </script>
</body>
</html>
