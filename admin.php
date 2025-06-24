<?php
include "koneksi.php";

// Validasi stok saat form disubmit (untuk edit produk)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['stok'])) {
    if ($_POST['stok'] < 0) {
        die("<script>alert('Stok tidak boleh minus!');history.back();</script>");
    }
}

$query = mysqli_query($koneksi, "
  SELECT p.*, m.nama_merk, k.nama_kategori
  FROM produk p
  JOIN merk m ON p.id_merk = m.id_merk
  JOIN kategori k ON p.id_kategori = k.id_kategori
  ORDER BY p.id_produk ASC
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Data Produk</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

    .main-content {
      margin-left: 250px;
      padding: 30px 40px;
    }

    h1 {
      margin-top: 0;
      font-weight: 700;
    }

    .btn-tambah {
      display: inline-block;
      padding: 10px 20px;
      background: #081F5C;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      margin-bottom: 20px;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }

    .btn-tambah:hover {
      background-color: #000000;
    }

    table {
      width: 100%;
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
    
    .stok-habis {
      color: red;
      font-weight: bold;
    }
    
    .stok-sedikit {
      color: orange;
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

  <div class="main-content">
    <h1>Data Produk</h1>
    <a href="tambah.php" class="btn-tambah">Tambah Produk</a>
    <table>
      <tr>
        <th>ID Produk</th>
        <th>Nama Produk</th>
        <th>Deskripsi</th>
        <th>Harga</th>
        <th>Stok</th>
        <th>Nama Merk</th>
        <th>Nama Kategori</th>
        <th>Tanggal Masuk</th>
        <th>Gambar</th>
        <th>Aksi</th>
      </tr>
      <?php while($produk = mysqli_fetch_assoc($query)): ?>
      <tr>
        <td><?=htmlspecialchars($produk['id_produk'])?></td>
        <td><?=htmlspecialchars($produk['nama_produk'])?></td>
        <td><?=htmlspecialchars($produk['deskripsi'])?></td>
        <td>Rp <?=number_format($produk['harga'], 0, ',', '.')?></td>
        <?php
$class = '';
if ($produk['stok'] <= 0) {
    $class = 'stok-habis';
} elseif ($produk['stok'] < 5) {
    $class = 'stok-sedikit';
}
?>
<td class="<?= $class ?>">
  <?= htmlspecialchars($produk['stok']) ?>
</td>
        <td><?=htmlspecialchars($produk['nama_merk'])?></td>
        <td><?=htmlspecialchars($produk['nama_kategori'])?></td>
        <td><?=htmlspecialchars($produk['tanggal_masuk'])?></td>
        <td>
          <?php if (!empty($produk['nama_file'])): ?>
            <img src="gambar/<?=htmlspecialchars($produk['nama_file'])?>" width="100" />
          <?php else: ?>
            <span>-</span>
          <?php endif; ?>
        </td>
        <td class="action">
          <a href="edit.php?id_produk=<?=$produk['id_produk']?>">Edit</a> |
          <a href="hapus.php?id_produk=<?=$produk['id_produk']?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const forms = document.querySelectorAll('form[action="edit.php"]');
      forms.forEach(form => {
        form.addEventListener('submit', function(e) {
          const stokInput = form.querySelector('input[name="stok"]');
          if (parseInt(stokInput.value) < 0) {
            e.preventDefault();
            alert('Stok tidak boleh minus!');
            stokInput.focus();
          }
        });
      });
    });
  </script>
</body>
</html>