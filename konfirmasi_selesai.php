<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_akun = $_SESSION['id_akun'] ?? null;
    $id_pesanan = $_POST['id_pesanan'] ?? null;

    if (!$id_akun || !$id_pesanan) {
        die("Data tidak valid.");
    }

    // Pastikan pesanan tersebut milik akun yang login
    $stmt = $koneksi->prepare("
        SELECT p.id_pesanan 
        FROM pesanan p
        JOIN pembeli b ON p.id_pembeli = b.id_pembeli
        WHERE p.id_pesanan = ? AND b.id_akun = ?
    ");
    $stmt->bind_param("ii", $id_pesanan, $id_akun);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Update status pengiriman ke 'Selesai'
        $update = $koneksi->prepare("UPDATE pesanan SET status_pengiriman = 'Selesai' WHERE id_pesanan = ?");
        $update->bind_param("i", $id_pesanan);
        $update->execute();
        $update->close();
    }

    $stmt->close();
    header("Location: pesanan.php");
    exit;
} else {
    http_response_code(405);
    echo "Metode tidak diperbolehkan.";
}
