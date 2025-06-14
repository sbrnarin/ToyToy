<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "sabrinalina";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pesanan = $_POST['id_pesanan'] ?? 0;

    // Pastikan user adalah pemilik pesanan (opsional tapi penting)
    $id_akun = $_SESSION['id_akun'] ?? 0;
    $stmt = $conn->prepare("SELECT p.id_pesanan FROM pesanan p JOIN pembeli b ON p.id_pembeli = b.id_pembeli WHERE p.id_pesanan = ? AND b.id_akun = ?");
    $stmt->bind_param("ii", $id_pesanan, $id_akun);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update status ke "Selesai"
        $update = $conn->prepare("UPDATE pesanan SET status_pengiriman = 'Selesai' WHERE id_pesanan = ?");
        $update->bind_param("i", $id_pesanan);
        $update->execute();
    }

    header("Location: pesanan.php"); // kembali ke halaman pesanan
    exit;
}
?>
