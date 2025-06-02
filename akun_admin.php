<?php
session_start();
include "koneksi.php";

// Misalnya kamu sudah punya data admin dari session atau database, contoh:
$admin = [
  'username' => 'admin123',
  'nama_lengkap' => 'Admin Kids Toys'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Akun Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5ede8;
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

    /* Main content */
    .main-content {
      margin-left: 250px;
      padding: 30px 40px;
      min-height: 100vh;
      background-color: #f5ede8;
      display: flex;
      justify-content: center;
      align-items: flex-start;
    }

    /* Profile Card */
    .profile-card {
      background-color: white;
      border-radius: 12px;
      padding: 40px;
      max-width: 450px;
      width: 100%;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      text-align: center;
    }

    .profile-card .avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      border: 3px solid #c8a98c;
      margin: 0 auto 25px auto;
      background: url('https://cdn-icons-png.flaticon.com/512/149/149071.png') center/cover no-repeat;
    }

    .profile-card .username {
      font-weight: 700;
      font-size: 22px;
      margin-bottom: 8px;
      color: #5a3d2b;
    }

    .profile-card .admin-name {
      font-size: 18px;
      margin-bottom: 25px;
      color: #7f5e4f;
    }

    .profile-card .icon-row {
      display: flex;
      justify-content: center;
      gap: 30px;
    }

    .profile-card .icon-row i {
      font-size: 26px;
      color: #a98b7e;
      cursor: pointer;
      transition: color 0.3s ease;
    }

    .profile-card .icon-row i:hover {
      color: #6f4e37;
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
    <div class="profile-card">
      <div class="avatar"></div>
      <div class="username"><?= htmlspecialchars($admin['username']) ?></div>
      <div class="admin-name"><?= htmlspecialchars($admin['nama_lengkap']) ?></div>

      <div class="icon-row">
        <i class="fas fa-th-large" title="Dashboard"></i>
        <i class="fas fa-key" title="Change Password"></i>
        <i class="fas fa-envelope" title="Messages"></i>
      </div>
    </div>
  </div>

</body>
</html>
