<?php
include 'koneksi.php';

// total pesanan
$total_order = $koneksi->query("SELECT COUNT(*) AS total FROM pesanan")->fetch_assoc()['total'];

// pesanan selesai
$completed_order = $koneksi->query("SELECT COUNT(*) AS total FROM pesanan WHERE status = 'completed'")->fetch_assoc()['total'];

// pesanan pending
$pending_order = $koneksi->query("SELECT COUNT(*) AS total FROM pesanan WHERE status = 'pending'")->fetch_assoc()['total'];

// pesanan dibatalkan
$canceled_order = $koneksi->query("SELECT COUNT(*) AS total FROM pesanan WHERE status = 'canceled'")->fetch_assoc()['total'];
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

    .completed { border-left: 6px solid #4CAF50; }
    .pending { border-left: 6px solid #FFC107; }
    .canceled { border-left: 6px solid #F44336; }
    .total { border-left: 6px solid #2196F3; }
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
      <div class="box-container">
        <div class="box total">
          <div class="label">Total Pesanan</div>
          <div class="count"><?= $total_order ?></div>
        </div>
        <div class="box completed">
          <div class="label">Selesai</div>
          <div class="count"><?= $completed_order ?></div>
        </div>
        <div class="box pending">
          <div class="label">Pending</div>
          <div class="count"><?= $pending_order ?></div>
        </div>
        <div class="box canceled">
          <div class="label">Dibatalkan</div>
          <div class="count"><?= $canceled_order ?></div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
