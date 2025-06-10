<?php
include "koneksi.php";
$sql = "SELECT * FROM pesanan";
$query = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pesanan - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f4f4f4;
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #002B5B;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        select, button {
            padding: 6px 10px;
            border-radius: 4px;
            border: 1px solid #aaa;
        }
    </style>
</head>
<body>
    <h2>Data Pesanan</h2>
    <table>
        <thead>
            <tr>
                <th>ID Pesanan</th>
                <th>ID User</th>
                <th>Tanggal Pesan</th>
                <th>Total Produk</th>
                <th>Total Harga</th>
                <th>Status Pengiriman</th>
                <th>Bukti Transfer</th>
                <th>Status Pembayaran</th>
                <th>Metode Pembayaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($pesanan = mysqli_fetch_assoc($query)) { ?>
            <tr>
                <td><?= $pesanan['id_pesanan'] ?></td>
                <td><?= $pesanan['id_pembeli'] ?></td>
                <td><?= $pesanan['tanggal_pesan'] ?></td>
                <td><?= $pesanan['total_produk'] ?></td>
                <td><?= $pesanan['total_harga'] ?></td>
                <td>
                    <form action="ubah_status.php" method="POST">
                        <input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>">
                        <select name="status_pengiriman">
                            <option value="Belum Diproses" <?= $pesanan['status_pengiriman'] == 'Belum Diproses' ? 'selected' : '' ?>>Belum Diproses</option>
                            <option value="Dikemas" <?= $pesanan['status_pengiriman'] == 'Dikemas' ? 'selected' : '' ?>>Dikemas</option>
                            <option value="Dikirim" <?= $pesanan['status_pengiriman'] == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                            <option value="Selesai" <?= $pesanan['status_pengiriman'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                        <button type="submit">Ubah</button>
                    </form>
                </td>
                <td><?= $pesanan['bukti_transfer'] ? "<a href='uploads/{$pesanan['bukti_transfer']}' target='_blank'>Lihat</a>" : '-' ?></td>
                <td><?= $pesanan['status_pembayaran'] ?></td>
                <td><?= $pesanan['metode_pembayaran'] ?></td>
                <td class="aksi-btn">
                    <!-- <form action="detail_pesanan.php" method="GET" style="display:inline;">
                        <input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>">
                        <button>Lihat</button>
                    </form> -->
                    <form action="hapus_pesanan.php" method="POST" style="display:inline;">
                        <input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>">
                        <button onclick="return confirm('Yakin mau hapus?')">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
