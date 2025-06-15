<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - ToyToyShop</title>
    <link rel="stylesheet" href="produk.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <nav class="navbar">
        <div class="login">
            <a href="location.html">Location</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
            <a href="footer">Contact</a>
        </div>
        
        <header>
            <div class="header-container">
                <!-- Logo Toko -->
                <div class="logo_toko">
                    <img src="gambar/Kids Toys Logo (1).png" alt="Toy logo" class="nav_logo"/>
                </div>
                
                <!-- Search Bar -->
                <form class="search-form" onsubmit="return searchProducts(event)">
                  <div class="search">
                    <span class="search-icon material-symbols-outlined">search</span>
                    <input class="search-input" type="search" placeholder="Search" />
                  </div>
                </form>
                  
                <div class="header-icons">
                  <div class="wishlist-link" id="wishlist-toggle">
                    <span class="material-symbols-outlined">favorite</span>
                    <span class="wishlist-count">0</span>
                    
                    <!-- Dropdown Wishlist -->
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
                        <span class="cart-count">0</span> <!-- jumlah item -->
                      </a>
                      <div id="cart-popup" class="cart-popup">
                        <h4>Keranjang</h4>
                        <ul id="cart-list-navbar">
                          <li id="empty-cart" class="empty-cart">
                            <h4>Keranjang Anda Kosong</h4>
                            <button onclick="window.location.href='#'" class="btn-continue">Lanjutkan belanja</button>
                            <p><strong>Sudah punya akun ?</strong></p>
                            <p><a href="login.php">Login</a> untuk checkout lebih cepat</p>
                          </li>
                        </ul>
                        <p id="cart-total" class="cart-total">Total: Rp 0</p>
                        <a href="keranjang.html" class="btn-view-full-cart">Lihat Keranjang Lengkap</a>
                      </div>
                    </div>
                </div>
            </div>
            
            <!-- Navigation Links -->
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
                        <a href="nerf.php">Hot Wheels</a>
                        <a href="bebyalive.php">Beby Alive</a>
                    </div>
                </div>

            </nav>
        </header>

    <div class="tulis">
        <h1>ALL PRODUCT</h1>
    </div>

    <!-- Products Showcase Section -->
    <section class="products-showcase">     
      <div class="product-list">
        <?php
        include 'koneksi.php';

        $query = mysqli_query($koneksi, "
          SELECT produk.*, merk.nama_merk 
          FROM produk 
          JOIN merk ON produk.id_merk = merk.id_merk
        ");

        while ($produk = mysqli_fetch_assoc($query)) {
        ?>
          <div class="product-card"
               data-name="<?php echo $produk['nama_produk']; ?>"
               data-price="<?php echo $produk['harga']; ?>"
               data-image="gambar/<?php echo $produk['nama_file']; ?>"
               data-id="<?php echo $produk['id_produk']; ?>">

            <button class="wishlist-btn" onclick="toggleWishlist(event)">
              <i class="heart-icon" data-lucide="heart"></i>
            </button>

            <img src="gambar/<?php echo $produk['nama_file']; ?>" alt="<?php echo $produk['nama_produk']; ?>" width="100%">

            <div class="info">
              <p class="product-title"><?php echo $produk['nama_produk']; ?></p>
              <p class="product-price">Rp. <?php echo number_format($produk['harga'], 0, ',', '.'); ?></p>
              <button class="add-to-cart" onclick="addToCart(event)">Add to cart</button>
            </div>
          </div>
        <?php } ?>
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
        // Initialize cart and wishlist
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
        
        // DOM Elements
        const cartCount = document.querySelector('.cart-count');
        const wishlistCount = document.querySelector('.wishlist-count');
        const cartListNavbar = document.getElementById('cart-list-navbar');
        const cartTotal = document.getElementById('cart-total');
        const emptyCartMessage = document.getElementById('empty-cart');
        const wishlistItemsContainer = document.querySelector('.wishlist-items');
        const wishlistToggle = document.getElementById('wishlist-toggle');
        const wishlistDropdown = document.querySelector('.wishlist-dropdown');
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCartUI();
            updateWishlistUI();
            lucide.createIcons();
            
            // Highlight wishlist items
            document.querySelectorAll('.product-card').forEach(card => {
                const productId = card.dataset.id;
                if (wishlist.some(item => item.id === productId)) {
                    const btn = card.querySelector('.wishlist-btn');
                    if (btn) btn.classList.add('active');
                }
            });
        });
        
        // Cart Functions
        function updateCartUI() {
            cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);
            
            if (!cartListNavbar) return;
            
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
        
        function saveCartToLocalStorage() {
            localStorage.setItem('cart', JSON.stringify(cart));
        }
        
        // Wishlist Functions
        function updateWishlistUI() {
            wishlistCount.textContent = wishlist.length;
            
            if (!wishlistItemsContainer) return;
            
            wishlistItemsContainer.innerHTML = '';
            
            if (wishlist.length === 0) {
                wishlistItemsContainer.innerHTML = `
                    <div class="empty-message">
                        <span class="material-symbols-outlined">favorite</span>
                        <p>Wishlist Anda kosong</p>
                    </div>
                `;
                return;
            }
            
            wishlist.forEach(item => {
                const div = document.createElement('div');
                div.className = 'wishlist-item';
                div.innerHTML = `
                    <img src="${item.image}" alt="${item.name}" width="40">
                    <div class="wishlist-item-details">
                        <div class="wishlist-item-name">${item.name}</div>
                        <div class="wishlist-item-price">Rp ${item.price.toLocaleString('id-ID')}</div>
                    </div>
                    <button class="remove-wishlist" data-id="${item.id}">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                `;
                wishlistItemsContainer.appendChild(div);
            });
        }
        
        function saveWishlistToLocalStorage() {
            localStorage.setItem('wishlist', JSON.stringify(wishlist));
        }
        
        // Event Handlers
        function addToCart(event) {
            event.stopPropagation();
            const productCard = event.target.closest('.product-card');
            const id = productCard.dataset.id;
            const name = productCard.dataset.name;
            const price = parseInt(productCard.dataset.price);
            const image = productCard.dataset.image;
            
            const existingItem = cart.find(item => item.id === id);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({ id, name, price, image, quantity: 1 });
            }
            
            saveCartToLocalStorage();
            updateCartUI();
            showNotification(`${name} ditambahkan ke keranjang`);
        }
        
        function toggleWishlist(event) {
            event.stopPropagation();
            event.preventDefault();
            
            const productCard = event.target.closest('.product-card');
            const id = productCard.dataset.id;
            const name = productCard.dataset.name;
            const price = parseInt(productCard.dataset.price);
            const image = productCard.dataset.image;
            
            const existingIndex = wishlist.findIndex(item => item.id === id);
            
            if (existingIndex === -1) {
                wishlist.push({ id, name, price, image });
                event.target.closest('.wishlist-btn').classList.add('active');
                showNotification(`${name} ditambahkan ke wishlist`);
            } else {
                wishlist.splice(existingIndex, 1);
                event.target.closest('.wishlist-btn').classList.remove('active');
                showNotification(`${name} dihapus dari wishlist`);
            }
            
            saveWishlistToLocalStorage();
            updateWishlistUI();
        }
        
        // Remove item from cart
        cartListNavbar?.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-item')) {
                const index = parseInt(e.target.dataset.index, 10);
                const removedItem = cart[index];
                cart.splice(index, 1);
                saveCartToLocalStorage();
                updateCartUI();
                showNotification(`${removedItem.name} dihapus dari keranjang`);
            }
        });
        
        // Remove item from wishlist
        wishlistItemsContainer?.addEventListener('click', function(e) {
            if (e.target.closest('.remove-wishlist')) {
                const id = e.target.closest('.remove-wishlist').dataset.id;
                const removedItem = wishlist.find(item => item.id === id);
                wishlist = wishlist.filter(item => item.id !== id);
                
                // Update wishlist buttons
                document.querySelectorAll(`.product-card[data-id="${id}"] .wishlist-btn`)
                    .forEach(btn => btn.classList.remove('active'));
                
                saveWishlistToLocalStorage();
                updateWishlistUI();
                showNotification(`${removedItem.name} dihapus dari wishlist`);
            }
        });
        
        // Toggle wishlist dropdown
        wishlistToggle?.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            wishlistDropdown.classList.toggle('show');
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!wishlistToggle.contains(e.target) && !wishlistDropdown.contains(e.target)) {
                wishlistDropdown.classList.remove('show');
            }
            
            if (!cartIcon.contains(e.target) && !cartPopup.contains(e.target)) {
                cartPopup.style.display = 'none';
            }
        });
        
        // Product click handler
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (!e.target.closest('button')) {
                    const id = card.dataset.id;
                    const name = card.dataset.name;
                    const price = card.dataset.price;
                    const image = card.dataset.image;
                    
                    localStorage.setItem('selectedProduct', JSON.stringify({ id, name, price, image }));
                    window.location.href = 'product_detail.html';
                }
            });
        });
        
        function showNotification(message) {
            alert(message);
        }
    </script>
</body>
</html>