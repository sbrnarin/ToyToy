<?php
include 'koneksi.php';
$sql = "SELECT produk.*, merk.nama_merk 
        FROM produk 
        JOIN merk ON produk.id_merk = merk.id_merk 
        WHERE merk.nama_merk = 'lego'";

$result = mysqli_query($koneksi, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baby Alive Products - ToyToyShop</title>
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
            <a href="#contact">Contact</a>
        </div>
        
        <header>
            <div class="header-container">
                <div class="logo_toko">
                    <img src="c:\Users\LENOVO\Downloads\Kids_Toys_Logo-removebg-preview.png" alt="Toy logo" class="nav_logo"/>
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
                            <button onclick="window.location.href='#'" class="btn-continue">Lanjutkan belanja</button>
                            <p><strong>Sudah punya akun ?</strong></p>
                            <p><a href="login.php">Login</a> untuk checkout lebih cepat</p>
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
                        <a href="nerf.php">Hot Wheels</a>
                        <a href="bebyalive.php">Beby Alive</a>
                    </div>
                </div>

            </nav>
        </header>

        <div class="tulis">
    <h1>LEGO COLLECTION</h1>
</div>

<div class="video-wrapper">
        <div class="video-container">
            <video autoplay loop muted>
                <source src="gambar/lego.mp4" type="video/mp4">
            </video>
    </div>
</div>

        
<!-- product -->
<section class="products-showcase">     
  <div class="product-list">
    <?php
    include 'koneksi.php';

    $query = mysqli_query($koneksi, "
      SELECT produk.*, merk.nama_merk 
      FROM produk 
      JOIN merk ON produk.id_merk = merk.id_merk 
      WHERE merk.nama_merk = 'lego'
    ");

    while ($produk = mysqli_fetch_assoc($query)) {
    ?>
      <div class="product-card"
           data-name="<?php echo $produk['nama_produk']; ?>"
           data-price="<?php echo $produk['harga']; ?>"
           data-image="gambar/<?php echo $produk['nama_file']; ?>"
           data-product-id="<?php echo $produk['id_produk']; ?>">

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
    <?php
    }
    ?>
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

let cart = JSON.parse(localStorage.getItem('cart')) || [];
const cartListNavbar = document.getElementById('cart-list-navbar');
const cartCount = document.querySelector('.cart-count');
const cartIcon = document.getElementById('cart-icon');
const cartPopup = document.getElementById('cart-popup');
const cartTotal = document.getElementById('cart-total');
const emptyCartMessage = document.getElementById('empty-cart');

function updateCartUI() {
    if (!cartListNavbar || !cartTotal) return;

    cartListNavbar.innerHTML = '';
    let total = 0;

    if (cart.length === 0) {
        if (emptyCartMessage) {
            cartListNavbar.appendChild(emptyCartMessage);
            emptyCartMessage.style.display = 'block';
        }
        cartTotal.textContent = 'Total: Rp 0';
        cartCount.textContent = '0';
        return;
    }

    cart.forEach((item, index) => {
        const li = document.createElement('li');
        li.innerHTML = `
            <img src="${item.image}" alt="${item.title}" style="width: 40px; height: auto; vertical-align: middle; margin-right: 8px;">
            ${item.title} - Rp ${item.price.toLocaleString('id-ID')}
            <button class="remove-item" data-index="${index}">&times;</button>
        `;
        cartListNavbar.appendChild(li);

        total += item.price * item.quantity;
    });

    if (emptyCartMessage) emptyCartMessage.style.display = 'none';
    cartTotal.textContent = `Total: Rp ${total.toLocaleString('id-ID')}`;
    cartCount.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
}

function saveCartToLocalStorage() {
    localStorage.setItem('cart', JSON.stringify(cart));
}

function loadCartFromLocalStorage() {
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
        cart = JSON.parse(savedCart);
        updateCartUI();
    }
}

function addToCart(event) {
    event.preventDefault();
    event.stopPropagation();
    
    const productCard = event.target.closest('.product-card');
    const productId = productCard.dataset.productId;
    const title = productCard.dataset.name;
    const price = parseInt(productCard.dataset.price);
    const image = productCard.dataset.image;

    const existingItem = cart.find(item => item.id === productId);

    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({ 
            id: productId,
            title, 
            price, 
            image, 
            quantity: 1 
        });
    }

    saveCartToLocalStorage();
    updateCartUI();
    showNotification(`${title} ditambahkan ke keranjang`);
}

cartListNavbar?.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-item')) {
        const index = parseInt(e.target.dataset.index, 10);
        const removedItem = cart[index];
        cart.splice(index, 1);
        saveCartToLocalStorage();
        updateCartUI();
        showNotification(`${removedItem.title} dihapus dari keranjang`);
    }
});


cartIcon?.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    if (cartPopup) {
        cartPopup.style.display = cartPopup.style.display === 'block' ? 'none' : 'block';
    }
});

document.addEventListener('click', function(e) {
    if (!cartIcon?.contains(e.target) && !cartPopup?.contains(e.target)) {
        if (cartPopup) cartPopup.style.display = 'none';
    }
});


let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
const wishlistToggle = document.getElementById('wishlist-toggle');
const wishlistDropdown = document.querySelector('.wishlist-dropdown');
const wishlistItemsContainer = document.querySelector('.wishlist-items');

function updateWishlistDisplay() {
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

function saveWishlistToLocalStorage() {
    localStorage.setItem('wishlist', JSON.stringify(wishlist));
}

function loadWishlistFromLocalStorage() {
    const savedWishlist = localStorage.getItem('wishlist');
    if (savedWishlist) {
        wishlist = JSON.parse(savedWishlist);
        updateWishlistDisplay();
    }
}

function toggleWishlist(event) {
    event.preventDefault();
    event.stopPropagation();
    
    const productCard = event.target.closest('.product-card');
    const productId = productCard.dataset.productId;
    const productName = productCard.dataset.name;
    const productPrice = parseInt(productCard.dataset.price);
    const productImage = productCard.dataset.image;
    const wishlistBtn = event.target.closest('.wishlist-btn');

    const existingIndex = wishlist.findIndex(item => item.id === productId);

    if (existingIndex === -1) {
        wishlist.push({
            id: productId,
            name: productName,
            price: productPrice,
            image: productImage
        });
        wishlistBtn.classList.add('active');
        showNotification(`${productName} ditambahkan ke wishlist`);
    } else {
        wishlist.splice(existingIndex, 1);
        wishlistBtn.classList.remove('active');
        showNotification(`${productName} dihapus dari wishlist`);
    }

    saveWishlistToLocalStorage();
    updateWishlistDisplay();
}


wishlistItemsContainer?.addEventListener('click', function(e) {
    if (e.target.closest('.remove-wishlist')) {
        const itemElement = e.target.closest('.wishlist-item');
        const productId = itemElement.dataset.id;

        wishlist = wishlist.filter(item => item.id !== productId);
        saveWishlistToLocalStorage();
        updateWishlistDisplay();

        document.querySelectorAll(`.product-card[data-product-id="${productId}"] .wishlist-btn`)
            .forEach(btn => btn.classList.remove('active'));

        showNotification("Item dihapus dari wishlist");
    }
});


wishlistToggle?.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    wishlistDropdown.classList.toggle('show');
});


document.addEventListener('click', function(e) {
    if (!wishlistToggle.contains(e.target) && !wishlistDropdown.contains(e.target)) {
        wishlistDropdown.classList.remove('show');
    }
});


document.querySelector('.close-wishlist')?.addEventListener('click', function() {
    wishlistDropdown.classList.remove('show');
});


function searchProducts(event) {
    event.preventDefault();
    const query = document.querySelector('.search-input').value.toLowerCase();
    const products = document.querySelectorAll('.product-card');

    products.forEach(product => {
        const name = product.getAttribute('data-name').toLowerCase();

        if (query === "") {
            product.style.display = "block";
        } else {
            product.style.display = name.includes(query) ? "block" : "none";
        }
    });

    return false;
}


function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }, 10);
}

document.addEventListener('DOMContentLoaded', function() {
    loadCartFromLocalStorage();
    loadWishlistFromLocalStorage();
    
    
    document.querySelectorAll('.product-card').forEach(card => {
        const productId = card.dataset.productId;
        if (wishlist.some(item => item.id === productId)) {
            card.querySelector('.wishlist-btn').classList.add('active');
        }
    });
    
    lucide.createIcons();
});

function showNotification(message) {
    alert(message);
}
</script>

<script>
  lucide.createIcons();
</script>
</body>
</html>