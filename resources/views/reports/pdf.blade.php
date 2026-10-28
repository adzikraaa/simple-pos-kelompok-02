<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #222;
        }

        h2 {
            text-align: center;
            margin-bottom: 6px;
        }

        .info {
            text-align: center;
            margin-bottom: 20px;
        }

        .summary {
            margin-bottom: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #777;
            padding: 7px;
            text-align: left;
        }

        th {
            background-color: #eeeeee;
        }

        .number {
            text-align: right;
        }
    </style>
</head>
<body>
    <h2>Laporan Penjualan</h2>

    <div class="info">
        Periode: {{ $from }} sampai {{ $to }}
    </div>

    <div class="summary">
        <p>
            <strong>Total Penjualan:</strong>
            Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
        </p>

        <p>
            <strong>Jumlah Transaksi:</strong>
            {{ $jumlahTransaksi }}
        </p>
    </div>

    <h3>5 Produk Terlaris</h3>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Produk</th>
                <th>Total Terjual</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topProducts as $product)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $product->name }}</td>
                    <td class="number">
                        {{ number_format($product->total_terjual, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center;">
                        Tidak ada data produk terjual pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tanggal</th>
                <th>Kasir</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->created_at }}</td>
                    <td>{{ $item->user->name ?? '-' }}</td>
                    <td class="number">
                        Rp {{ number_format($item->total, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">
                        Tidak ada transaksi pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>