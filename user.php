<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include("db_config.php");

$username = $_SESSION['username'];
$message = '';

// Ambil id_akun dari username
$sql = "SELECT id_akun FROM akun WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    die("Akun tidak ditemukan.");
}

$row = $result->fetch_assoc();
$id_akun = $row['id_akun'];

// Ambil data pembeli berdasarkan id_akun
$userData = [
    'nama_pembeli' => '',
    'email' => '',
    'no_telp' => '',
    'alamat' => '',
    'profile_image' => ''
];

$sql = "SELECT nama_pembeli, email, no_telp, alamat, profile_image FROM pembeli WHERE id_akun = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_akun);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $dbData = $result->fetch_assoc();
    foreach ($dbData as $key => $value) {
        if ($value !== null) {
            $userData[$key] = $value;
        }
    }
} else {
    $message = "Data pengguna tidak ditemukan.";
}

// Update profil
if (isset($_POST['update'])) {
    $nama_pembeli = $_POST['name'];
    $email = $_POST['email'];
    $no_telp = $_POST['phone'];
    $alamat = $_POST['alamat'];

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $profile_picture = $userData['profile_image'];

    // Upload foto profil baru
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $tmp_name = $_FILES['profile_picture']['tmp_name'];
        $file_name = $_FILES['profile_picture']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png'];

        if (in_array($file_ext, $allowed_ext)) {
            $new_filename = uniqid() . '.' . $file_ext;
            $destination = $upload_dir . $new_filename;

            if (move_uploaded_file($tmp_name, $destination)) {
                if (!empty($profile_picture) && file_exists($upload_dir . $profile_picture)) {
                    unlink($upload_dir . $profile_picture);
                }
                $profile_picture = $new_filename;
            } else {
                $message = "Gagal mengunggah foto.";
            }
        } else {
            $message = "Format foto tidak valid. Hanya jpg, jpeg, dan png.";
        }
    }

    // Update password jika diisi
    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Update password di tabel akun
        $sql = "UPDATE akun SET password = ? WHERE id_akun = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $id_akun);
        $stmt->execute();
    }

    // Update data di tabel pembeli
    $sql = "UPDATE pembeli SET nama_pembeli = ?, email = ?, no_telp = ?, alamat = ?, profile_image = ? WHERE id_akun = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nama_pembeli, $email, $no_telp, $alamat, $profile_picture, $id_akun);

    if ($stmt->execute()) {
        $message = "Profil berhasil diperbarui.";
        $userData['nama_pembeli'] = $nama_pembeli;
        $userData['email'] = $email;
        $userData['no_telp'] = $no_telp;
        $userData['alamat'] = $alamat;
        $userData['profile_image'] = $profile_picture;
    } else {
        $message = "Terjadi kesalahan: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil - ToyToy</title>
    <link rel="stylesheet" href="user.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h3>Akun Saya</h3>
            <ul>
                <li><a href="user.php">Profil</a></li>
                <li><a href="pesanan.php">Pesanan Saya</a></li>
                <li><a href="#">Pusat Bantuan</a></li>
                <li><a href="logout.php">Logout</a></li>
                <li><a href="index.php">Home</a></li>
            </ul>
        </div>

        <div class="main-content">
            <h2>Profil</h2>
            <?php if (!empty($message)): ?>
                <p class="message"><?= htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <?php if (!empty($userData['profile_image'])): ?>
                <img src="uploads/<?= htmlspecialchars($userData['profile_image']); ?>" alt="Foto Profil" style="max-width:150px; margin-bottom:15px;">
            <?php else: ?>
                <p>Tidak ada foto profil</p>
            <?php endif; ?>

            <form method="POST" action="user.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" value="<?= htmlspecialchars($username); ?>" disabled class="form-control">
                </div>
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($userData['nama_pembeli']); ?>" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($userData['email']); ?>" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($userData['no_telp']); ?>" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak diubah" class="form-control">
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" name="alamat" value="<?= htmlspecialchars($userData['alamat']); ?>" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Foto Profil</label>
                    <input type="file" name="profile_picture" class="form-control">
                </div>
                <button type="submit" name="update" class="btn btn-dark">Update Profil</button>
            </form>
        </div>
    </div>
</body>
</html>
