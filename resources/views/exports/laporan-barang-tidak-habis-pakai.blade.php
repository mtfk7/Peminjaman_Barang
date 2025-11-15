<!DOCTYPE html>
<html>
<head>
    <title>Laporan Barang Tidak Habis Pakai</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Laporan Barang Tidak Habis Pakai</h2>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Jumlah Stok</th>
                <th>Satuan</th>
                <th>Baik</th>
                <th>Kurang Baik</th>
                <th>Tidak Baik</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $item)
            <tr>
                <td>{{ $item->kode_barang }}</td>
                <td>{{ $item->nama_barang }}</td>
                <td>{{ $item->jumlah_stok }}</td>
                <td>{{ $item->satuan }}</td>
                <td>{{ $item->kondisi_baik }}</td>
                <td>{{ $item->kondisi_kurang_baik }}</td>
                <td>{{ $item->kondisi_tidak_baik }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
