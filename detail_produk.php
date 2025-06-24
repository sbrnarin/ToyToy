<?php
include 'koneksi.php';

$id_produk = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_produk <= 0) {
    die("Invalid product ID");
}

$query = "SELECT produk.*, merk.nama_merk, kategori.nama_kategori 
          FROM produk 
          JOIN merk ON produk.id_merk = merk.id_merk
          JOIN kategori ON produk.id_kategori = kategori.id_kategori
          WHERE produk.id_produk = ?";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_produk);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Database query failed: " . mysqli_error($koneksi));
}

$produk = mysqli_fetch_assoc($result);

if (!$produk) {
    die("Product not found");
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?php echo isset($produk['nama_produk']) ? htmlspecialchars($produk['nama_produk']) : 'Product Detail'; ?> - ToyToyShop</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <style>
    * {
      padding: 0;
      margin: 0;
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
      outline: none; 
      border: none;
      text-decoration: none;
      text-transform: capitalize;
      transition: .2s linear;
    }

    .login {
      padding: 4px 0;
      background-color: #000080;
      text-align: right;
    }

    .login a { 
      color: #ffffff;
      padding: 0 10px;
    }

    body {
      background-color: #ffffff;
    }

    .header-container {
      display: flex;
      align-items: center;
      padding: 10px 20px;
      gap: 20px;
    }

    .logo_toko {
      flex-shrink: 0;
    }

    .nav_logo {
      width: 100px;
      height: 90px;
    }  

    .search-form {
      flex-grow: 1;
      max-width: 10000px;
    }

    .search {
      --padding: 14px;
      width: 100%;
      display: flex;
      align-items: center;
      padding: var(--padding);
      border-radius: 28px;
      background: #f6f6f6;
      transition: box-shadow 0.25s;
    }

    .search:focus-within {
      box-shadow: 0 0 2px rgba(0, 0, 0, 0.25);
    }

    .search-input {
      font-size: 16px;
      font-family: 'Lexend', sans-serif;
      color: #333333;
      margin-left: var(--padding);
      outline: none;
      border: none;
      background: transparent;
      width: 100%;
    }

    .search-input::placeholder,
    .search-icon {
      color: rgba(0, 0, 0, 0.25);
    }

    /* Header Icons */
    .header-icons {
      display: flex;
      gap: 20px;
      margin-left: auto;
    }

    .icon-link {
      color: #333;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      transition: all 0.3s ease;
    }

    .icon-link:hover {
      background-color: #f0f0f0;
      transform: translateY(-2px);
    }

    .icon-link .material-symbols-outlined {
      font-size: 24px;
    }

    .wishlist-link {
      position: relative;
      display: flex;
      align-items: center;
      cursor: pointer;
      padding: 8px;
    }

    .wishlist-link .material-symbols-outlined {
      font-size: 24px;
      color: #666;
      transition: color 0.2s;
    }

    .wishlist-link:hover .material-symbols-outlined {
      color: #e53935;
    }

    .wishlist-dropdown {
      display: none;
      position: absolute;
      top: 100%;
      right: 0;
      width: 320px;
      background: white;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      z-index: 1000;
      max-height: 400px;
      overflow-y: auto;
    }

    .wishlist-dropdown.show {
      display: block;
    }

    .wishlist-header {
      padding: 12px 16px;
      border-bottom: 1px solid #eee;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .wishlist-items {
      padding: 8px 0;
    }

    .wishlist-item {
      display: flex;
      align-items: center;
      padding: 10px 16px;
      border-bottom: 1px solid #f5f5f5;
    }

    .wishlist-item img {
      width: 50px;
      height: 50px;
      object-fit: cover;
      margin-right: 12px;
      border-radius: 4px;
    }

    .wishlist-item-details {
      flex-grow: 1;
    }

    .wishlist-item-name {
      font-weight: 500;
      margin-bottom: 4px;
    }

    .wishlist-item-price {
      color: #080808;
      font-size: 14px;
    }

    .remove-wishlist {
      background: none;
      border: none;
      color: #999;
      cursor: pointer;
    }

    .empty-message {
      text-align: center;
      padding: 30px 0;
      color: #999;
    }

    /* Cart Styles */
    .cart-icon-wrapper {
      position: relative;
    }

    .cart-count {
      position: absolute;
      top: -5px;
      right: -5px;
      background-color: red;
      color: white;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: bold;
      z-index: 10;
    }

    .cart-popup {
      display: none;
      position: absolute;
      top: 30px;
      right: 0;
      width: 260px;
      background: white;
      border: 1px solid #ccc;
      border-radius: 10px;
      box-shadow: 0 5px 10px rgba(0,0,0,0.1);
      z-index: 100;
      padding: 15px;
    }

    .cart-popup h4 {
      margin: 0 0 10px;
      font-size: 16px;
    }

    .empty-cart {
      display: flex;
      flex-direction: column;
      gap: 8px;
      text-align: center;
    }

    .empty-cart h4 {
      font-size: 14px;
      font-weight: bold;
    }

    .empty-cart .btn-continue {
      background-color: #0a1c4c;
      color: white;
      padding: 8px;
      border-radius: 6px;
      font-size: 13px;
      cursor: pointer;
    }

    .empty-cart p {
      margin: 0;
      font-size: 13px;
      color: #333;
    }

    .empty-cart a {
      color: #0a1c4c;
      font-weight: bold;
      text-decoration: underline;
      font-size: 13px;
    }

    .cart-popup ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .cart-popup li {
      font-size: 14px;
      padding: 5px 0;
      border-bottom: 1px solid #eee;
    }

    .cart-popup li {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 6px 0;
      border-bottom: 1px solid #eee;
      font-size: 14px;
    }

    .remove-item {
      background: none;
      border: none;
      color: red;
      font-weight: bold;
      cursor: pointer;
      font-size: 16px;
      padding: 2px 6px;
    }

    .cart-total {
      margin-top: 10px;
      font-weight: bold;
      text-align: right;
      font-size: 15px;
    }

    /* Navigation Dropdown */
    .nav_link {
      display: flex;
      justify-content: center;
      gap: 40px;
      padding: 15px 0;
      background: #f8f8f8;
      margin-bottom: 20px;
      position: relative;
    }

    .nav-item.dropdown {
      position: relative;
    }

    .dropdown-toggle {
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .dropdown-menu {
      display: none;
      position: absolute;
      top: 100%;
      left: 50%;
      transform: translateX(-50%);
      background: white;
      min-width: 200px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
      border-radius: 8px;
      z-index: 100;
      padding: 10px 0;
    }

    .dropdown-menu a {
      display: block;
      padding: 10px 20px;
      color: #333;
      text-decoration: none;
      transition: background 0.2s;
    }

    .dropdown-menu a:hover {
      background: #f0f0f0;
    }

    /* lihat dropdown */
    .nav-item.dropdown:hover .dropdown-menu {
      display: block;
    }

    .nav-item.dropdown:hover .dropdown-toggle span {
      transform: rotate(180deg);
    }

    /* Animasi */
    .dropdown-toggle span,
    .dropdown-menu a {
      transition: all 0.3s ease;
    }

    @media (max-width: 768px) {
      .nav_link {
        flex-direction: column;
        gap: 0;
        padding: 0;
      }
      
      .nav_link a, 
      .dropdown-toggle {
        padding: 12px 20px;
        border-bottom: 1px solid #e0e0e0;
      }
      
      .dropdown-menu {
        position: static;
        transform: none;
        box-shadow: none;
        display: none;
        background: #f0f0f0;
      }
      
      .nav-item.dropdown.mobile-open .dropdown-menu {
        display: block;
      }
      
      .nav-item.dropdown.mobile-open .dropdown-toggle span {
        transform: rotate(180deg);
      }
    }

    .detail-container {
      display: flex;
      gap: 40px;
      max-width: 1200px;
      margin: 30px auto;
      padding: 30px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .image-section {
      flex: 1;
      min-width: 300px;
    }

    .image-section img {
      width: 100%;
      max-height: 500px;
      object-fit: contain;
      border-radius: 8px;
    }

    .info-section {
      flex: 1;
      padding: 20px;
    }

    .info-section h2 {
      color: #081F5C;
      margin-bottom: 20px;
      font-size: 28px;
    }

    .info-section p {
      color: #555;
      line-height: 1.6;
      margin-bottom: 15px;
    }

    .quantity {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 25px 0;
    }

    .quantity button {
      width: 35px;
      height: 35px;
      background-color: #081F5C;
      color: white;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
    }

    .quantity input {
      width: 60px;
      height: 35px;
      text-align: center;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .pembelianan {
      margin: 25px 0;
    }

    .pembelianan p {
      font-size: 20px;
      font-weight: bold;
      color: #081F5C;
    }

    .buttons {
      display: flex;
      gap: 15px;
    }

    .cart-btn, .buy-btn {
      padding: 12px 25px;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: all 0.3s;
    }

    .cart-btn {
      background-color: #081F5C;
      color: white;
      border: 1px solid #081F5C;
    }

    .cart-btn:hover {
      background-color: #061744;
    }

    .buy-btn {
      background-color:rgb(186, 182, 180);
      color: white;
      border: 1px solidrgb(157, 154, 152);
    }

    .buy-btn:hover {
      background-color:rgb(196, 192, 190);
    }

    .btn-view-full-cart {
      display: inline-block;
      margin-top: 10px;
      padding: 8px 12px;
      background-color: #0a1c4c;
      color: white;
      text-align: center;
      text-decoration: none;
      border-radius: 4px;
      font-weight: bold;
      width: 100%;
      box-sizing: border-box;
    }

    .btn-view-full-cart:hover {
      background-color: #1f3f97;
    }

    /* Footer */
    .main-footer {
      background-color: #2c3e50;
      color: #ecf0f1;
      padding: 50px 0 0;
      font-family: 'Poppins', sans-serif;
    }

    .footer-container {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 40px;
      padding: 0 20px;
    }

    .footer-column {
      margin-bottom: 30px;
    }

    .footer-title {
      font-size: 1.3rem;
      margin-bottom: 20px;
      position: relative;
      padding-bottom: 10px;
      color: #fff;
    }

    .footer-title::after {
      content: '';
      position: absolute;
      left: 0;
      bottom: 0;
      width: 50px;
      height: 2px;
      background: #3498db;
    }

    .footer-column p {
      margin-bottom: 15px;
      line-height: 1.6;
    }

    .footer-link {
      color: #3498db;
      text-decoration: none;
      transition: color 0.3s;
    }

    .footer-link:hover {
      color: #2980b9;
      text-decoration: underline;
    }

    .footer-links {
      list-style: none;
    }

    .footer-links li {
      margin-bottom: 10px;
    }

    .footer-links a {
      color: #ecf0f1;
      text-decoration: none;
      transition: color 0.3s;
    }

    .footer-links a:hover {
      color: #3498db;
    }

    .payment-methods {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 25px;
    }

    .payment-methods img {
      width: 50px;
      height: auto;
      background: white;
      padding: 5px;
      border-radius: 4px;
    }

    .social-title {
      font-size: 1.1rem;
      margin: 25px 0 15px;
      color: #fff;
    }

    .social-media {
      display: flex;
      gap: 15px;
    }

    .social-media a {
      color: #ecf0f1;
      font-size: 1.5rem;
      transition: color 0.3s;
    }

    .social-media a:hover {
      color: #3498db;
    }

    .footer-bottom {
      text-align: center;
      padding: 20px 0;
      background-color: #1a252f;
      margin-top: 40px;
    }

    .footer-bottom p {
      margin: 0;
      font-size: 0.9rem;
    }

    @media (max-width: 768px) {
      .footer-container {
        grid-template-columns: 1fr;
        gap: 30px;
      }
      
      .footer-column {
        margin-bottom: 20px;
      }

      .detail-container {
        flex-direction: column;
        padding: 20px;
      }

      .image-section, .info-section {
        flex: none;
        width: 100%;
      }
    }

        .stock-info {
      font-size: 14px;
      margin: 10px 0;
    }
    .in-stock {
      color: green;
      font-weight: bold;
    }
    .low-stock {
      color: orange;
      font-weight: bold;
    }
    .out-of-stock {
      color: red;
      font-weight: bold;
    }
    .stock-error {
      color: red;
      font-size: 14px;
      margin-top: 5px;
      display: none;
    }
  </style>
</head>
<body>
    <nav class="navbar">
        <div class="login">
            <a href="location.html">Location</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
            <a href="#contact">Contact</a>
        </div>
        
        <header>
            <div class="header-container">
                <div class="logo_toko">
                    <img src="gambar/Kids Toys Logo (1).png" alt="Toy logo" class="nav_logo"/>
                </div>
          
                <form class="search-form" onsubmit="return searchProducts(event)">
                  <div class="search">
                    <span class="search-icon material-symbols-outlined">search</span>
                    <input class="search-input" type="search" placeholder="Search" />
                  </div>
                </form>

                <div class="header-icons">
                    <div class="wishlist-link" id="wishlist-toggle">
                        <span class="material-symbols-outlined">favorite</span>
                        
                        <div class="wishlist-dropdown">
                          <div class="wishlist-header">
                            <h3>Wishlist Saya</h3>
                            <button class="close-wishlist" aria-label="Close wishlist">
                              <span class="material-symbols-outlined">close</span>
                            </button>
                          </div>
                          <div class="wishlist-items">
                            <div class="empty-message">
                              <span class="material-symbols-outlined">favorite</span>
                              <p>Wishlist Anda kosong</p>
                            </div>
                          </div>
                          <div class="wishlist-footer">
                            <a href="wishlist.html" class="btn-view-full-wishlist" onclick="window.location.href='wishlist.html'">Lihat wishlist Lengkap</a>
                          </div>
                        </div>
                    </div>

                    <a href="user.php" class="icon-link">
                        <span class="material-symbols-outlined">person</span>
                    </a>
                    <div class="cart-icon-wrapper">
                      <a href="#" id="cart-icon" class="icon-link">
                        <span class="material-symbols-outlined">shopping_cart</span>
                        <span class="cart-count">0</span> 
                      </a>
                      <div id="cart-popup" class="cart-popup">
                        <h4>Keranjang</h4>
                        <ul id="cart-list-navbar">
                          <li id="empty-cart" class="empty-cart">
                            <h4>Keranjang Anda Kosong</h4>
                            <button onclick="window.location.href='#'" class="btn-continue">Lanjutkan belanja</button>
                          </li>
                        </ul>
                        <p id="cart-total" class="cart-total">Total: Rp 0</p>
                        <a href="keranjang.php" class="btn-view-full-cart">Lihat Keranjang Lengkap</a>
                      </div>
                    </div>
                </div>
            </div>
 
            <nav class="nav_link">
              <a href="index.php">Home</a>
                <div class="nav-item dropdown">
                    <a href="video-games.php" class="dropdown-toggle">Toys & Games 
                        <span class="material-symbols-outlined">arrow_drop_down</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="puzzles.php">Building Blocks</a>
                        <a href="figurines.php">Action Figures</a>
                        <a href="playdoh.php">Toy Clay</a>
                        <a href="dolls.php">Dolls & Accessories</a>
                    </div>
                </div>

                <div class="nav-item dropdown">
                    <a href="video-games.php" class="dropdown-toggle">Video Games & Consoles
                        <span class="material-symbols-outlined">arrow_drop_down</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="video-games.php">Console Accessories</a>
                        <a href="video-games.php">Game Consoles</a>
                        <a href="video-games.php">Video Game</a>
                    </div>
                </div>

                <div class="nav-item dropdown">
                    <a href="#brands" class="dropdown-toggle">Brands
                        <span class="material-symbols-outlined">arrow_drop_down</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="playdoh.php">Play-Doh</a>
                        <a href="lego.php">Lego</a>
                        <a href="hotwheels.php">Hot Wheels</a>
                        <a href="bebyalive.php">Beby Alive</a>
                    </div>
                </div>
            </nav>
        </header>

         <div class="detail-container">
        <div class="image-section">
            <?php if (isset($produk['nama_file'])): ?>
                <img src="gambar/<?php echo htmlspecialchars($produk['nama_file']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>">
            <?php endif; ?>
        </div>
        <div class="info-section">
            <?php if (isset($produk['nama_produk'])): ?>
                <h2><?php echo htmlspecialchars($produk['nama_produk']); ?></h2>
            <?php endif; ?>
            
            <?php if (isset($produk['nama_merk'])): ?>
                <p><strong>Brand:</strong> <?php echo htmlspecialchars($produk['nama_merk']); ?></p>
            <?php endif; ?>
            
            <?php if (isset($produk['nama_kategori'])): ?>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($produk['nama_kategori']); ?></p>
            <?php endif; ?>
            
            <!-- ADDED STOCK INFORMATION -->
            <p class="stock-info <?php 
                echo ($produk['stok'] <= 0) ? 'out-of-stock' : 
                     (($produk['stok'] < 5) ? 'low-stock' : 'in-stock'); 
            ?>">
                <?php 
                    echo ($produk['stok'] <= 0) ? 'Stok Habis' : 
                         (($produk['stok'] < 5) ? 'Stok Terbatas: ' . $produk['stok'] : 'Stok Tersedia: ' . $produk['stok']); 
                ?>
            </p>
            
            <?php if (isset($produk['deskripsi'])): ?>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($produk['deskripsi']); ?></p>
            <?php endif; ?>
            
            <div class="quantity">
                <button onclick="decreaseQuantity()">-</button>
                <input type="number" id="quantity" value="1" min="1" max="<?php echo $produk['stok']; ?>">
                <button onclick="increaseQuantity()">+</button>
            </div>
            <p id="stock-error" class="stock-error">Jumlah melebihi stok yang tersedia</p>
            
            <div class="pembelianan">
                <p>Total Price: <span id="product-price">Rp 
                    <?php if (isset($produk['harga'])): ?>
                        <?php echo number_format($produk['harga'], 0, ',', '.'); ?>
                    <?php else: ?>
                        0
                    <?php endif; ?>
                </span></p>
            </div>
            
            <div class="buttons">
                <button class="cart-btn" onclick="addToCart()" <?php echo ($produk['stok'] <= 0) ? 'disabled style="opacity:0.5; cursor:not-allowed"' : ''; ?>>
                    <?php echo ($produk['stok'] <= 0) ? 'Stok Habis' : 'Add to Cart'; ?>
                </button>
                <button class="buy-btn" onclick="buyNow()" <?php echo ($produk['stok'] <= 0) ? 'disabled style="opacity:0.5; cursor:not-allowed"' : ''; ?>>
                    <?php echo ($produk['stok'] <= 0) ? 'Stok Habis' : 'Buy Now'; ?>
                </button>
            </div>
        </div>
    </div>

    <script> 
      let cart = JSON.parse(localStorage.getItem('cart')) || [];
      let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
      const maxStock = <?php echo $produk['stok']; ?>;

      // Elemen
      const cartCount = document.querySelector('.cart-count');
      const cartListNavbar = document.getElementById('cart-list-navbar');
      const cartTotal = document.getElementById('cart-total');
      const emptyCartMessage = document.getElementById('empty-cart');
      const cartIcon = document.getElementById('cart-icon');
      const cartPopup = document.getElementById('cart-popup');
      const quantityInput = document.getElementById('quantity');
      const stockError = document.getElementById('stock-error');

      const wishlistToggle = document.getElementById('wishlist-toggle');
      const wishlistDropdown = document.querySelector('.wishlist-dropdown');
      const wishlistItemsContainer = document.querySelector('.wishlist-items');

      function updateCartUI() {
        const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
        if (cartCount) cartCount.textContent = totalItems;

        if (!cartListNavbar || !cartTotal) return;

        cartListNavbar.innerHTML = '';
        let total = 0;

        if (cart.length === 0) {
          if (emptyCartMessage) {
            cartListNavbar.appendChild(emptyCartMessage);
            emptyCartMessage.style.display = 'block';
          }
          cartTotal.textContent = 'Total: Rp 0';
          return;
        }

        cart.forEach((item, index) => {
          const li = document.createElement('li');
          li.innerHTML = `
            <img src="${item.image}" alt="${item.name}" style="width: 40px; height: auto; margin-right: 8px;">
            ${item.name} - Rp ${item.price.toLocaleString('id-ID')} (${item.quantity})
            <button class="remove-item" data-index="${index}">&times;</button>
          `;
          cartListNavbar.appendChild(li);
          total += item.price * item.quantity;
        });

        if (emptyCartMessage) emptyCartMessage.style.display = 'none';
        cartTotal.textContent = `Total: Rp ${total.toLocaleString('id-ID')}`;
      }

      function updateWishlistUI() {
        if (!wishlistItemsContainer) return;

        if (wishlist.length === 0) {
          wishlistItemsContainer.innerHTML = `
            <div class="empty-message">
              <span class="material-symbols-outlined">favorite</span>
              <p>Wishlist Anda kosong</p>
            </div>
          `;
          return;
        }

        let itemsHTML = '';
        wishlist.forEach(item => {
          itemsHTML += `
            <div class="wishlist-item" data-id="${item.id}">
              <img src="${item.image}" alt="${item.name}">
              <div class="wishlist-item-details">
                <div class="wishlist-item-name">${item.name}</div>
                <div class="wishlist-item-price">Rp ${item.price.toLocaleString('id-ID')}</div>
              </div>
              <button class="remove-wishlist">
                <span class="material-symbols-outlined">close</span>
              </button>
            </div>
          `;
        });

        wishlistItemsContainer.innerHTML = itemsHTML;
      }

      function showNotification(message) {
        alert(message);
      }

      document.addEventListener('DOMContentLoaded', () => {
        updateCartUI();
        updateWishlistUI();
        
        // Set max quantity based on stock
        quantityInput.max = maxStock;
        
        if (cartIcon && cartPopup) {
          cartIcon.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            cartPopup.style.display = cartPopup.style.display === 'block' ? 'none' : 'block';
          });
        }

        if (wishlistToggle && wishlistDropdown) {
          wishlistToggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            wishlistDropdown.classList.toggle('show');
          });
        }

        // Klik luar: tutup dropdown
        document.addEventListener('click', (e) => {
          if (cartPopup && !cartIcon.contains(e.target) && !cartPopup.contains(e.target)) {
            cartPopup.style.display = 'none';
          }
          if (wishlistDropdown && !wishlistToggle.contains(e.target) && !wishlistDropdown.contains(e.target)) {
            wishlistDropdown.classList.remove('show');
          }
        });

        // Hapus item dari cart
        cartListNavbar?.addEventListener('click', (e) => {
          if (e.target.classList.contains('remove-item')) {
            const index = parseInt(e.target.dataset.index, 10);
            const removed = cart[index];
            cart.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartUI();
            showNotification(`${removed.name} dihapus dari keranjang`);
          }
        });

        // Hapus dari wishlist
        wishlistItemsContainer?.addEventListener('click', (e) => {
          if (e.target.closest('.remove-wishlist')) {
            const itemElement = e.target.closest('.wishlist-item');
            const productId = itemElement.dataset.id;

            wishlist = wishlist.filter(item => item.id !== productId);
            localStorage.setItem('wishlist', JSON.stringify(wishlist));
            updateWishlistUI();
            showNotification('Item dihapus dari wishlist');
          }
        });

        // Tombol close wishlist
        document.querySelector('.close-wishlist')?.addEventListener('click', () => {
          wishlistDropdown.classList.remove('show');
        });
      });

      // Product Detail
      function decreaseQuantity() {
        let quantity = parseInt(quantityInput.value);
        if (quantity > 1) {
          quantity--;
          quantityInput.value = quantity;
          updateTotalPrice(quantity);
          stockError.style.display = 'none';
        }
      }

      function increaseQuantity() {
        let quantity = parseInt(quantityInput.value);
        if (quantity < maxStock) {
          quantity++;
          quantityInput.value = quantity;
          updateTotalPrice(quantity);
          stockError.style.display = 'none';
        } else {
          stockError.style.display = 'block';
        }
      }

      function updateTotalPrice(quantity) {
        const priceElement = document.getElementById('product-price');
        const basePrice = <?php echo isset($produk['harga']) ? $produk['harga'] : 0; ?>;
        const totalPrice = basePrice * quantity;
        priceElement.textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
      }

      function addToCart() {
        const quantity = parseInt(quantityInput.value);
        
        if (quantity > maxStock) {
          showNotification('Jumlah melebihi stok yang tersedia');
          return;
        }

        const product = {
          id: <?php echo $produk['id_produk']; ?>,
          name: "<?php echo addslashes($produk['nama_produk']); ?>",
          price: <?php echo $produk['harga']; ?>,
          quantity: quantity,
          image: "gambar/<?php echo $produk['nama_file']; ?>",
          maxStock: maxStock
        };

        // Check if product already in cart
        const existingIndex = cart.findIndex(item => item.id === product.id);
        if (existingIndex >= 0) {
          // Check if total quantity exceeds stock
          if (cart[existingIndex].quantity + quantity > maxStock) {
            showNotification('Total jumlah melebihi stok yang tersedia');
            return;
          }
          cart[existingIndex].quantity += quantity;
        } else {
          cart.push(product);
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartUI();
        showNotification('Produk telah ditambahkan ke keranjang');
      }

      function buyNow() {
        const quantity = parseInt(quantityInput.value);
        
        if (quantity > maxStock) {
          showNotification('Jumlah melebihi stok yang tersedia');
          return;
        }

        const productData = {
          id: "<?php echo $produk['id_produk']; ?>",
          name: "<?php echo $produk['nama_produk']; ?>",
          price: <?php echo $produk['harga']; ?>,
          quantity: quantity,
          image: "gambar/<?php echo $produk['nama_file']; ?>"
        };
        
        // Tambahkan ke keranjang
        const existingIndex = cart.findIndex(item => item.id === productData.id);
        if (existingIndex >= 0) {
          // Check stock before adding
          if (cart[existingIndex].quantity + quantity > maxStock) {
            showNotification('Total jumlah melebihi stok yang tersedia');
            return;
          }
          cart[existingIndex].quantity += productData.quantity;
        } else {
          cart.push(productData);
        }
        
        // Simpan ke localStorage
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Redirect ke halaman keranjang
        window.location.href = "keranjang.php";
      }

      // Validate quantity input
      quantityInput.addEventListener('change', function() {
        const quantity = parseInt(this.value);
        if (quantity > maxStock) {
          this.value = maxStock;
          stockError.style.display = 'block';
          updateTotalPrice(maxStock);
        } else if (quantity < 1) {
          this.value = 1;
          stockError.style.display = 'none';
          updateTotalPrice(1);
        } else {
          stockError.style.display = 'none';
          updateTotalPrice(quantity);
        }
      });
    </script>
</body>
</html>