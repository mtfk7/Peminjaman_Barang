<x-filament::page>
    <form wire:submit.prevent="generateReport" class="space-y-4">
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-700">Jenis Barang</label>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model="jenisBarang">
                        <option value="habis_pakai">Barang Habis Pakai</option>
                        <option value="tidak_habis_pakai">Barang Tidak Habis Pakai</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Periode</label>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model="periode">
                        <option value="harian">Harian</option>
                        <option value="mingguan">Mingguan</option>
                        <option value="bulanan">Bulanan</option>
                        <option value="custom">Custom</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            @if ($periode === 'harian' || $periode === 'custom')
                <div>
                    <label class="text-sm font-medium text-gray-700">Tanggal Mulai</label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" wire:model="tanggalMulai" />
                    </x-filament::input.wrapper>
                </div>
            @endif

            @if ($periode === 'custom')
                <div>
                    <label class="text-sm font-medium text-gray-700">Tanggal Selesai</label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" wire:model="tanggalSelesai" />
                    </x-filament::input.wrapper>
                </div>
            @endif
        </div>

        <x-filament::button type="submit" color="primary">Tampilkan Laporan</x-filament::button>
    </form>

    {{-- Hasil Laporan --}}
    @if(!empty($laporan) && count($laporan) > 0)
        <div class="mt-6">
            @if ($jenisBarang === 'habis_pakai')
                <h2 class="text-lg font-bold mb-3">Laporan Barang Habis Pakai</h2>
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">Nama Barang</th>
                            <th class="px-4 py-2 border">Total Keluar</th>
                            <th class="px-4 py-2 border">Satuan</th>
                            <th class="px-4 py-2 border">Total Stok</th>
                            <th class="px-4 py-2 border">Batas Minimum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($laporan as $item)
                            <tr>
                                <td class="px-4 py-2 border">{{ $item->nama_barang }}</td>
                                <td class="px-4 py-2 border text-center">{{ $item->total_keluar }}</td>
                                <td class="px-4 py-2 border text-center">{{ $item->satuan }}</td>
                                <td class="px-4 py-2 border text-center">{{ $item->total_stok }}</td>
                                <td class="px-4 py-2 border text-center">{{ $item->batas_minimum }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <h2 class="text-lg font-bold mb-3">Laporan Barang Tidak Habis Pakai</h2>
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">Nama Barang</th>
                            <th class="px-4 py-2 border">Total Dipinjam</th>
                            <th class="px-4 py-2 border">Satuan</th>
                            <th class="px-4 py-2 border">Kembali Baik</th>
                            <th class="px-4 py-2 border">Kembali Kurang Baik</th>
                            <th class="px-4 py-2 border">Kembali Tidak Baik</th>
                            <th class="px-4 py-2 border">Total Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($laporan as $item)
                            <tr>
                                <td class="px-4 py-2 border">{{ $item->nama_barang }}</td>
                                <td class="px-4 py-2 border text-center">{{ $item->total_dipinjam }}</td>
                                <td class="px-4 py-2 border text-center">{{ $item->satuan }}</td>
                                <td class="px-4 py-2 border text-center">{{ $item->kembali_baik }}</td>
                                <td class="px-4 py-2 border text-center">{{ $item->kembali_kurang_baik }}</td>
                                <td class="px-4 py-2 border text-center">{{ $item->kembali_tidak_baik }}</td>
                                <td class="px-4 py-2 border text-center">{{ $item->total_stok }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endif
</x-filament::page>
