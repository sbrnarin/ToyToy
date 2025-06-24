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
    'kota'=>'',
    'profile_image' => ''
];

$sql = "SELECT nama_pembeli, email, no_telp, alamat, kota, profile_image FROM pembeli WHERE id_akun = ?";
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
    $kota = $_POST['kota'];

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
    $sql = "UPDATE pembeli SET nama_pembeli = ?, email = ?, no_telp = ?, alamat = ?, kota = ?, profile_image = ? WHERE id_akun = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $nama_pembeli, $email, $no_telp, $alamat, $kota, $profile_picture, $id_akun);

    if ($stmt->execute()) {
        $userData['nama_pembeli'] = $nama_pembeli;
        $userData['email'] = $email;
        $userData['no_telp'] = $no_telp;
        $userData['alamat'] = $alamat;
        $userData['kota'] = $kota;
        $userData['profile_image'] = $profile_picture;
    } else {
        $message = "Terjadi kesalahan: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profil - Kids Toys</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background-color: #f4f6fc;
    }

    .navbar {
      background-color: #081F5C;
      height: 20px;
      width: 100%;
    }

    .header-wrapper {
      max-width: 1000px;
      margin: 15px auto 0;
      display: flex;
      align-items: center;
      gap: 20px;
      padding: 0 20px;
      justify-content: center;
    }

    .logo-area img {
      height: 40px;
    }

    .nav-menu {
      display: flex;
      gap: 30px;
      background-color: white;
      padding: 10px 30px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      align-items: center;
    }

    .nav-menu a {
      color: black;
      font-weight: 500;
      text-decoration: none;
    }

    .nav-menu a.logout {
      color: red;
    }

    .nav-menu img {
      height: 24px;
      width: 24px;
      border-radius: 50%;
      border: 1px solid #ccc;
      padding: 4px;
    }

    .profile-box {
      background-color: white;
      width: 90%;
      max-width: 900px;
      margin: 40px auto;
      border-radius: 12px;
      padding: 30px;
    }

    .profile-box h2 {
      font-size: 24px;
      margin-bottom: 20px;
      border-bottom: 1px solid #ccc;
      padding-bottom: 10px;
      color: #333;
    }

    .profile-layout {
      display: flex;
      gap: 30px;
    }

    .profile-img {
      flex: 0 0 150px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .profile-img img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 10px;
    }

    .profile-img label {
      display: block;
      margin-bottom: 8px;
      font-size: 14px;
    }

    .profile-form-wrapper {
      flex: 1;
    }

    .profile-form {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-top: 0;
    }

    .form-col {
      flex: 1 1 45%;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 6px;
      font-size: 14px;
    }

    .form-control {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      background-color: #f2f2f2;
    }

    .submit-btn {
      display: block;
      margin: 30px auto 0;
      background-color: #081F5C;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      font-weight: 500;
      cursor: pointer;
    }

    .submit-btn:hover {
      background-color: #0a2a7a;
    }
  </style>
</head>
<body>
  <div class="navbar"></div>

  <div class="header-wrapper">
    
    <div class="nav-menu">
      <a href="user.php">Profil</a>
      <a href="pesanan.php">Pesanan</a>
      <a href="index.php">Home</a>
      <a href="logout.php" class="logout">Logout</a>
      <img src="user-icon.png" alt="User Icon">
    </div>
  </div>

  <div class="profile-box">
    <h2>Profil</h2>
    <div class="profile-layout">
      <div class="profile-img">
        <img src="uploads/<?= htmlspecialchars($userData['profile_image']) ?: 'default-user.png' ?>" alt="Profile Pic">
        <label for="profile_picture">Ganti profil</label>
        <input type="file" name="profile_picture" id="profile_picture" class="form-control">
      </div>
      <div class="profile-form-wrapper">
        <form class="profile-form" method="POST" enctype="multipart/form-data">
          <div class="form-col">
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
              <label>Nomor telepon</label>
              <input type="tel" name="phone" value="<?= htmlspecialchars($userData['no_telp']); ?>" required class="form-control">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group">
              <label>Password</label>
              <input type="password" name="password" placeholder="Kosongkan jika tidak diubah" class="form-control">
            </div>
            <div class="form-group">
              <label>Alamat</label>
              <input type="text" name="alamat" value="<?= htmlspecialchars($userData['alamat']); ?>" required class="form-control">
            </div>
            <div class="form-group">
              <label>Kota</label>
              <input type="text" name="kota" value="<?= htmlspecialchars($userData['kota']); ?>" required class="form-control">
            </div>
          </div>
          <button type="submit" name="update" class="submit-btn">Update Profil</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
