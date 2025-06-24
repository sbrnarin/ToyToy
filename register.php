<?php
session_start();
include 'koneksi.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mulai transaksi
    $koneksi->begin_transaction();
    
    try {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $nama     = $_POST["nama"];
        $email    = $_POST["email"];
        $alamat   = $_POST["alamat"];
        $no_telp  = $_POST["no_telp"];
        $kota     = $_POST["kota"];
        // Validasi email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Format email tidak valid.");
        }

        // Validasi pas
        if (strlen($password) < 6) {
            throw new Exception("Password minimal harus 6 karakter.");
        }

        // cek username 
        $cek_username = $koneksi->prepare("SELECT id_akun FROM akun WHERE username = ?");
        $cek_username->bind_param("s", $username);
        $cek_username->execute();
        $cek_username->store_result();

        if ($cek_username->num_rows > 0) {
            throw new Exception("Username sudah digunakan. Silakan pilih username lain.");
        }

        // Insert ke tabel akun
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt_akun = $koneksi->prepare("INSERT INTO akun (username, password, role) VALUES (?, ?, 'user')");
        $stmt_akun->bind_param("ss", $username, $password_hashed);
        
        if (!$stmt_akun->execute()) {
            throw new Exception("Gagal membuat akun: " . $stmt_akun->error);
        }

        $id_akun = $koneksi->insert_id;

        // insert ke tabel pembeli
        $stmt_pembeli = $koneksi->prepare("INSERT INTO pembeli (id_akun, nama_pembeli, email, alamat, no_telp, kota) 
                                         VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_pembeli->bind_param("isssss", $id_akun, $nama, $email, $alamat, $no_telp, $kota);
        
        if (!$stmt_pembeli->execute()) {
            throw new Exception("Gagal menyimpan data pembeli: " . $stmt_pembeli->error);
        }

        // Commit transaksi 
        $koneksi->commit();
        
        $_SESSION['id_pembeli'] = $koneksi->insert_id;
        $_SESSION['nama_pembeli'] = $nama;
        $_SESSION['email'] = $email;
        
        header("Location: login.php");
        exit;

    } catch (Exception $e) {
        $koneksi->rollback();
        $error_message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Akun</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #f0f4f8, #dbeafe);
      font-family: 'Segoe UI', Tahoma, sans-serif;
      margin: 0;
      padding: 0;
    }

    .wrapper {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 30px;
    }

    .card-glass {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(12px);
      border-radius: 28px;
      padding: 40px;
      width: 100%;
      max-width: 480px;
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
      border: 1px solid #e0e7ff;
    }

    .form-title {
      font-size: 28px;
      color: #081F5C;
      font-weight: bold;
      margin-bottom: 20px;
      text-align: center;
    }

    label {
      font-weight: 500;
      color: #334155;
    }

    .form-control {
      border-radius: 12px;
      padding: 10px 14px;
      border: 1px solid #cbd5e1;
    }

    .form-control:focus {
      border-color: #081F5C;
      box-shadow: 0 0 0 0.15rem rgba(8, 31, 92, 0.2);
    }

    .btn-custom {
      background-color: #081F5C;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 12px;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .btn-custom:hover {
      background-color: #062147;
    }

    .icon-wrapper {
      text-align: center;
      margin-bottom: 15px;
      font-size: 50px;
      color: #081F5C;
    }
  </style>
</head>
<body>

<div class="wrapper">
  <div class="card-glass">
    <div class="icon-wrapper">
      <i class="fas fa-user-plus"></i>
    </div>
    <div class="form-title">Buat Akun Baru</div>
    <form method="POST" action="">
      <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Nama Lengkap</label>
        <input type="text" name="nama" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Nomor Telepon</label>
        <input type="text" name="no_telp" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Alamat</label>
        <input type="text" name="alamat" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Kota</label>
        <input type="text" name="kota" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-custom w-100 mt-3">Daftar Sekarang</button>
    </form>
  </div>
</div>

</body>
</html>