<x-filament::page>
    <form wire:submit.prevent="generateReport" class="space-y-4">
        {{ $this->form }}

        <div class="flex gap-3">
            <x-filament::button type="submit" color="warning">Tampilkan Laporan</x-filament::button>
            <x-filament::button color="success" wire:click="exportPdf">Cetak PDF</x-filament::button>
        </div>
    </form>

    @if(!empty($laporan))
        <div class="mt-6">
            <h2 class="text-lg font-bold mb-3">Hasil Laporan Barang Masuk</h2>

            <table class="min-w-full border border-gray-700 text-sm">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="border px-3 py-2 text-left">No</th>
                        <th class="border px-3 py-2 text-left">Nama Barang</th>
                        <th class="border px-3 py-2 text-left">Jenis Barang</th>
                        <th class="border px-3 py-2 text-left">Jumlah Masuk</th>
                        <th class="border px-3 py-2 text-left">Satuan</th> <!-- ✅ Tambah kolom satuan -->
                        <th class="border px-3 py-2 text-left">Total Stok</th> <!-- ✅ Kolom total stok -->
                        <th class="border px-3 py-2 text-left">Tanggal Masuk</th>
                        <th class="border px-3 py-2 text-left">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporan as $index => $item)
                        <tr class="border-b border-gray-700">
                            <td class="border px-3 py-2">{{ $index + 1 }}</td>
                            <td class="border px-3 py-2">{{ $item->nama_barang }}</td>
                            <td class="border px-3 py-2">
                                {{ $item->jenis_barang === 'habis' ? 'Barang Habis Pakai' : 'Barang Tidak Habis Pakai' }}
                            </td>
                            <td class="border px-3 py-2">{{ $item->jumlah_masuk }}</td>

                            <!-- ✅ Kolom satuan -->
                            <td class="border px-3 py-2">{{ $item->satuan ?? '-' }}</td>

                            <!-- ✅ Kolom total stok -->
                            <td class="border px-3 py-2">
                                @if($item->jenis_barang === 'habis' && $item->barangHabisPakai)
                                    {{ $item->barangHabisPakai->total_stok }}
                                @elseif($item->jenis_barang === 'tidak_habis' && $item->barangTidakHabisPakai)
                                    {{ $item->barangTidakHabisPakai->total_stok }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="border px-3 py-2">
                                {{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d M Y') }}
                            </td>
                            <td class="border px-3 py-2">{{ $item->keterangan ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-filament::page>
