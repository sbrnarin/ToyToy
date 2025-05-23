<?php
include "koneksi.php";

$sql = "SELECT * FROM produk";
$query = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Daftar Produk</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
    }

    .sidebar {
      position: fixed;
      width: 200px;
      height: 100%;
      background-color: #e5d3bc;
      padding: 20px;
    }

    .sidebar .logo {
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 40px;
    }

    .sidebar nav ul {
      list-style: none;
      padding: 0;
    }

    .sidebar nav ul li {
      margin: 20px 0;
      font-size: 16px;
      cursor: pointer;
    }

    .main-content {
      margin-left: 220px;
      padding: 30px;
    }

    .order-dashboard {
      background-color: #f4f4f4;
      padding: 20px;
      border-radius: 10px;
    }

    .order-dashboard h2 {
      font-size: 24px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
    }

    .order-dashboard h2 i {
      margin-right: 10px;
      font-size: 28px;
      color: #d49f00;
    }

    .order-boxes {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
      margin-bottom: 30px;
    }

    .order-box {
      flex: 1;
      min-width: 180px;
      background-color: rgb(207, 196, 186);
      border-radius: 10px;
      padding: 15px;
      box-shadow: 0 2px 8px rgb(207, 196, 186);
      text-align: center;
    }

    .order-label {
      font-weight: bold;
      margin-top: 5px;
    }

    .order-count {
      font-size: 20px;
      font-weight: bold;
    }

    .table-container {
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    table th, table td {
      padding: 10px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    table th {
      background-color:rgb(207, 196, 186);;
      color: white;
    }

    table tr:hover {
      background-color: #f9f9f9;
    }

    .action-icons a {
      font-size: 18px;
      margin-right: 8px;
      text-decoration: none;
      color: #444;
    }

    .action-icons a:hover {
      opacity: 0.7;
    }

    .btn-tambah {
      display: inline-block;
      margin-bottom: 15px;
      background: #e5d3bc;
      padding: 8px 12px;
      border-radius: 5px;
      text-decoration: none;
      color: #000;
      font-weight: bold;
    }

    .btn-tambah:hover {
      background:rgb(239, 224, 204);
    }
  </style>
</head>
<body>

  <aside class="sidebar">
    <div class="logo">KIDS TOYS</div>
    <nav>
      <ul>
        <li>🏠 Dashboard</li>
        <li>🛒 Order</li>
        <li>📦 Product</li>
        <li>👤 Account</li>
      </ul>
    </nav>
  </aside>

  <div class="main-content">
    <div class="order-dashboard">
      <h2><i class="fas fa-box"></i> Daftar Produk</h2>
      <div class="order-boxes">
        <div class="order-box order-total">
          <i class="fas fa-list"></i>
          <div class="order-label">Total Produk</div>
          <div class="order-count"><?= mysqli_num_rows($query); ?></div>
        </div>
      </div>
    </div>

    <a href="tambah.php" class="btn-tambah">➕ Tambah Produk</a>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Id Produk</th>
            <th>Nama Produk</th>
            <th>Deskripsi</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Id Merk</th>
            <th>Id Kategori</th>
            <th>Tanggal Masuk</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          mysqli_data_seek($query, 0); // Reset pointer
          while ($produk = mysqli_fetch_assoc($query)) { ?>
            <tr>
              <td><?= $produk['id_produk'] ?></td>
              <td><?= $produk['nama_produk'] ?></td>
              <td><?= $produk['deskripsi'] ?></td>
              <td>Rp<?= number_format($produk['harga'], 0, ',', '.') ?></td>
              <td><?= $produk['stok'] ?></td>
              <td><?= $produk['id_merk'] ?></td>
              <td><?= $produk['id_kategori'] ?></td>
              <td><?= $produk['tanggal_masuk'] ?></td>
              <td class="action-icons">
                <a href="edit.php?id_produk=<?= $produk['id_produk'] ?>" title="Edit">✏️</a>
                <a href="hapus.php?id_produk=<?= $produk['id_produk'] ?>" title="Hapus" onclick="return confirm('Hapus produk ini?')">🗑️</a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>
