<?php
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pesanan = intval($_POST['id_pesanan'] ?? 0);
    $status = trim($_POST['status_pengiriman'] ?? '');

    if ($id_pesanan > 0 && $status) {
        $stmt = $koneksi->prepare("UPDATE pesanan SET status_pengiriman = ? WHERE id_pesanan = ?");
        $stmt->bind_param("si", $status, $id_pesanan);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "invalid";
    }
}
?>