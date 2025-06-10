<?php
include("koneksi.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id_pesanan'];
    $status = $_POST['status_pengiriman'];

    $sql = "UPDATE pesanan SET status_pengiriman = ? WHERE id_pesanan = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    header("Location: data_pesanan_admin.php");
    exit();
}
?>
