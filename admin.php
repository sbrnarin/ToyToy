<?php
include "koneksi.php";
$query = mysqli_query($koneksi, "SELECT * FROM produk");
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
      background-color: #e2d2c2;
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
      color: #000;
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
      background-color: #d6c0ae;
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
      background:#d6bfae;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      margin-bottom: 20px;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }

    .btn-tambah:hover {
      background-color:#bfa791;
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
      background-color:#d6bfae;
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
        <th>Id Produk</th>
        <th>Nama Produk</th>
        <th>Gambar</th>
        <th>Harga</th>
        <th>Stok</th>
        <th>Id Merk</th>
        <th>Id Kategori</th>
        <th>Tanggal Masuk</th>
        <th>Aksi</th>
      </tr>
      <?php while($produk = mysqli_fetch_assoc($query)) { ?>
      <tr>
        <td><?=$produk['id']?></td>
        <td><?=$produk['nama_produk']?></td>
        <td>
          <?php if (!empty($produk['gambar'])) { ?>
            <img src="gambar_produk/<?=$produk['gambar']?>" width="100" />
          <?php } else { ?>
            <span>-</span>
          <?php } ?>
        </td>
        <td><?=$produk['harga']?></td>
        <td><?=$produk['stok']?></td>
        <td><?=$produk['id_merk']?></td>
        <td><?=$produk['id_kategori']?></td>
        <td><?=$produk['tanggal_masuk']?></td>
        <td class="action">
          <a href="edit.php?id=<?=$produk['id']?>">Edit</a> |
          <a href="hapus.php?id=<?=$produk['id']?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
        </td>
      </tr>
      <?php } ?>
    </table>
  </div>
</body>
</html>
