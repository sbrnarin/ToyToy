<?php
session_start();
include("db_config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM akun WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            header("Location: dash_" . $row['role'] . ".php");
            exit();
        }
    }
    echo "Login gagal";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ToyToy</title>
</head>
<style>
    body {
    background: url('gambar/slide2.jpg') no-repeat center center fixed;
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    background-size: cover;
    background-position: center;
}

.login-container {
    background-color: transparent;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 300px;
    border: 2px solid rgba(255, 255, 255, .2);
    backdrop-filter: blur(10px);
    
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #fff;
}

.form-group {
    margin-bottom: 15px;
    color: #fff;
}

label {
    font-size: 14px;
    display: block;
    margin-bottom: 5px;
}

input[type="text"], input[type="password"] {
    width: 100%;
    padding: 10px;
    font-size: 14px;
    border: 1px solid #f3efef;
    border-radius: 4px;
    box-sizing: border-box;`
}

button[type="submit"] {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}

.error {
    color: red;
    font-size: 14px;
    text-align: center;
}

p {
    text-align: center;
}

p a {
    color: #007bff;
    text-decoration: none;
}

</style>
<body>
    
    <div class="login-container">
        <h2>Login ke Admin</h2>
        
        <?php if (isset($error_message)) { ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php } ?>
        
        <form method="POST" action="login_admin.php">
            <div class="form-group">
                <label for="username">Admin</label>
                <input type="text" id="username" name="username" class="form-control" required placeholder="Masukkan Username">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Masukkan Password">
            </div>
            
            <button type="submit" class="btn-login">Login</button>
            <a href="akun_admin.php" class="btn-view-full-cart"></a>
        </form>
    </div>
</body>
</html>
