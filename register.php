<?php
include 'koneksi.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $nama_pembeli = $_POST["name"];
    $email = $_POST["email"];
    $alamat = $_POST["alamat"];
    $no_telp = $_POST["phone"];
    $kota = $_POST["kota"];

    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO pembeli (username, password, nama_pembeli, email, alamat, no_telp, kota) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssis", $username, $password_hashed, $nama_pembeli, $email, $alamat, $no_telp, $kota);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit;
    } else {
        $error_message = "Terjadi kesalahan saat registrasi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Akun</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8fafc;
        }
        .form-container {
            max-width: 400px;
            margin: 40px auto;
            background-color: white;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.08);
        }
        .form-container h2 {
            margin-bottom: 25px;
            font-weight: bold;
        }
        .btn-dark {
            background-color: #1e293b;
            border: none;
        }
        .btn-dark:hover {
            background-color: #0f172a;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2 class="text-center">Daftar Akun</h2>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?= $error_message ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
        </div>
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" placeholder="Masukkan Nama Lengkap" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" placeholder="Masukkan Email" required>
        </div>
        <div class="mb-3">
            <label>Nomor Telepon</label>
            <input type="number" name="phone" class="form-control" placeholder="Masukkan Nomor Telepon" required>
        </div>
        <div class="mb-3">
            <label>Alamat</label>
            <input type="text" name="alamat" class="form-control" placeholder="Masukkan Alamat Lengkap" required>
        </div>
        <div class="mb-3">
            <label>Kota</label>
            <input type="text" name="kota" class="form-control" placeholder="Masukkan Kota" required>
        </div>
        <button type="submit" class="btn btn-dark w-100">Daftar</button>
    </form>
</div>

</body>
</html>
