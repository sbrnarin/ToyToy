<?php
include "koneksi.php";

$merk = mysqli_query($koneksi, "SELECT * FROM merk");
while ($m = mysqli_fetch_assoc($merk)) {
    echo "<a href='produk_merk.php?id_merk={$m['id_merk']}'>{$m['nama_merk']}</a><br>";
}
?>
