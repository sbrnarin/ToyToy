<?php
$conn = new mysqli("localhost", "root", "", "sabrinalina");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// Ambil data dari form
$pesanan_id = (int)$_POST['pesanan_id'];
$metode_pembayaran = $_POST['metode_pembayaran'];

// Validasi file upload
if ($_FILES['bukti_pembayaran']['error'] !== UPLOAD_ERR_OK) {
    die("Error dalam upload file: " . $_FILES['bukti_pembayaran']['error']);
}

// Batasan file
$max_size = 2 * 1024 * 1024; // 2MB
$allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];

if ($_FILES['bukti_pembayaran']['size'] > $max_size) {
    die("Ukuran file terlalu besar (maks. 2MB)");
}

if (!in_array($_FILES['bukti_pembayaran']['type'], $allowed_types)) {
    die("Format file tidak didukung (hanya JPG, PNG, atau PDF)");
}

// Generate nama file unik
$ext = pathinfo($_FILES['bukti_pembayaran']['name'], PATHINFO_EXTENSION);
$filename = 'bukti_' . $pesanan_id . '_' . time() . '.' . $ext;
$upload_path = 'uploads/bukti_pembayaran/' . $filename;

// Pindahkan file ke folder upload
if (!move_uploaded_file($_FILES['bukti_pembayaran']['tmp_name'], $upload_path)) {
    die("Gagal menyimpan file");
}

// Update database
$stmt = $conn->prepare("UPDATE pesanan SET status_pembayaran = 'menunggu verifikasi', bukti_pembayaran = ? WHERE id_pesanan = ?");
$stmt->bind_param("si", $filename, $pesanan_id);
$stmt->execute();

// Redirect ke halaman sukses
header("Location: sukses.php?pesanan_id=" . $pesanan_id);
?>