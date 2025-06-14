<?php
session_start();
include("koneksi.php");

if (!isset($_SESSION['username'])) {
    header("Location: login_admin.php");
    exit;
}

$username = $_SESSION['username'];
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    // Ambil password lama dari DB
    $stmt = $koneksi->prepare("SELECT password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($current_password, $admin['password'])) {
        if ($new_password === $confirm_password) {
            $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $koneksi->prepare("UPDATE admin SET password = ? WHERE username = ?");
            $update->bind_param("ss", $new_hashed, $username);
            if ($update->execute()) {
                $message = "Password berhasil diubah!";
            } else {
                $message = "Gagal mengubah password!";
            }
        } else {
            $message = "Password baru dan konfirmasi tidak cocok!";
        }
    } else {
        $message = "Password lama salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ubah Password</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f6fa;
            margin: 0;
            padding: 0;
        }

        .form-container {
            max-width: 500px;
            margin: 60px auto;
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
        }

        h2 {
            color: #081F5C;
            margin-bottom: 20px;
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #081F5C;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0a2a7a;
        }

        .message {
            margin-top: 20px;
            text-align: center;
            color: #d9534f;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Ubah Password Admin</h2>

        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="current_password">Password Lama</label>
            <input type="password" name="current_password" required>

            <label for="new_password">Password Baru</label>
            <input type="password" name="new_password" required>

            <label for="confirm_password">Konfirmasi Password Baru</label>
            <input type="password" name="confirm_password" required>

            <button type="submit" class="btn">Simpan Perubahan</button>
        </form>
    </div>
</body>
</html>
