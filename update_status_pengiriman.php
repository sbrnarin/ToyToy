<?php
include 'koneksi.php';

// Validasi awal
$id = isset($_POST['id_pesanan']) ? (int) $_POST['id_pesanan'] : 0;
$status = $_POST['status_pengiriman'] ?? '';

$allowed_status = ['Belum Diproses', 'Dikemas', 'Dikirim', 'Selesai'];

if ($id > 0 && in_array($status, $allowed_status)) {
    $sql = "UPDATE pesanan SET status_pengiriman = ? WHERE id_pesanan = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, 'si', $status, $id);
    $success = mysqli_stmt_execute($stmt);
    echo $success ? 'ok' : 'gagal';
} else {
    echo 'invalid';
}
?>
