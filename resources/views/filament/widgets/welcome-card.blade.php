<x-filament::widget>
    <x-filament::card>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-white">
                    👋 Selamat Datang, {{ Auth::user()->name ?? 'Admin' }}
                </h2>
                <p class="text-sm text-gray-400 mt-1">
                    Sistem Peminjaman & Inventory Barang Jurusan Teknologi Informasi
                </p>
            </div>

            <div class="mt-3 sm:mt-0">
                <x-filament::button color="primary" tag="a" href="{{ url('/admin/peminjamans') }}">
                    Lihat Data Peminjaman
                </x-filament::button>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
