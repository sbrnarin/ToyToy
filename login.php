<?php
session_start();

if (isset($_SESSION["username"])) {
    header("Location: user.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("db_config.php");

    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM pembeli WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION["username"] = $username;
            header("Location: user.php");
            exit;
        } else {
            $error_message = "Username atau password salah!";
        }
    } else {
        $error_message = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ToyToy</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <h2>Login ke Akun Anda</h2>
        
        <?php if (isset($error_message)) { ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php } ?>
        
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required placeholder="Masukkan Username">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Masukkan Password">
            </div>
            
            <button type="submit" class="btn-login">Login</button>
            
            <p>Belum punya akun? <a href="register.html">Daftar di sini</a></p>
        </form>
    </div>
</body>
</html>
