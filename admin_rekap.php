<?php
include 'koneksi.php';

$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

$sql = "
SELECT 
  p.nama_produk,
  m.nama_merk,
  k.nama_kategori,
  SUM(dp.jumlah) AS total_terjual,
  SUM(dp.jumlah * p.harga) AS total_pendapatan
FROM detail_penjualan dp
JOIN produk p ON dp.id_produk = p.id_produk
JOIN penjualan pj ON dp.id_jual = pj.id_jual
LEFT JOIN merk m ON p.id_merk = m.id_merk
LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
WHERE MONTH(pj.tanggal) = ? AND YEAR(pj.tanggal) = ?
GROUP BY dp.id_produk
ORDER BY total_terjual DESC
";



$stmt = $koneksi->prepare($sql);
$stmt->bind_param("ss", $bulan, $tahun);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Rekap Penjualan Produk</title>
  <style>
    body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f4f4f4;
    margin: 30px;
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
    /* body {
      font-family: Arial, sans-serif;
      background-color: #f4f6fa;
      margin: 0;
      padding: 20px;
    } */
     
    .container {
      background: white;
      border: 1px solid #ccc;
      padding: 20px;
      border-radius: 10px;
      max-width: 800px;
      margin: auto;
    }
    h2 {
      color: #081F5C;
      margin-bottom: 20px;
      text-align: center;
    }
    form {
      margin-bottom: 20px;
      display: flex;
      justify-content: center;
      gap: 10px;
    }
    select, button {
      padding: 5px 10px;
      border-radius: 5px;
      border: 1px solid #081F5C;
    }
    button {
      background-color: #081F5C;
      color: white;
      cursor: pointer;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    th {
      background-color: #081F5C;
      color: white;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: center;
    }
    tr:nth-child(even) {
      background-color: #f0f4fc;
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
  <div class="container">
    <h2>Rekap Penjualan Produk</h2>

    <form method="GET">
      <label for="bulan">Bulan:</label>
      <select name="bulan" id="bulan">
        <?php
        for ($i = 1; $i <= 12; $i++) {
          $val = str_pad($i, 2, '0', STR_PAD_LEFT);
          $sel = ($bulan == $val) ? 'selected' : '';
          echo "<option value='$val' $sel>$val</option>";
        }
        ?>
      </select>

      <label for="tahun">Tahun:</label>
      <select name="tahun" id="tahun">
        <?php
        $tahunSekarang = date('Y');
        for ($y = $tahunSekarang; $y >= $tahunSekarang - 5; $y--) {
          $sel = ($tahun == $y) ? 'selected' : '';
          echo "<option value='$y' $sel>$y</option>";
        }
        ?>
      </select>

      <button type="submit">Tampilkan</button>
    </form>

    <table>
      <tr>
        <th>No</th>
        <th>Nama Produk</th>
        <th>Merk</th>
        <th>Kategori</th>
        <th>Jumlah Terjual</th>
        <th>Total Pendapatan</th>
      </tr>
      <?php
  $no = 1;
  $totalBulan = 0;
  while ($row = $result->fetch_assoc()) {
    $totalBulan += $row['total_pendapatan'];
    echo "<tr>
            <td>$no</td>
            <td>{$row['nama_produk']}</td>
            <td>{$row['nama_merk']}</td>
            <td>{$row['nama_kategori']}</td>
            <td>{$row['total_terjual']}</td>
            <td>Rp " . number_format($row['total_pendapatan'], 0, ',', '.') . "</td>
          </tr>";
    $no++;
  }
      if ($no == 1) {
        echo "<tr><td colspan='4'>Tidak ada data penjualan di bulan ini.</td></tr>";
      } else {
        echo "<tr>
                <td colspan='3'><strong>Total Pendapatan Bulan Ini</strong></td>
                <td><strong>Rp " . number_format($totalBulan, 0, ',', '.') . "</strong></td>
              </tr>";
      }
      ?>
    </table>
  </div>
</body>
</html>
