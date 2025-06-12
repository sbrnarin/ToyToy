<?php
include 'koneksi.php'; // file koneksi ke database

// Ambil semua detail pesanan, sekaligus join ke pesanan dan produk
$query = "
    SELECT dp.*, 
           p.tanggal_pesan, 
           p.status_pengiriman, 
           p.status_pembayaran, 
           p.metode_pembayaran,
           pr.nama_file
    FROM detail_pesanan dp
    JOIN pesanan p ON dp.id_pesanan = p.id_pesanan
    JOIN produk pr ON dp.id_produk = pr.id_produk
    ORDER BY dp.id_detail DESC
";

$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Pesanan</title>
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
    <h2>Data Detail Pesanan</h2>
    <table>
        <thead>
            <tr>
                <th>ID Detail</th>
                <th>ID Pesanan</th>
                <th>Id Produk</th>
                <th>Jumlah</th>
                <th>Harga Awal</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($data = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $data['id_detail'] ?></td>
                    <td><?= $data['id_pesanan'] ?></td>
                    <td><?= $data['id_produk'] ?></td>
                    <td><?= $data['jumlah'] ?></td>
                    <td><?= $data['harga_saat_beli'] ?></td>
                    <td><?= $data['subtotal'] ?></td>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</body>
</html>