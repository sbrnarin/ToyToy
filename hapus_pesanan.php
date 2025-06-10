<?php
include "db_config.php"; // Pastikan ini menghubungkan ke database ($conn)

if (isset($_POST['id_pesanan'])) {
    $id_pesanan = $_POST['id_pesanan'];

    // Hapus dulu dari detail_pesanan jika ada relasi
    $sql_detail = "DELETE FROM detail_pesanan WHERE id_pesanan = ?";
    $stmt_detail = mysqli_prepare($conn, $sql_detail);
    mysqli_stmt_bind_param($stmt_detail, "i", $id_pesanan);
    mysqli_stmt_execute($stmt_detail);

    // Hapus dari tabel pesanan
    $sql = "DELETE FROM pesanan WHERE id_pesanan = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_pesanan);

    if (mysqli_stmt_execute($stmt)) {
        // Redirect kembali ke halaman data pesanan
        header("Location: data_pesanan.php");
        exit();
    } else {
        echo "Gagal menghapus pesanan: " . mysqli_error($conn);
    }
} else {
    echo "ID pesanan tidak ditemukan.";
}
?>
