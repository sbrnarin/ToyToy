<?php
include 'koneksi.php';

$id_pesanan = $_POST['id_pesanan'] ?? '';
$status_pembayaran = $_POST['status_pembayaran'] ?? '';

if (!empty($id_pesanan) && !empty($status_pembayaran)) {
    $stmt = $koneksi->prepare("UPDATE pesanan SET status_pembayaran = ? WHERE id_pesanan = ?");
    $stmt->bind_param("si", $status_pembayaran, $id_pesanan);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "failed";
    }
    $stmt->close();
} else {
    echo "invalid";
}
?>
