<?php
session_start();

if (isset($_SESSION["username"])) {
    if ($_SESSION["role"] === "admin") {
        header("Location: dash_admin.php");
        exit;
    } elseif ($_SESSION["role"] === "user") {
        header("Location: index.php");
        exit;
    }
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("koneksi.php");

    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM akun WHERE username = ?";
    $stmt = $koneksi->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $akun = $result->fetch_assoc();

            if (password_verify($password, $akun['password'])) {
                $_SESSION["username"] = $akun['username'];
                $_SESSION["id_akun"]  = $akun['id_akun'];
                $_SESSION["role"]     = $akun['role'];

                // Jika user biasa, ambil id_pembeli
                if ($akun['role'] === 'user') {
                    $query_pembeli = $koneksi->prepare("SELECT id_pembeli FROM pembeli WHERE id_akun = ?");
                    $query_pembeli->bind_param("i", $akun['id_akun']);
                    $query_pembeli->execute();
                    $result_pembeli = $query_pembeli->get_result();

                    if ($result_pembeli->num_rows > 0) {
                        $pembeli = $result_pembeli->fetch_assoc();
                        $_SESSION["id_pembeli"] = $pembeli['id_pembeli'];
                    }

                    header("Location: index.php"); // ke halaman user
                } else {
                    header("Location: dash_admin.php"); // ke halaman admin
                }
                exit;
            } else {
                $error_message = "Username atau password salah!";
            }
        } else {
            $error_message = "Username atau password salah!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - ToyToy</title>
    <link rel="stylesheet" href="login.css" />
</head>
<body>
    <div class="login-container">
        <h2>Login ke Akun Anda</h2>

        <?php if (!empty($error_message)) { ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php } ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required placeholder="Masukkan Username" />
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Masukkan Password" />
            </div>

            <button type="submit" class="btn-login">Login</button>

            <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
        </form>
    </div>
</body>
</html>