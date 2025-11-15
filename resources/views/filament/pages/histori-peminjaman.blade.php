<x-filament::page>
    <form wire:submit.prevent="generateReport" class="space-y-4">
        {{ $this->form }}

        <div class="flex gap-3">
            <x-filament::button type="submit">Tampilkan Histori</x-filament::button>
            <x-filament::button type="button" wire:click="resetFilter" color="secondary">
                Reset Filter
            </x-filament::button>
        </div>
    </form>

    <!-- Peminjaman Mahasiswa (Web Mobile) -->
    <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-100 mb-4">
            <i class="fas fa-graduation-cap mr-2"></i>Peminjaman Mahasiswa (Web Mobile)
        </h2>
        
        @if($this->peminjamanMahasiswa->count())
            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-700 bg-gray-900 text-white">
                    <thead class="bg-gray-800">
                        <tr>
                            <th class="px-4 py-2">Pemilik Akun</th>
                            <th class="px-4 py-2">Nama Peminjam</th>
                            <th class="px-4 py-2">NIM</th>
                            <th class="px-4 py-2">Items</th>
                            <th class="px-4 py-2">Tanggal</th>
                            <th class="px-4 py-2">Jam Pinjam</th>
                            <th class="px-4 py-2">Mata Kuliah</th>
                            <th class="px-4 py-2">Kelas</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Disetujui Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->peminjamanMahasiswa as $row)
                            @php
                                $statusKeseluruhan = $row->status_keseluruhan;
                            @endphp
                            <tr class="border-t border-gray-700 hover:bg-gray-800">
                                <td class="px-4 py-2">{{ $row->nama_mahasiswa }}</td>
                                <td class="px-4 py-2">{{ $row->nama_peminjam ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $row->nim }}</td>
                                <td class="px-4 py-2">
                                    <div class="space-y-1">
                                        @foreach($row->items as $item)
                                        <div class="text-xs">
                                            <span class="font-semibold">{{ $item->nama_barang }}</span>
                                            <span class="text-gray-400">({{ $item->jumlah }} {{ $item->satuan }})</span>
                                            <span class="ml-1 px-1.5 py-0.5 rounded text-xs
                                                {{ $item->jenis_barang === 'habis_pakai' ? 'bg-orange-900/30 text-orange-400' : 'bg-blue-900/30 text-blue-400' }}">
                                                {{ $item->jenis_barang === 'habis_pakai' ? 'C' : 'NC' }}
                                            </span>
                                            <span class="ml-1 px-1.5 py-0.5 rounded text-xs
                                                {{ $item->status === 'pending' ? 'bg-yellow-900/30 text-yellow-400' : 
                                                   ($item->status === 'approved' ? 'bg-green-900/30 text-green-400' : 
                                                   ($item->status === 'rejected' ? 'bg-red-900/30 text-red-400' : 'bg-blue-900/30 text-blue-400')) }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-2">
                                    {{ $row->tanggal_pinjam->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ $row->jam_pinjam ? date('H:i', strtotime($row->jam_pinjam)) : '-' }}
                                </td>
                                <td class="px-4 py-2">{{ $row->mata_kuliah ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $row->kelas ?? '-' }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 rounded text-sm
                                        @if($statusKeseluruhan === 'pending') bg-yellow-600
                                        @elseif($statusKeseluruhan === 'approved') bg-green-600
                                        @elseif($statusKeseluruhan === 'rejected') bg-red-600
                                        @else bg-blue-600
                                        @endif">
                                        {{ ucfirst($statusKeseluruhan) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2">
                                    {{ $row->approver->nama_user ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $this->peminjamanMahasiswa->links() }}
            </div>
        @else
            <p class="text-gray-400 mt-4">Belum ada histori peminjaman mahasiswa.</p>
        @endif
    </div>

    <!-- Peminjaman Reguler (Admin/Staf) -->
    <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-100 mb-4">
            <i class="fas fa-users mr-2"></i>Peminjaman Reguler (Admin/Staf)
        </h2>
        
        @if($this->peminjaman->count())
            <div class="mb-3 text-sm text-gray-400">
                @if($showReport)
                    Periode: <strong>{{ ucfirst($periode) }}</strong>
                @else
                    Menampilkan histori sebelum: <strong>{{ now()->format('d/m/Y') }}</strong>
                @endif
            </div>

            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-700 bg-gray-900 text-white">
                    <thead class="bg-gray-800">
                        <tr>
                            <th class="px-4 py-2">Nama Peminjam</th>
                            <th class="px-4 py-2">Jenis</th>
                            <th class="px-4 py-2">Barang</th>
                            <th class="px-4 py-2">Jumlah</th>
                            <th class="px-4 py-2">Satuan</th>
                            <th class="px-4 py-2">Kelas</th>
                            <th class="px-4 py-2">Tanggal Pinjam</th>
                            <th class="px-4 py-2">Status</th>
                            @if(!$jenis_barang || $jenis_barang === 'tidak_habis_pakai')
                                <th class="px-4 py-2">Kondisi Kembali</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->peminjaman as $row)
                            <tr class="border-t border-gray-700">
                                <td class="px-4 py-2">{{ $row->nama_peminjam }}</td>
                                <td class="px-4 py-2 capitalize">
                                    {{ str_replace('_', ' ', $row->jenis_barang) }}
                                </td>
                                <td class="px-4 py-2">
                                    @if($row->jenis_barang === 'habis_pakai')
                                        {{ $row->barangHabisPakai->nama_barang ?? '-' }}
                                    @elseif($row->jenis_barang === 'tidak_habis_pakai')
                                        {{ $row->barangTidakHabisPakai->nama_barang ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ $row->jumlah }}</td>
                                <td class="px-4 py-2">{{ $row->satuan ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $row->kelas ?? '-' }}</td>
                                <td class="px-4 py-2">
                                    {{ \Carbon\Carbon::parse($row->tanggal_pinjam)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 rounded text-sm
                                        @if($row->status === 'Dipinjam') bg-yellow-600 @else bg-green-600 @endif">
                                        {{ $row->status }}
                                    </span>
                                </td>

                                @if(!$jenis_barang || $row->jenis_barang === 'tidak_habis_pakai')
                                    <td class="px-4 py-2">
                                        {{ $row->kondisi_kembali ? ucfirst(str_replace('_', ' ', $row->kondisi_kembali)) : '-' }}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $this->peminjaman->links() }}
            </div>
        @else
            <p class="text-gray-400 mt-4">Belum ada histori peminjaman sebelum hari ini.</p>
        @endif
    </div>
</x-filament::page>
