<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Checkout Toytoy</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background: #f3f3f3;
    }
    .checkout-container {
      max-width: 800px;
      margin: auto;
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2, h3 {
      color: #333;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group label {
      display: block;
      margin-bottom: 6px;
      font-weight: bold;
    }
    .form-group input, .form-group select {
      width: 100%;
      padding: 8px 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-sizing: border-box;
    }
    .shipping-method {
      margin-top: 30px;
    }
    .shipping-method label {
      display: block;
      margin-bottom: 10px;
      cursor: pointer;
    }
    .shipping-method input[type="radio"] {
      margin-right: 10px;
    }
    .summary {
      background: #f9f9f9;
      padding: 15px;
      border-radius: 10px;
      margin-top: 30px;
    }
    .summary p {
      display: flex;
      justify-content: space-between;
      margin: 8px 0;
    }
    .summary hr {
      margin: 10px 0;
      border: 0;
      border-top: 1px solid #ddd;
    }
    .summary strong {
      font-size: 1.1em;
    }
    button {
      width: 100%;
      padding: 12px;
      background-color: #0a1c4c;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      margin-top: 20px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    button:hover {
      background-color: #14378a;
    }
    .cart-items-container {
      margin-top: 20px;
    }
    .cart-item {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      padding: 15px;
      background: #f9f9f9;
      border: 1px solid #ddd;
      border-radius: 6px;
      margin-bottom: 15px;
    }
    .cart-item img {
      width: 90px;
      height: 90px;
      object-fit: cover;
      border-radius: 5px;
    }
    .cart-details {
      display: flex;
      justify-content: space-between;
      width: 100%;
      gap: 10px;
    }
    .cart-details .left {
      flex: 1;
    }
    .cart-details .right {
      flex: 1;
      text-align: right;
    }
    .cart-details .name {
      font-weight: bold;
      font-size: 15px;
      color: #333;
      margin-bottom: 5px;
    }
    .cart-details .price {
      font-size: 14px;
      color: #555;
      margin-bottom: 5px;
    }
    .cart-details .quantity {
      font-size: 13px;
      color: #777;
    }
    .cart-details .total {
      font-weight: bold;
      font-size: 15px;
      color: #0a1c4c;
      margin-top: 5px;
    }
    @media (max-width: 600px) {
      .checkout-container {
        padding: 15px;
      }
      .cart-details {
        flex-direction: column;
      }
      .cart-details .right {
        text-align: left;
      }
    }
  </style>
</head>

<body>

<div class="checkout-container">
  <h2>Informasi Pengiriman</h2>

  <form id="checkout-form" method="POST" action="proses_cekot.php" onsubmit="return validateForm()">
    <div class="form-group">
      <label for="nama">Nama Lengkap</label>
      <input type="text" id="nama" name="nama" required />
    </div>
    <div class="form-group">
      <label for="alamat">Alamat Lengkap</label>
      <input type="text" id="alamat" name="alamat" required />
    </div>
    <div class="form-group">
      <label for="kota">Kota</label>
      <input type="text" id="kota" name="kota" required />
    </div>
    <div class="form-group">
      <label for="provinsi">Provinsi</label>
      <select id="provinsi" name="provinsi" required onchange="updateShippingOptions()">
        <option value="">Pilih Provinsi</option>
        <option value="DKI Jakarta">DKI Jakarta</option>
        <option value="Jawa Barat">Jawa Barat</option>
        <option value="Jawa Tengah">Jawa Tengah</option>
        <option value="DI Yogyakarta">DI Yogyakarta</option>
        <option value="Jawa Timur">Jawa Timur</option>
        <option value="Banten">Banten</option>
        <option value="Bali">Bali</option>
        <option value="Sumatera Utara">Sumatera Utara</option>
        <option value="Kalimantan Barat">Kalimantan Barat</option>
      </select>
    </div>
    <div class="form-group">
      <label for="nohp">Nomor Handphone</label>
      <input type="text" id="nohp" name="nohp" required />
    </div>

    <h3>Produk</h3>
    <div class="cart-items-container" id="cart-items"></div>

    <div class="shipping-method" id="shipping-method-container">
      <h3>Metode Pengiriman</h3>
    </div>

    <div class="summary">
      <h3>Ringkasan Pesanan</h3>
      <p>Subtotal: <span id="subtotal">Rp 0</span></p>
      <p>Ongkir: <span id="shipping">FREE</span></p>
      <hr />
      <p><strong>Total: <span id="total-display">Rp 0</span></strong></p>
    </div>

    <!-- Hidden inputs -->
    <input type="hidden" id="produkData" name="produk" />
    <input type="hidden" id="total" name="total" />
    <input type="hidden" id="shippingMethod" name="shippingMethod" />
    <input type="hidden" id="shippingCost" name="shippingCost" />

    <button type="submit">Lanjut ke Pembayaran</button>
  </form>
</div>

<script>
  const javaProvinces = ["DKI Jakarta", "Jawa Barat", "Jawa Tengah", "DI Yogyakarta", "Jawa Timur", "Banten"];

  function isJavaProvince(prov) {
    return javaProvinces.includes(prov);
  }

  function updateShippingOptions() {
    const prov = document.getElementById("provinsi").value;
    const container = document.getElementById("shipping-method-container");

    if (!prov) {
      container.innerHTML = '<p>Harap pilih provinsi terlebih dahulu</p>';
      updateTotal();
      return;
    }

    if (isJavaProvince(prov)) {
      container.innerHTML = `
        <h3>Metode Pengiriman</h3>
        <label><input type="radio" name="shipping" value="0" data-name="JNE Regular" checked> JNE Regular (Gratis Ongkir)</label>
        <label><input type="radio" name="shipping" value="10000" data-name="JNE YES"> JNE Express (+Rp 10.000)</label>
      `;
    } else {
      container.innerHTML = `
        <h3>Metode Pengiriman</h3>
        <label><input type="radio" name="shipping" value="15000" data-name="JNE Regular" checked> JNE Regular (Rp 15.000)</label>
        <label><input type="radio" name="shipping" value="25000" data-name="JNE YES"> JNE Express (Rp 25.000)</label>
      `;
    }

    const radios = document.querySelectorAll('input[name="shipping"]');
    radios.forEach(r => r.addEventListener('change', updateTotal));
    updateTotal();
  }

  function formatRupiah(num) {
    return "Rp " + num.toLocaleString('id-ID');
  }

  function getCheckoutItems() {
    const items = localStorage.getItem('checkoutItems');
    return items ? JSON.parse(items) : [];
  }

  function getFormattedItems() {
    const rawItems = getCheckoutItems();
    return rawItems.map(item => ({
      id_produk: item.id,
      price: item.price,
      quantity: item.quantity
    }));
  }

  function displayCheckoutItems() {
    const container = document.getElementById('cart-items');
    const items = getCheckoutItems();
    container.innerHTML = '';

    if (items.length === 0) {
      container.innerHTML = '<p>Keranjang kosong</p>';
      return;
    }

    items.forEach(item => {
      const div = document.createElement('div');
      div.classList.add('cart-item');
      div.innerHTML = `
        <img src="${item.image}" alt="${item.name}" />
        <div class="cart-details">
          <div class="left">
            <div class="name">${item.name}</div>
            <div class="price">Harga: ${formatRupiah(item.price)}</div>
            <div class="quantity">Jumlah: ${item.quantity}</div>
          </div>
          <div class="right">
            <div class="total">Total: ${formatRupiah(item.price * item.quantity)}</div>
          </div>
        </div>
      `;
      container.appendChild(div);
    });
  }

  function calculateSubtotal() {
    const items = getCheckoutItems();
    return items.reduce((sum, item) => sum + item.price * item.quantity, 0);
  }

  function updateTotal() {
    const subtotal = calculateSubtotal();
    const shippingRadio = document.querySelector('input[name="shipping"]:checked');
    const shippingCost = shippingRadio ? parseInt(shippingRadio.value) : 0;
    const shippingName = shippingRadio ? shippingRadio.getAttribute('data-name') : 'FREE';

    document.getElementById('subtotal').textContent = formatRupiah(subtotal);
    document.getElementById('shipping').textContent = shippingCost === 0 ? 'FREE' : formatRupiah(shippingCost);
    document.getElementById('total-display').textContent = formatRupiah(subtotal + shippingCost);

    document.getElementById('produkData').value = JSON.stringify(getFormattedItems());
    document.getElementById('total').value = subtotal + shippingCost;
    document.getElementById('shippingMethod').value = shippingName;
    document.getElementById('shippingCost').value = shippingCost;
  }

  function validateForm() {
    const form = document.getElementById('checkout-form');
    if (!form.checkValidity()) {
      form.reportValidity();
      return false;
    }

    const phone = form.nohp.value.trim();
    if (!/^[0-9]{8,15}$/.test(phone)) {
      alert('Nomor handphone harus berupa angka dan 8-15 digit');
      return false;
    }

    const items = getCheckoutItems();
    if (items.length === 0) {
      alert('Keranjang kosong!');
      return false;
    }

    return true;
  }

  window.onload = () => {
    displayCheckoutItems();
    updateShippingOptions();
  };
</script>

</body>
</html>
