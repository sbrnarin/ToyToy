<?php
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pesanan = (int)($_POST['id_pesanan'] ?? 0);
    $status_pembayaran = $_POST['status_pembayaran'] ?? '';

    if ($id_pesanan && in_array($status_pembayaran, ['belum bayar', 'sudah bayar', 'gagal'])) {
        $stmt = $koneksi->prepare("UPDATE pesanan SET status_pembayaran = ? WHERE id_pesanan = ?");
        $stmt->bind_param("si", $status_pembayaran, $id_pesanan);
        $stmt->execute();
    }
}

header("Location: data_pesanan.php");
exit;
?>