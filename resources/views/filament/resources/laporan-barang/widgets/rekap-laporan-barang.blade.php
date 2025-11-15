<x-filament::widget>
    <x-filament::card>
        <h2 class="text-lg font-bold mb-4">Rekap Laporan Barang</h2>

        <div class="grid grid-cols-3 gap-4">
            <div class="p-4 bg-gray-800 rounded-lg text-center">
                <p class="text-sm">Total Dipinjam</p>
                <p class="text-2xl font-bold text-amber-400">
                    {{ $this->getData()['total_dipinjam'] }}
                </p>
            </div>

            <div class="p-4 bg-gray-800 rounded-lg text-center">
                <p class="text-sm">Total Stok Tersedia</p>
                <p class="text-2xl font-bold text-green-400">
                    {{ $this->getData()['total_stok'] }}
                </p>
            </div>

            <div class="p-4 bg-gray-800 rounded-lg text-center">
                <p class="text-sm">Barang di Bawah Minimum</p>
                <p class="text-2xl font-bold text-red-400">
                    {{ $this->getData()['barang_minimum'] }}
                </p>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
