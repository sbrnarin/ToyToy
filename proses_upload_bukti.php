<?php
$conn = new mysqli("localhost", "root", "", "sabrinalina");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$pesanan_id = (int)$_POST['pesanan_id'];
$metode_pembayaran = ucfirst(strtolower($_POST['metode_pembayaran'] ?? ''));

$allowed_methods = ['Gopay', 'Dana'];
if (!in_array($metode_pembayaran, $allowed_methods)) {
    die("Metode pembayaran tidak valid.");
}

if ($_FILES['bukti_pembayaran']['error'] !== UPLOAD_ERR_OK) {
    die("Error dalam upload file: " . $_FILES['bukti_pembayaran']['error']);
}

$max_size = 2 * 1024 * 1024;
$allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
if ($_FILES['bukti_pembayaran']['size'] > $max_size) {
    die("Ukuran file terlalu besar (maks. 2MB)");
}
if (!in_array($_FILES['bukti_pembayaran']['type'], $allowed_types)) {
    die("Format file tidak didukung (hanya JPG, PNG, atau PDF)");
}

$ext = pathinfo($_FILES['bukti_pembayaran']['name'], PATHINFO_EXTENSION);
$ext_file = strtolower($ext);
$ext_valid = ['jpg', 'jpeg', 'png', 'pdf'];
if (!in_array($ext_file, $ext_valid)) {
    die("Ekstensi file tidak diizinkan");
}

$filename = 'bukti_' . $pesanan_id . '_' . time() . '.' . $ext_file;
$upload_dir = 'uploads/bukti_pembayaran/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
$upload_path = $upload_dir . $filename;

if (!move_uploaded_file($_FILES['bukti_pembayaran']['tmp_name'], $upload_path)) {
    die("Gagal menyimpan file");
}

$stmt = $conn->prepare("UPDATE pesanan SET status_pembayaran = 'sudah bayar', bukti_pembayaran = ?, metode_pembayaran = ? WHERE id_pesanan = ?");
$stmt->bind_param("ssi", $filename, $metode_pembayaran, $pesanan_id);
$stmt->execute();
$stmt->close();

header("Location: sukses.php?pesanan_id=" . $pesanan_id);
exit;
?>
