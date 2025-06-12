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
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f4f4f4;
    margin: 0;
    color: #333;
  }

  .sidebar {
    width: 230px;
    background-color: #081F5C;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    padding-top: 20px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .sidebar .logo {
    margin-bottom: 20px;
  }

  .sidebar .logo img {
    width: 130px;
  }

  .sidebar nav ul {
    list-style: none;
    padding: 0;
    width: 100%;
  }

  .sidebar nav ul li {
    margin-bottom: 15px;
  }

  .sidebar nav ul li a {
    color: white;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
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

  .action a {
    margin: 0 5px;
    text-decoration: none;
    color: #000;
    font-weight: 600;
    transition: opacity 0.3s ease;
  }

  .action a:hover {
    opacity: 0.7;
  }

  .no-data {
    text-align: center;
    color: #555;
    padding: 20px;
  }

.status-dropdown {
  padding: 5px;
  border-radius: 8px;
  font-weight: bold;
}

.status-dropdown.belum-diproses {
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

  .status-belum {
    background-color: #ffd1d1; /* merah muda */
    color: #b30000;
    font-weight: bold;
}

.status-sudah {
    background-color: #d4f8d4; /* hijau muda */
    color: #007a00;
    font-weight: bold;
}

.status-gagal {
    background-color: #ffe9b3; /* kuning muda */
    color: #b38f00;
    font-weight: bold;
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
</head>
<body>
    <div class="main-content">
    <div class="container">
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
                  <form action="update_status_pengiriman.php" method="POST">
                  <select class="status-dropdown" data-id="<?= $pesanan['id_pesanan'] ?>">
                 <option value="Belum Diproses" <?= $pesanan['status_pengiriman'] == 'Belum Diproses' ? 'selected' : '' ?>>Belum Diproses</option>
                 <option value="Dikemas" <?= $pesanan['status_pengiriman'] == 'Dikemas' ? 'selected' : '' ?>>Dikemas</option>
                 <option value="Dikirim" <?= $pesanan['status_pengiriman'] == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                 <option value="Selesai" <?= $pesanan['status_pengiriman'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
             </select>
             </form>
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
                     <form action="update_status_pembayaran.php" method="POST">
                          <input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>">
                         <select name="status_pembayaran" class="status-select" onchange="this.form.submit()"
                         data-status="<?= $pesanan['status_pembayaran'] ?>">
                         <option value="belum bayar" <?= $pesanan['status_pembayaran'] == 'belum bayar' ? 'selected' : '' ?>>Belum Bayar</option>
                         <option value="sudah bayar" <?= $pesanan['status_pembayaran'] == 'sudah bayar' ? 'selected' : '' ?>>Sudah Bayar</option>
                        <option value="gagal" <?= $pesanan['status_pembayaran'] == 'gagal' ? 'selected' : '' ?>>Gagal</option>
                         </select>
                       </form>
                 </td>

                <td><?= $pesanan['metode_pembayaran'] ?></td>
                <td class="aksi-btn">
                    <form action="detail_pesanan.php" method="GET" style="display:inline;">
                        <input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>">
                        <button>Lihat</button>
                    </form> 
                    <form action="hapus_pesanan.php" method="POST">
                        <input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>">
                        <button onclick="return confirm('Yakin mau hapus?')">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
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
      body: id_pesanan=${idPesanan}&status_pengiriman=${encodeURIComponent(statusBaru)}
    })
    .then(res => res.text())
    .then(res => {
      if (res === 'ok') {
        this.className = 'status-dropdown ' + statusBaru.replace(/\s/g, '-').toLowerCase();
      }
    });
  });
});
</script>

            <script>
document.querySelectorAll('.status-select').forEach(select => {
    const status = select.dataset.status;

    // Reset class dulu
    select.classList.remove('status-belum', 'status-sudah', 'status-gagal');

    // Tambahkan class sesuai status
    if (status === 'belum bayar') {
        select.classList.add('status-belum');
    } else if (status === 'sudah bayar') {
        select.classList.add('status-sudah');
    } else if (status === 'gagal') {
        select.classList.add('status-gagal');
    }
});
</script>

        </tbody>
    </table>
</body>
</html>