<?php
include 'koneksi.php';

$id = $_POST['id_pesanan'];
$status = $_POST['status_pengiriman'];

$sql = "UPDATE pesanan SET status_pengiriman = ? WHERE id_pesanan = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, 'si', $status, $id);
$success = mysqli_stmt_execute($stmt);

echo $success ? 'ok' : 'gagal';
?>