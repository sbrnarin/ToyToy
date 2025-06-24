<?php
include 'koneksi.php';
session_start();

// Query products with stock information
$query = mysqli_query($koneksi, "
    SELECT produk.*, merk.nama_merk 
    FROM produk 
    JOIN merk ON produk.id_merk = merk.id_merk 
    WHERE merk.nama_merk = 'Playdoh'
");

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Playdoh Products - ToyToyShop</title>
    <link rel="stylesheet" href="produk.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* ONLY ADDED STYLES FOR STOCK DISPLAY */
        .stock-info {
            font-size: 12px;
            margin: 5px 0;
            color: #666;
        }
        .stock-out {
            color: red;
            font-weight: bold;
        }
        .stock-low {
            color: orange;
        }
        .disabled-btn {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="login">
          <p style="color: #000080; text-align: center; margin: 0;">.</p>
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
                        <a href="wishlist.html" class="btn-view-full-wishlist">Lihat wishlist Lengkap</a>
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
                          <button class="btn-continue">Lanjutkan belanja</button>
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

        <div class="tulis">
            <h1>PLAY-DOH COLLECTION</h1>
        </div>

        <div class="video-wrapper">
            <div class="video-container">
                <video autoplay loop muted>
                    <source src="gambar/playdoh.mp4" type="video/mp4">
                </video>
            </div>
        </div>
        

        <section class="products-showcase">     
            <div class="product-list">
                <?php while ($produk = mysqli_fetch_assoc($query)): ?>
                <div class="product-card"
                    data-name="<?= htmlspecialchars($produk['nama_produk']) ?>"
                    data-price="<?= $produk['harga'] ?>"
                    data-image="gambar/<?= htmlspecialchars($produk['nama_file']) ?>"
                    data-product-id="<?= $produk['id_produk'] ?>"
                    data-stock="<?= $produk['stok'] ?>">

                    <button class="wishlist-btn" onclick="toggleWishlist(event)">
                        <i class="heart-icon" data-lucide="heart"></i>
                    </button>

                    <a href="detail_produk.php?id=<?= $produk['id_produk'] ?>" class="product-link">
                        <img src="gambar/<?= htmlspecialchars($produk['nama_file']) ?>" alt="<?= htmlspecialchars($produk['nama_produk']) ?>">
                    </a>

                    <div class="info">
                        <a href="detail_produk.php?id=<?= $produk['id_produk'] ?>" class="product-title-link">
                            <p class="product-title"><?= htmlspecialchars($produk['nama_produk']) ?></p>
                        </a>
                        <p class="product-price">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></p>
                        
                        <!-- Stock Information (ONLY ADDED ELEMENT) -->
                        <p class="stock-info <?= ($produk['stok'] <= 0) ? 'stock-out' : (($produk['stok'] < 5) ? 'stock-low' : '') ?>">
                            Stok: <?= $produk['stok'] ?>
                        </p>
                        
                        <!-- Modified Add to Cart Button -->
                        <button class="add-to-cart <?= ($produk['stok'] <= 0) ? 'disabled-btn' : '' ?>" 
                                onclick="<?= ($produk['stok'] > 0) ? 'addToCart(event)' : 'return false' ?>"
                                <?= ($produk['stok'] <= 0) ? 'disabled' : '' ?>>
                            <?= ($produk['stok'] <= 0) ? 'Stok Habis' : 'Add to cart' ?>
                        </button>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </section>

        <footer class="main-footer">
            <div class="footer-container">
                <div class="footer-column">
                    <h3 class="footer-title">TENTANG</h3>
                    <p>Toko mainan berkualitas sejak 2025. Menyediakan berbagai mainan edukatif dan koleksi eksklusif.</p>
                    <a href="#about" class="footer-link">Tentang Kami</a>
                </div>
                
                <div class="footer-column">
                    <h3 class="footer-title">LAYANAN PELANGGAN</h3>
                    <ul class="footer-links">
                        <li><a href="#help">Bantuan</a></li>
                        <li><a href="#payment">Metode Pembayaran</a></li>
                        <li><a href="#shipping">Gratis Ongkir</a></li>
                        <li><a href="#contact">Hubungi Kami</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3 class="footer-title">PEMBAYARAN</h3>
                    <div class="payment-methods">
                        <img src="gambar/gopay.png" alt="Gopay">
                        <img src="gambar/dana.png" alt="dana">
                    </div>
                    
                    <h3 class="social-title">IKUTI KAMI</h3>
                    <div class="social-media">
                        <a href="#facebook"><i class="fa fa-facebook"></i></a>
                        <a href="https://www.instagram.com/toytoy_kidsstore?igsh=MWNybDZkemhtdGx0dA=="><i class="fa fa-instagram"></i></a>
                        <a href="https://wa.me/nomor_anda"><i class="fa fa-whatsapp"></i></a>
                        <a href="https://youtube.com/@toytoyshop?si=yNXNYKnuUr-zLk1G"><i class="fa fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 ToyToyShop. All Rights Reserved.</p>
            </div>
        </footer>

        <script>
            // Initialize cart from localStorage
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
            
            // DOM Elements
            const cartCount = document.querySelector('.cart-count');
            const cartList = document.getElementById('cart-list-navbar');
            const cartTotal = document.getElementById('cart-total');
            const emptyCartMsg = document.getElementById('empty-cart');
            
            // Update cart display on page load
            document.addEventListener('DOMContentLoaded', function() {
                updateCartDisplay();
                lucide.createIcons();
                
                // Initialize wishlist buttons
                document.querySelectorAll('.wishlist-btn').forEach(btn => {
                    const productId = btn.closest('.product-card').dataset.productId;
                    if (wishlist.some(item => item.id === productId)) {
                        btn.classList.add('active');
                    }
                });
            });
            
            // MODIFIED: Add to Cart Function with stock check
            function addToCart(event) {
                event.preventDefault();
                event.stopPropagation();
                
                const productCard = event.target.closest('.product-card');
                const productId = productCard.dataset.productId;
                const productName = productCard.dataset.name;
                const productPrice = parseFloat(productCard.dataset.price);
                const productImage = productCard.dataset.image;
                const productStock = parseInt(productCard.dataset.stock);
                
                // Check if product is out of stock
                if (productStock <= 0) {
                    showNotification('Maaf, produk ini sudah habis');
                    return;
                }
                
                // Find existing item in cart
                const existingItem = cart.find(item => item.id === productId);
                
                if (existingItem) {
                    // Check if we can add more
                    if (existingItem.quantity >= productStock) {
                        showNotification('Maaf, stok tidak mencukupi');
                        return;
                    }
                    existingItem.quantity += 1;
                } else {
                    cart.push({
                        id: productId,
                        name: productName,
                        price: productPrice,
                        image: productImage,
                        quantity: 1,
                        maxStock: productStock
                    });
                }
                
                updateCart();
                showNotification(`${productName} ditambahkan ke keranjang`);
            }
            
            // Update Cart Function
            function updateCart() {
                // Save to localStorage
                localStorage.setItem('cart', JSON.stringify(cart));
                
                // Update UI
                updateCartDisplay();
            }
            
            // Update Cart Display
            function updateCartDisplay() {
                // Update cart count
                const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
                cartCount.textContent = totalItems;
                
                // Update cart popup content
                cartList.innerHTML = '';
                
                if (cart.length === 0) {
                    cartList.appendChild(emptyCartMsg);
                    emptyCartMsg.style.display = 'block';
                    cartTotal.textContent = 'Total: Rp 0';
                    return;
                }
                
                let total = 0;
                
                cart.forEach((item, index) => {
                    const li = document.createElement('li');
                    const itemTotal = item.price * item.quantity;
                    total += itemTotal;
                    
                    li.innerHTML = `
                        <img src="${item.image}" alt="${item.name}" style="width: 40px; height: auto; vertical-align: middle; margin-right: 8px;">
                        ${item.name} - Rp ${item.price.toLocaleString('id-ID')}
                        <div>
                            <button class="qty-btn minus" data-index="${index}">-</button>
                            <span>${item.quantity}</span>
                            <button class="qty-btn plus" data-index="${index}" ${item.quantity >= item.maxStock ? 'disabled' : ''}>+</button>
                        </div>
                        <button class="remove-item" data-index="${index}">&times;</button>
                    `;
                    
                    cartList.appendChild(li);
                });
                
                cartTotal.textContent = `Total: Rp ${total.toLocaleString('id-ID')}`;
                emptyCartMsg.style.display = 'none';
            }
            
            // Handle Cart Interactions
            document.addEventListener('click', function(e) {
                // Quantity decrease
                if (e.target.classList.contains('minus')) {
                    const index = e.target.dataset.index;
                    if (cart[index].quantity > 1) {
                        cart[index].quantity--;
                    } else {
                        cart.splice(index, 1);
                    }
                    updateCart();
                }
                
                // Quantity increase
                if (e.target.classList.contains('plus')) {
                    const index = e.target.dataset.index;
                    if (cart[index].quantity < cart[index].maxStock) {
                        cart[index].quantity++;
                        updateCart();
                    } else {
                        showNotification('Tidak bisa menambah, stok terbatas');
                    }
                }
                
                // Remove item
                if (e.target.classList.contains('remove-item')) {
                    const index = e.target.dataset.index;
                    const removedItem = cart.splice(index, 1)[0];
                    updateCart();
                    showNotification(`${removedItem.name} dihapus dari keranjang`);
                }
            });
            
            // NEW: Checkout Validation
            document.querySelector('.btn-view-full-cart')?.addEventListener('click', function(e) {
                // Check if cart is empty
                if (cart.length === 0) {
                    e.preventDefault();
                    showNotification('Keranjang Anda kosong');
                    return;
                }
                
                // Check for out of stock items
                const outOfStockItems = [];
                
                cart.forEach(item => {
                    const productCard = document.querySelector(`.product-card[data-product-id="${item.id}"]`);
                    if (!productCard) {
                        outOfStockItems.push(item.name);
                        return;
                    }
                    
                    const currentStock = parseInt(productCard.dataset.stock);
                    if (currentStock < item.quantity) {
                        outOfStockItems.push(item.name);
                    }
                });
                
                if (outOfStockItems.length > 0) {
                    e.preventDefault();
                    showNotification(`Produk berikut melebihi stok: ${outOfStockItems.join(', ')}`);
                }
            });
            
            // Wishlist Functionality (UNCHANGED)
            function toggleWishlist(event) {
                event.preventDefault();
                event.stopPropagation();
                
                const productCard = event.target.closest('.product-card');
                const productId = productCard.dataset.productId;
                const productName = productCard.dataset.name;
                const productPrice = parseFloat(productCard.dataset.price);
                const productImage = productCard.dataset.image;
                const wishlistBtn = event.target.closest('.wishlist-btn');
                
                // Check if already in wishlist
                const existingIndex = wishlist.findIndex(item => item.id === productId);
                
                if (existingIndex === -1) {
                    // Add to wishlist
                    wishlist.push({
                        id: productId,
                        name: productName,
                        price: productPrice,
                        image: productImage
                    });
                    wishlistBtn.classList.add('active');
                    showNotification(`${productName} ditambahkan ke wishlist`);
                } else {
                    // Remove from wishlist
                    wishlist.splice(existingIndex, 1);
                    wishlistBtn.classList.remove('active');
                    showNotification(`${productName} dihapus dari wishlist`);
                }
                
                // Save to localStorage
                localStorage.setItem('wishlist', JSON.stringify(wishlist));
            }
            
            // Search Functionality (UNCHANGED)
            function searchProducts(event) {
                event.preventDefault();
                const searchTerm = document.querySelector('.search-input').value.toLowerCase();
                const productCards = document.querySelectorAll('.product-card');
                
                productCards.forEach(card => {
                    const productName = card.dataset.name.toLowerCase();
                    if (productName.includes(searchTerm)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }
            
            // Notification Function (UNCHANGED)
            function showNotification(message) {
                alert(message);
            }
        </script>

        <script>
            lucide.createIcons();
        </script>
    </body>
</html>