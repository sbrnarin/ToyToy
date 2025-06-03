<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
                    <a href="#toys&games" class="dropdown-toggle">Toys & Games 
                        <span class="material-symbols-outlined">arrow_drop_down</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="puzzles.html">Building Blocks</a>
                        <a href="figuriens.html">Action Figures</a>
                        <a href="playdoh.html">Toy Clay</a>
                        <a href="dolls.html">Dolls & Accessories</a>
                    </div>
                </div>

                <div class="nav-item dropdown">
                    <a href="#videogames" class="dropdown-toggle">Video Games & Consoles
                        <span class="material-symbols-outlined">arrow_drop_down</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="#">Console Accessories</a>
                        <a href="#">Game Consoles</a>
                        <a href="video-games.html">Video Game</a>
                    </div>
                </div>

                <div class="nav-item dropdown">
                    <a href="#brands" class="dropdown-toggle">Brands
                        <span class="material-symbols-outlined">arrow_drop_down</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="playdoh.html">Play-Doh</a>
                        <a href="lego.html">Lego</a>
                        <a href="nerf.html">Nerf</a>
                        <a href="bebyalive.html">Beby Alive</a>
                    </div>
                </div>

            </nav>

        </header>

    <div class="tulis">
        <h1>ALL PRODUCT</h1>
    </div>
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
                            <img src="assets/payment/visa.png" alt="Visa">
                            <img src="assets/payment/mastercard.png" alt="Mastercard">
                            <img src="assets/payment/gopay.png" alt="Gopay">
                            <img src="assets/payment/ovo.png" alt="OVO">
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
        
        
// keranjang
const cart = [];
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
        return;
    }

    cart.forEach((item, index) => {
        const li = document.createElement('li');
        li.innerHTML = `
            <img src="${item.image}" alt="${item.title}" style="width: 40px; height: auto; vertical-align: middle; margin-right: 8px;">
            ${item.title} - ${item.price}
            <button class="remove-item" data-index="${index}">&times;</button>
        `;
        cartListNavbar.appendChild(li);

        const priceNum = parseInt(item.price.replace(/[^\d]/g, ''), 10);
        total += priceNum * item.quantity;
    });

    if (emptyCartMessage) emptyCartMessage.style.display = 'none';
    cartTotal.textContent = `Total: Rp ${total.toLocaleString('id-ID')}`;
}

function saveCartToLocalStorage() {
    localStorage.setItem('cart', JSON.stringify(cart));
}

function loadCartFromLocalStorage() {
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
        cart.push(...JSON.parse(savedCart));
        cartCount.textContent = cart.length;
        updateCartUI();
    }
}

// Add to cart 
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function() {
        const productCard = button.closest('.product-card');
        const title = productCard.querySelector('.product-title').textContent;
        const price = productCard.querySelector('.product-price').textContent;
        const image = productCard.querySelector('img').getAttribute('src');

        const existingItem = cart.find(item => item.title === title && item.price === price);

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({ title, price, image, quantity: 1 });
        }

        cartCount.textContent = cart.length;
        updateCartUI();
        saveCartToLocalStorage();

        showNotification(`${title} ditambahkan ke keranjang`);
    });
});

// Remove item cart
cartListNavbar?.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-item')) {
        const index = parseInt(e.target.dataset.index, 10);
        const removedItem = cart[index];
        cart.splice(index, 1);
        cartCount.textContent = cart.length;
        updateCartUI();
        saveCartToLocalStorage();

        showNotification(`${removedItem.title} dihapus dari keranjang`);
    }
});

//cart popup
cartIcon?.addEventListener('click', function(e) {
    e.preventDefault();
    if (cartPopup) {
        cartPopup.style.display = cartPopup.style.display === 'block' ? 'none' : 'block';
    }
});

// cart
document.addEventListener('click', function(e) {
    if (!cartIcon?.contains(e.target) && !cartPopup?.contains(e.target)) {
        if (cartPopup) cartPopup.style.display = 'none';
    }
});

// WISHLIST
let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
const wishlistToggle = document.getElementById('wishlist-toggle');
const wishlistDropdown = document.querySelector('.wishlist-dropdown');
const wishlistItemsContainer = document.querySelector('.wishlist-items');

// wishlist buttons
function initializeWishlistButtons() {
    document.querySelectorAll('.wishlist-btn').forEach(btn => {
        const productCard = btn.closest('.product-card');
        const productId = productCard.dataset.productId || generateId(productCard);

        if (wishlist.some(item => item.id === productId)) {
            btn.classList.add('active');
        }

        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleWishlistItem(productCard, btn);
        });
    });
}

function toggleWishlistItem(productCard, btn) {
    const productId = productCard.dataset.productId || generateId(productCard);
    const productName = productCard.querySelector('.product-title').textContent;
    const productPrice = productCard.querySelector('.product-price').textContent;
    const productImage = productCard.querySelector('img').src;

    const existingIndex = wishlist.findIndex(item => item.id === productId);

    if (existingIndex === -1) {
        wishlist.push({ id: productId, name: productName, price: productPrice, image: productImage });
        btn.classList.add('active');
        showNotification(`${productName} ditambahkan ke wishlist`);
    } else {
        wishlist.splice(existingIndex, 1);
        btn.classList.remove('active');
        showNotification(`${productName} dihapus dari wishlist`);
    }

    localStorage.setItem('wishlist', JSON.stringify(wishlist));
    updateWishlistDisplay();
}

function generateId(element) {
    if (!element.dataset.productId) {
        element.dataset.productId = 'prod-' + Math.random().toString(36).substr(2, 9);
    }
    return element.dataset.productId;
}

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
                    <div class="wishlist-item-price">${item.price}</div>
                </div>
                <button class="remove-wishlist">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        `;
    });

    wishlistItemsContainer.innerHTML = itemsHTML;
}

wishlistItemsContainer?.addEventListener('click', function(e) {
    if (e.target.closest('.remove-wishlist')) {
        const itemElement = e.target.closest('.wishlist-item');
        const productId = itemElement.dataset.id;

        wishlist = wishlist.filter(item => item.id !== productId);
        localStorage.setItem('wishlist', JSON.stringify(wishlist));
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

// SEARCH
const searchInput = document.getElementById('search-input');
const searchButton = document.getElementById('search-button');
const productCards = document.querySelectorAll('.product-card');

function performSearch() {
    const searchTerm = searchInput.value.toLowerCase();

    productCards.forEach(card => {
        const productName = card.querySelector('.product-title').textContent.toLowerCase();
        const productDesc = card.querySelector('.product-desc')?.textContent.toLowerCase() || '';

        if (productName.includes(searchTerm) || productDesc.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

searchButton?.addEventListener('click', performSearch);
searchInput?.addEventListener('keyup', function(e) {
    if (e.key === 'Enter') {
        performSearch();
    }
});

//  NAFIGASI DETAIL PRODUK
document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('click', function(e) {
        if (!e.target.closest('button')) {
            const name = card.getAttribute('data-name') || card.querySelector('.product-title').textContent;
            const price = card.getAttribute('data-price') || card.querySelector('.product-price').textContent;
            const image = card.getAttribute('data-image') || card.querySelector('img').src;

            const product = { name, price, image };
            localStorage.setItem('selectedProduct', JSON.stringify(product));
            window.location.href = 'product_detail.html';
        }
    });
});

// NOTIFICATION
function showNotification(message) {
    alert(message); // native browser notification
}

document.addEventListener('DOMContentLoaded', function() {
    loadCartFromLocalStorage();
    initializeWishlistButtons();
    updateWishlistDisplay();
});
</script>

</script>


<script>
  lucide.createIcons();
</script>
</script>        
</body>
</html>