<x-filament::page>
    <form wire:submit.prevent="generateReport" class="space-y-4">
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-300">Jenis Barang</label>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model="jenisBarang" class="bg-gray-800 text-gray-100 border-gray-700">
                        <option value="habis_pakai">Barang Habis Pakai</option>
                        <option value="tidak_habis_pakai">Barang Tidak Habis Pakai</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-300">Periode</label>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model="periode" class="bg-gray-800 text-gray-100 border-gray-700">
                        <option value="harian">Harian</option>
                        <option value="mingguan">Mingguan</option>
                        <option value="bulanan">Bulanan</option>
                        <option value="custom">Custom</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            @if ($periode === 'harian' || $periode === 'custom')
                <div>
                    <label class="text-sm font-medium text-gray-300">Tanggal Mulai</label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" wire:model="tanggalMulai" class="bg-gray-800 text-gray-100 border-gray-700" />
                    </x-filament::input.wrapper>
                </div>
            @endif

            @if ($periode === 'custom')
                <div>
                    <label class="text-sm font-medium text-gray-300">Tanggal Selesai</label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" wire:model="tanggalSelesai" class="bg-gray-800 text-gray-100 border-gray-700" />
                    </x-filament::input.wrapper>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-3">
            <x-filament::button type="submit" color="warning">Tampilkan Laporan</x-filament::button>

            {{-- Tombol Export PDF hanya muncul jika ada data --}}
            @if(!empty($laporan) && count($laporan) > 0)
                <x-filament::button wire:click="exportPdf" color="danger">
                    Export PDF
                </x-filament::button>
            @endif
        </div>
    </form>

    {{-- Hasil Laporan --}}
    <div class="mt-6">
        @if(!empty($laporan) && count($laporan) > 0)
            @if ($jenisBarang === 'habis_pakai')
                <h2 class="text-lg font-bold mb-3 text-white">Laporan Barang Habis Pakai</h2>
                <table class="min-w-full bg-gray-900 border border-gray-700 rounded-lg text-gray-100">
                    <thead class="bg-gray-800 text-gray-200">
                        <tr>
                            <th class="px-4 py-2 border border-gray-700">Nama Barang</th>
                            <th class="px-4 py-2 border border-gray-700">Total Keluar</th>
                            <th class="px-4 py-2 border border-gray-700">Satuan</th>
                            <th class="px-4 py-2 border border-gray-700">Total Stok</th>
                            <th class="px-4 py-2 border border-gray-700">Batas Minimum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($laporan as $item)
                            <tr class="hover:bg-gray-800 transition">
                                <td class="px-4 py-2 border border-gray-700">{{ $item->nama_barang }}</td>
                                <td class="px-4 py-2 border border-gray-700 text-center">{{ $item->total_keluar }}</td>
                                <td class="px-4 py-2 border border-gray-700 text-center">{{ $item->satuan }}</td>
                                <td class="px-4 py-2 border border-gray-700 text-center">{{ $item->total_stok }}</td>
                                <td class="px-4 py-2 border border-gray-700 text-center">{{ $item->batas_minimum }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <h2 class="text-lg font-bold mb-3 text-white">Laporan Barang Tidak Habis Pakai</h2>
                <table class="min-w-full bg-gray-900 border border-gray-700 rounded-lg text-gray-100">
                    <thead class="bg-gray-800 text-gray-200">
                        <tr>
                            <th class="px-4 py-2 border border-gray-700">Nama Barang</th>
                            <th class="px-4 py-2 border border-gray-700">Total Dipinjam</th>
                            <th class="px-4 py-2 border border-gray-700">Satuan</th>
                            <th class="px-4 py-2 border border-gray-700">Kembali Baik</th>
                            <th class="px-4 py-2 border border-gray-700">Kembali Kurang Baik</th>
                            <th class="px-4 py-2 border border-gray-700">Kembali Tidak Baik</th>
                            <th class="px-4 py-2 border border-gray-700">Total Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($laporan as $item)
                            <tr class="hover:bg-gray-800 transition">
                                <td class="px-4 py-2 border border-gray-700">{{ $item->nama_barang }}</td>
                                <td class="px-4 py-2 border border-gray-700 text-center">{{ $item->total_dipinjam }}</td>
                                <td class="px-4 py-2 border border-gray-700 text-center">{{ $item->satuan }}</td>
                                <td class="px-4 py-2 border border-gray-700 text-center">{{ $item->kembali_baik }}</td>
                                <td class="px-4 py-2 border border-gray-700 text-center">{{ $item->kembali_kurang_baik }}</td>
                                <td class="px-4 py-2 border border-gray-700 text-center">{{ $item->kembali_tidak_baik }}</td>
                                <td class="px-4 py-2 border border-gray-700 text-center">{{ $item->total_stok }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @else
            {{-- Teks jika tidak ada data --}}
            <div class="mt-10 text-center text-gray-400 italic border border-gray-700 bg-gray-900 rounded-lg py-10">
                Tidak ada data barang keluar dalam periode ini.
            </div>
        @endif
    </div>
</x-filament::page>
