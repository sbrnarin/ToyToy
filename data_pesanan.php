<?php
include "koneksi.php";

// Ambil data pesanan + nama pembeli
$sql = "SELECT pesanan.*, pembeli.nama_pembeli 
        FROM pesanan 
        JOIN pembeli ON pesanan.id_pembeli = pembeli.id_pembeli";
$query = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pesanan - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f1f1f1;
        margin: 0;
        color: #333;
    }

    .sidebar {
        width: 230px;
        background-color: #081F5C;
        height: 100vh;
        position: fixed;
        padding: 20px;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .sidebar .logo img {
        width: 130px;
        margin-bottom: 30px;
    }

    .sidebar nav ul {
        list-style: none;
        padding: 0;
        width: 100%;
    }

    .sidebar nav ul li {
        margin-bottom: 20px;
    }

    .sidebar nav ul li a {
        color: white;
        text-decoration: none;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 15px;
        border-radius: 8px;
        transition: background-color 0.3s;
    }

    .sidebar nav ul li a:hover {
        background-color: #000000;
    }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .main-content {
            margin-left: 250px;
            padding: 40px;
            min-height: 100vh;
            box-sizing: border-box;
            background-color: #f9f9f9;
        }
        h2 {
            margin-top: 0;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 90%;
            margin: 0 auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #081F5C;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .status-dropdown,
        .status-select {
            padding: 5px;
            border-radius: 8px;
            font-weight: bold;
        }
        .status-dropdown.belum-diproses,
        .status-select.belum-bayar {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-dropdown.dikemas {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-dropdown.dikirim {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-dropdown.selesai {
            background-color: #d4edda;
            color: #155724;
        }
        .status-select.sudah-bayar {
            background-color: #d4f8d4;
            color: #007a00;
        }
        .status-select.gagal {
            background-color:rgb(245, 184, 184);
            color:rgb(255, 0, 0);
        }
        .aksi-btn form {
            display: inline-block;
            margin: 2px;
        }

        .aksi-btn button {
            padding: 6px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
        }

        .aksi-btn button:hover {
            opacity: 0.85;
        }

        .aksi-btn button.lihat {
            background-color: #007bff;
            color: white;
        }

        .aksi-btn button.hapus {
            background-color: #dc3545;
            color: white;
        }

    </style>
</head>
<body>
<aside class="sidebar">
    <div class="logo">
      <img src="gambar/Kids Toys Logo (1).png" alt="Kids Toys Logo">
    </div>
    <nav>
      <ul>
        <li><a href="dash_admin.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li><a href="admin.php"><i class="fas fa-box"></i> Product</a></li>
        <li><a href="akun_admin.php"><i class="fas fa-user"></i> Account</a></li>
      </ul>
    </nav>
  </aside>

<div class="main-content">
    <div class="container">
        <h2>Data Pesanan</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Pesanan</th>
                    <th>Nama Pembeli</th>
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
                    <td><?= htmlspecialchars($pesanan['nama_pembeli']) ?></td>
                    <td><?= $pesanan['tanggal_pesan'] ?></td>
                    <td><?= $pesanan['total_produk'] ?></td>
                    <td><?= $pesanan['total_harga'] ?></td>
                    <td>
                        <?php
                        $statusClass = match (strtolower(trim($pesanan['status_pengiriman']))) {
                            'belum diproses' => 'belum-diproses',
                            'dikemas' => 'dikemas',
                            'dikirim' => 'dikirim',
                            'selesai' => 'selesai',
                            default => 'belum-diproses'
                        };
                        ?>
                        <select class="status-dropdown <?= $statusClass ?>" data-id="<?= $pesanan['id_pesanan'] ?>">
                            <option value="Belum Diproses" <?= $pesanan['status_pengiriman'] == 'Belum Diproses' ? 'selected' : '' ?>>Belum Diproses</option>
                            <option value="Dikemas" <?= $pesanan['status_pengiriman'] == 'Dikemas' ? 'selected' : '' ?>>Dikemas</option>
                            <option value="Dikirim" <?= $pesanan['status_pengiriman'] == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                            <option value="Selesai" <?= $pesanan['status_pengiriman'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </td>
                    <td>
                        <?php if ($pesanan['bukti_pembayaran']) : ?>
                            <a href="uploads/<?= $pesanan['bukti_pembayaran'] ?>" target="_blank">
                                <img src="uploads/bukti_pembayaran/<?= $pesanan['bukti_pembayaran'] ?>" alt="Bukti Transfer" width="60" height="60" style="object-fit: cover; border-radius: 6px;">
                            </a>
                        <?php else : ?>
                            <span style="color:#999;">Belum Upload</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $statusBayarClass = match (strtolower(trim($pesanan['status_pembayaran']))) {
                            'belum bayar' => 'belum-bayar',
                            'sudah bayar' => 'sudah-bayar',
                            'gagal' => 'gagal',
                            default => 'belum-bayar'
                        };
                        ?>
                        <form action="update_status_pembayaran.php" method="POST">
                            <input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>">
                            <select name="status_pembayaran" class="status-select <?= $statusBayarClass ?>" onchange="this.form.submit()">
                                <option value="belum bayar" <?= $pesanan['status_pembayaran'] == 'belum bayar' ? 'selected' : '' ?>>Belum Bayar</option>
                                <option value="sudah bayar" <?= $pesanan['status_pembayaran'] == 'sudah bayar' ? 'selected' : '' ?>>Sudah Bayar</option>
                                <option value="gagal" <?= $pesanan['status_pembayaran'] == 'gagal' ? 'selected' : '' ?>>Gagal</option>
                            </select>
                        </form>
                    </td>
                    <td><?= $pesanan['metode_pembayaran'] ?></td>
                    <td>
                        <form action="detail_pesanan.php" method="GET">
                            <input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>">
                            <button class="lihat">Lihat</button>
                        </form> 
                        <form action="hapus_pesanan.php" method="POST" onsubmit="return confirm('Yakin mau hapus?')">
                            <input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>">
                            <button class="hapus">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.querySelectorAll('.status-dropdown').forEach(select => {
    select.addEventListener('change', function () {
        const idPesanan = this.getAttribute('data-id');
        const statusBaru = this.value;

        fetch('update_status_pengiriman.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_pesanan=${idPesanan}&status_pengiriman=${encodeURIComponent(statusBaru)}`
        })
        .then(res => res.text())
        .then(res => {
            if (res === 'ok') {
                this.className = 'status-dropdown ' + statusBaru.replace(/\s/g, '-').toLowerCase();
            } else {
                alert('Gagal menyimpan status.');
            }
        });
    });
});
</script>

</body>
</html>