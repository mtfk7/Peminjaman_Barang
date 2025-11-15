<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Barang</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Laporan Barang</h2>
    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Stok Awal</th>
                <th>Dipinjam</th>
                <th>Kembali (Baik)</th>
                <th>Kembali (Kurang Baik)</th>
                <th>Kembali (Tidak Baik)</th>
                <th>Stok Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($laporan as $item)
                <tr>
                    <td>{{ $item['nama_barang'] }}</td>
                    <td>{{ $item['stok_awal'] }}</td>
                    <td>{{ $item['dipinjam'] }}</td>
                    <td>{{ $item['baik'] }}</td>
                    <td>{{ $item['kurang_baik'] }}</td>
                    <td>{{ $item['tidak_baik'] }}</td>
                    <td>{{ $item['stok_akhir'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
