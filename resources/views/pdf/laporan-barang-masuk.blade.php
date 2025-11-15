<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Barang Masuk</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #eee; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Laporan Barang Masuk</h2>

    <p><strong>Periode:</strong>
        {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d M Y') }}
        - {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d M Y') }}
    </p>

    @if($jenis_barang && $jenis_barang !== 'semua')
        <p><strong>Jenis Barang:</strong>
            {{ $jenis_barang === 'habis' ? 'Barang Habis Pakai' : 'Barang Tidak Habis Pakai' }}
        </p>
    @else
        <p><strong>Jenis Barang:</strong> Semua Jenis</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Jenis Barang</th>
                <th>Jumlah Masuk</th>
                <th>Satuan</th> <!-- ✅ Kolom baru -->
                <th>Total Stok</th> <!-- ✅ Kolom baru -->
                <th>Tanggal Masuk</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->jenis_barang === 'habis' ? 'Barang Habis Pakai' : 'Barang Tidak Habis Pakai' }}</td>
                    <td>{{ $item->jumlah_masuk }}</td>
                    <td>{{ $item->satuan ?? '-' }}</td>
                    <td>
                        @if($item->jenis_barang === 'habis' && $item->barangHabisPakai)
                            {{ $item->barangHabisPakai->total_stok }}
                        @elseif($item->jenis_barang === 'tidak_habis' && $item->barangTidakHabisPakai)
                            {{ $item->barangTidakHabisPakai->total_stok }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d M Y') }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
