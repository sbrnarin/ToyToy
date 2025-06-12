<?php
include("db_config.php");

if (isset($_GET['id_akun'])) {
    $id = $_GET['id_akun'];
    $conn->query("DELETE FROM akun WHERE id_akun = $id");
    header("Location: total_akun.php");
}
?>