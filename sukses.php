<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pesanan Berhasil - ToyToyShop</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f0f6ff;
      color: #0a1c4c;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      background: #ffffff;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      max-width: 500px;
      width: 90%;
      text-align: center;
    }

    .icon-success {
      font-size: 3.5rem;
      margin-bottom: 20px;
      color: #0a1c4c;
    }

    h1 {
      font-size: 2rem;
      margin-bottom: 15px;
    }

    p {
      font-size: 1.1rem;
      line-height: 1.5;
      margin-bottom: 30px;
    }

    .btn {
      display: inline-block;
      text-decoration: none;
      padding: 12px 24px;
      margin: 10px 5px;
      border-radius: 30px;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    .btn-primary {
      background-color: #0a1c4c;
      color: #fff;
    }

    .btn-primary:hover {
      background-color: #14378a;
      transform: scale(1.05);
    }

    .btn-outline {
      background-color: transparent;
      border: 2px solid #0a1c4c;
      color: #0a1c4c;
    }

    .btn-outline:hover {
      background-color: #0a1c4c;
      color: #fff;
      transform: scale(1.05);
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="icon-success">✅</div>
    <h1>Pesananmu Berhasil!</h1>
    <p>Terima kasih telah berbelanja di <strong>ToyToyShop</strong>.<br>Pesanan kamu sedang kami proses dan akan segera dikirim.</p>
    <a href="index.php" class="btn btn-primary">Kembali ke Halaman Utama</a>
    <a href="pesanan.php" class="btn btn-outline">Lihat Pesanan Saya</a>
  </div>
</body>
</html>
