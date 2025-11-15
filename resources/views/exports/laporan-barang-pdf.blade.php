<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Barang</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
        }
        h2 {
            text-align: center;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #444;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Laporan Barang {{ $jenisBarang === 'habis_pakai' ? 'Habis Pakai' : 'Tidak Habis Pakai' }}</h2>
    <p>
        Periode: {{ $periode }} <br>
        Dari: {{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }}
        s/d {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d/m/Y') }}
    </p>

    @if($jenisBarang === 'habis_pakai')
        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Total Keluar</th>
                    <th>Satuan</th>
                    <th>Total Stok</th>
                    <th>Batas Minimum</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporan as $item)
                    <tr>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->total_keluar }}</td>
                        <td>{{ $item->satuan }}</td>
                        <td>{{ $item->total_stok }}</td>
                        <td>{{ $item->batas_minimum }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Total Dipinjam</th>
                    <th>Satuan</th>
                    <th>Kembali Baik</th>
                    <th>Kembali Kurang Baik</th>
                    <th>Kembali Tidak Baik</th>
                    <th>Total Stok</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporan as $item)
                    <tr>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->total_dipinjam }}</td>
                        <td>{{ $item->satuan }}</td>
                        <td>{{ $item->kembali_baik }}</td>
                        <td>{{ $item->kembali_kurang_baik }}</td>
                        <td>{{ $item->kembali_tidak_baik }}</td>
                        <td>{{ $item->total_stok }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
