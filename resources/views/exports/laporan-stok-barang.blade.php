<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Barang</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Laporan Stok Barang</h2>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Total Stok</th>
                <th>Baik</th>
                <th>Kurang Baik</th>
                <th>Tidak Baik</th>
                <th>Merk/Type</th>
                <th>Tahun Perolehan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->total_stok }}</td>
                    <td>{{ $item->kondisi_baik }}</td>
                    <td>{{ $item->kondisi_kurang_baik }}</td>
                    <td>{{ $item->kondisi_tidak_baik }}</td>
                    <td>{{ $item->merk }}</td>
                    <td>{{ $item->tahun_perolehan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
