<?php
include("db_config.php");

$sql = "SELECT * FROM akun";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Akun</title>
    <style>
        table {
            width: 80%;
            margin: 30px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #000;
        }
        th {
            background-color: #012B6D;
            color: white;
        }
        td {
            background-color: #F2F2F2;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Total Akun</h2>
    <table>
        <tr>
            <th>Id Akun</th>
            <th>Username</th>
            <th>Email</th>
            <th>Login sebagai</th>
            <th>Aksi</th>
        </tr>
        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id_akun']; ?></td>
            <td><?= htmlspecialchars($row['username']); ?></td>
            <td><?= htmlspecialchars($row['email']); ?></td>
            <td><?= $row['role']; ?></td>
            <td>
                <a href="ubah_role.php?id_akun=<?= $row['id_akun']; ?>">Ubah Role</a> |
                <a href="hapus_akun.php?id_akun=<?= $row['id_akun']; ?>" onclick="return confirm('Yakin?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>

<?php $conn->close(); ?>
