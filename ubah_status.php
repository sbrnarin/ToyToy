<?php
// update_status.php
include 'koneksi.php';

$id_pesanan = $_POST['id_pesanan'];
$status_pengiriman = $_POST['status_pengiriman'];

$stmt = $conn->prepare("UPDATE pesanan SET status_pengiriman = ? WHERE id_pesanan = ?");
$stmt->bind_param("si", $status_pengiriman, $id_pesanan);
$stmt->execute();
$stmt->close();

echo "Status pengiriman berhasil diperbarui.";
?>