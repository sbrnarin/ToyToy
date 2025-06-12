<?php
include 'koneksi.php';

// total pesanan
$total_order = $koneksi->query("SELECT COUNT(*) AS total FROM pesanan")->fetch_assoc()['total'];

// pesanan selesai
$total_akun = $koneksi->query("SELECT COUNT(*) AS total FROM pesanan WHERE status_pengiriman = 'completed'")->fetch_assoc()['total'];

// pesanan pending
$dikirim = $koneksi->query("SELECT COUNT(*) AS total FROM pesanan WHERE status_pengiriman = 'dikirim'")->fetch_assoc()['total'];

// pesanan dibatalkan
$selesai = $koneksi->query("SELECT COUNT(*) AS total FROM pesanan WHERE status_pengiriman = 'selesai'")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin</title>
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

    .dashboard {
        padding: 30px 0;
    }

    .box-container {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .box {
        background: #fff;
        padding: 20px;
        border-radius: 16px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        flex: 1;
        min-width: 200px;
        max-width: 240px;
    }

    .card-link {
      text-decoration: none;
      color: inherit;
       transition: transform 0.2s ease;
  }

    .card-link:hover {
       transform: scale(1.02);
  }

    .label {
        font-size: 15px;
        color: #555;
    }

    .count {
        font-size: 32px;
        font-weight: bold;
        color: #222;
        margin-top: 8px;
    }


    .completed { border-left: 6px solid #081F5C; }
    .canceled { border-left: 6px solid #081F5C; }
    .total { border-left: 6px solid #081F5C; }

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
        .aksi-btn {
            display: flex;
            gap: 6px;
            justify-content: center;
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
    <div class="dashboard">
      <h1>Dashboard Admin</h1>
      <a href="data_pesanan.php" class="card-link">
      <div class="box-container">
        <div class="box total">
          <div class="label">Total Pesanan</div>
          <div class="count"><?= $total_order ?>
        </div>

        </div>
        <a href="total_akun.php" class="card-link">
        <div class="box completed">
          <div class="label">Total Akun</div>
          <div class="count"><?= $total_akun ?></div>
        </div>
        </a>
        
        <a href="detail_pesanan.php" class="card-link">
        <div class="box canceled">
          <div class="label">Dikirim</div>
           <div class="count"><?= $dikirim ?></div>
        </div>
        </a>

        <a href="" class="card-link">
        <div class="box canceled">
          <div class="label">Selesai</div>
           <div class="count"><?= $selesai ?></div>
        </div>
        </a>

      </div>
    </div>
  </div>
</body>
</html>