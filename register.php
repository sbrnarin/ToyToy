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
        
        header("Location: index.php");
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8fafc; }
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
        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

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
            <label>Nama</label>
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
        <button type="submit" class="btn btn-dark w-100">Daftar</button>
    </form>
</div>

</body>
</html>
