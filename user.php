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

        $sql = "UPDATE akun SET password = ? WHERE id_akun = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $id_akun);
        $stmt->execute();
    }

    // Update data pembeli
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
    <title>Profil - ToyToyShop</title>
    <link rel="stylesheet" href="user.css">
    <style>
        * {
            padding: 0; margin: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
            text-decoration: none;
        }
        body {
            background-color: #f4f6fc;
            color: #333;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #0a1c4c;
            color: #fff;
            padding: 15px 30px;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            margin-left: 20px;
        }
        .navbar .logo {
            font-size: 22px;
            font-weight: bold;
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 6px;
            overflow: hidden;
        }
        .dropdown-content a {
            color: #0a1c4c;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {
            background-color: #f0f0f0;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }

        .container {
            display: flex;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            gap: 20px;
        }

        .sidebar {
            width: 260px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 25px;
        }

        .sidebar h3 {
            font-size: 20px;
            color: #081F5C;
            margin-bottom: 20px;
            border-bottom: 2px solid #081F5C;
            padding-bottom: 10px;
        }

        .sidebar ul { list-style: none; }
        .sidebar li { margin-bottom: 15px; }

        .sidebar a {
            display: block;
            padding: 10px 15px;
            background-color: #f0f3ff;
            color: #081F5C;
            border-radius: 6px;
            font-weight: 500;
        }

        .sidebar a:hover {
            background-color: #081F5C;
            color: #fff;
        }

        .main-content {
            flex: 1;
            background-color: #fff;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .main-content h2 {
            font-size: 26px;
            color: #081F5C;
            margin-bottom: 25px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        .main-content img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            display: block;
            margin-bottom: 15px;
        }

        .message {
            color: #d9534f;
            margin-bottom: 15px;
            background-color: #fff4f4;
            padding: 10px 15px;
            border-left: 4px solid #ff6b6b;
            border-radius: 4px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            background-color: #f9f9f9;
        }

        .form-control:focus {
            border-color: #081F5C;
            background-color: #fff;
        }

        button {
            padding: 12px;
            background-color: #081F5C;
            color: #fff;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-weight: 500;
        }

        button:hover {
            background-color: #0a2a7a;
        }

        @media (max-width: 900px) {
            .container {
                flex-direction: column;
                padding: 10px;
            }
            .sidebar { width: 100%; }
            .main-content { padding: 20px; }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo"><a href="index.php">ToyToyShop</a></div>
    <div class="dropdown">
        <a href="#">👤 <?= htmlspecialchars($_SESSION['username']); ?> ▾</a>
        <div class="dropdown-content">
            <a href="user.php">Profil</a>
            <a href="pesanan.php">Pesanan Saya</a>
            <a href="logout.php">Logout</a>
            <a href="index.php">Kembali ke Home</a>
        </div>
    </div>
</div>

<div class="container">

    <div class="main-content">
        <h2>Profil</h2>
        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <?php if (!empty($userData['profile_image'])): ?>
            <img src="uploads/<?= htmlspecialchars($userData['profile_image']); ?>" alt="Foto Profil">
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
            <button type="submit" name="update">Update Profil</button>
        </form>
    </div>
</div>

</body>
</html>
