<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login_admin.php");
    exit;
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Akun Admin - Kids Toys</title>
    <style>
    body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background:rgb(255, 255, 255);
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

    /* Main content */
    .main-content {
      margin-left: 250px;
      padding: 30px 40px;
      min-height: 100vh;
      background-color: white;
      display: flex;
      justify-content: center;
      align-items: flex-start;
    }

  
        .account-card {
            max-width: 600px;
            margin: 80px auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
        }

        .account-card .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #081F5C;
            color: white;
            font-size: 40px;
            line-height: 100px;
            margin: 0 auto 20px;
            font-weight: bold;
        }

        .account-card h2 {
            margin: 0;
            color: #081F5C;
            font-size: 28px;
        }

        .account-card p {
            color: #333;
            font-size: 16px;
            margin: 8px 0 24px;
        }

        .account-card .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 10px;
            font-size: 14px;
            color: white;
            background-color: #081F5C;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }

        .account-card .btn:hover {
            background-color: #0a2a7a;
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
    <div class="account-card">
        <div class="avatar"><?= strtoupper(substr($username, 0, 1)) ?></div>
        <h2><?= htmlspecialchars($username) ?></h2>
        <p>Admin Kids Toys</p>

        <a href="ubah_password.php" class="btn">Ubah Password</a>
        <a href="logout.php" class="btn" style="background-color: #999; margin-left: 10px;">Logout</a>
    </div>

</body>
</html>
