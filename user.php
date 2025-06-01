<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include("db_config.php");

$username = $_SESSION['username'];
$message = '';

// Ambil data user dari database
$sql = "SELECT nama_pembeli, email, no_telp, alamat, profile_image FROM pembeli WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

// Jika kolom profile_image belum ada, agar tidak error, kita bisa set default:
if (!isset($userData['profile_image'])) {
    $userData['profile_image'] = '';
}

// Update profil jika form disubmit
if (isset($_POST['update'])) {
    $nama_pembeli = $_POST['name'];
    $email = $_POST['email'];
    $no_telp = $_POST['phone'];
    $alamat = $_POST['alamat'];

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $profile_picture = $userData['profile_image'];  // default photo lama

    // Handle upload foto profil baru
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_name = $_FILES['profile_picture']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png'];

        if (in_array($file_ext, $allowed_ext)) {
            $profile_picture = uniqid() . '.' . $file_ext;
            move_uploaded_file($file_tmp, $upload_dir . $profile_picture);
        } else {
            $message = "Format file foto harus jpg, jpeg, atau png.";
        }
    }

    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE pembeli SET nama_pembeli = ?, email = ?, no_telp = ?, password = ?, alamat = ?, profile_image = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $nama_pembeli, $email, $no_telp, $password_hashed, $alamat, $profile_picture, $username);
    } else {
        $sql = "UPDATE pembeli SET nama_pembeli = ?, email = ?, no_telp = ?, alamat = ?, profile_image = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $nama_pembeli, $email, $no_telp, $alamat, $profile_picture, $username);
    }

    if ($stmt->execute()) {
        $message = "Profil berhasil diperbarui.";

        // Refresh data setelah update
        $sql = "SELECT nama_pembeli, email, no_telp, alamat, profile_image FROM pembeli WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();
    } else {
        $message = "Terjadi kesalahan saat memperbarui profil.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Profil - ToyToy</title>
    <link rel="stylesheet" href="user.css" />
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
            </ul>
        </div>

        <div class="main-content">
            <h2>Profil</h2>
            <?php if (!empty($message)) : ?>
                <p class="message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <?php if (!empty($userData['profile_image'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($userData['profile_image']); ?>" alt="Foto Profil" style="max-width:150px; margin-bottom:15px;">
            <?php else: ?>
                <p>Tidak ada foto profil</p>
            <?php endif; ?>

            <form method="POST" action="user.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" disabled />
                </div>

                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($userData['nama_pembeli']); ?>" required />
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($userData['email']); ?>" required />
                </div>

                <div class="form-group">
                    <label for="phone">Nomor Telepon</label>
                    <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($userData['no_telp']); ?>" required />
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah" />
                </div>

                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <input type="text" id="alamat" name="alamat" class="form-control" value="<?php echo htmlspecialchars($userData['alamat']); ?>" required />
                </div>

                <div class="form-group">
                    <label for="profile_picture">Foto Profil (jpg, jpeg, png)</label>
                    <input type="file" id="profile_picture" name="profile_picture" class="form-control" />
                </div>

                <button type="submit" name="update">Update Profil</button>
            </form>
        </div>
    </div>
</body>
</html>
