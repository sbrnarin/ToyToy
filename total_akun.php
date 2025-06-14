<?php
include("db_config.php");

$sql = "SELECT * FROM akun";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Akun</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f4f4;
            margin: 20px;
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
  margin: 50px auto 60px 20px; /* auto kanan, 250px kiri (230 + 20px spasi) */
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
  <!-- <div class="content-wrapper"> -->
    <h2>Total Akun</h2>
    <table>
        <tr>
            <th>Id Akun</th>
            <th>Username</th>
            <th>Login sebagai</th>
            <th>Aksi</th>
        </tr>
        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id_akun']; ?></td>
            <td><?= htmlspecialchars($row['username']); ?></td>
            <td><?= $row['role']; ?></td>
            <td>
                <a href="ubah_role.php?id_akun=<?= $row['id_akun']; ?>">Ubah Role</a> |
                <a href="hapus_akun.php?id_akun=<?= $row['id_akun']; ?>" onclick="return confirm('Yakin?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
    
</body>
</html>

<?php $conn->close(); ?>
