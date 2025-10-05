document.addEventListener('DOMContentLoaded', () => {

    const API_URL = './api/';
    const productList = document.getElementById('product-list');
    const cartItemsEl = document.getElementById('cart-items');
    const cartTotalEl = document.getElementById('cart-total');
    const orderFeedEl = document.getElementById('order-feed');
    const paymentCashBtn = document.getElementById('payment-cash');
    const paymentQrBtn = document.getElementById('payment-qr');
    const qrCodeModalEl = new bootstrap.Modal(document.getElementById('qr-code-modal'));
    const qrCodeImageEl = document.getElementById('qr-code-image');
    const qrTotalEl = document.getElementById('qr-total');

    let cart = [];

    // --- API FUNCTIONS ---

    async function fetchProducts() {
        try {
            const response = await fetch(`${API_URL}get_products.php`);
            if (!response.ok) throw new Error('Network response was not ok.');
            const products = await response.json();
            renderProducts(products);
        } catch (error) {
            productList.innerHTML = `<p class="text-danger">Error loading products: ${error.message}</p>`;
        }
    }

    async function fetchOrders() {
        try {
            const response = await fetch(`${API_URL}get_orders.php`);
            if (!response.ok) throw new Error('Network response was not ok.');
            const orders = await response.json();
            renderOrders(orders);
        } catch (error) {
            console.error('Error fetching orders:', error);
        }
    }

    async function submitOrder(paymentMethod) {
        if (cart.length === 0) return;

        const orderData = {
            items: cart,
            total: calculateTotal(),
            paymentMethod: paymentMethod
        };

        try {
            const response = await fetch(`${API_URL}create_order.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderData)
            });
            const result = await response.json();

            if (result.success) {
                if (paymentMethod === 'QR Code') {
                    displayQrCode(orderData.total);
                }
                clearCart();
                fetchOrders(); // Refresh the order list immediately
            } else {
                alert(`Error creating order: ${result.error}`);
            }
        } catch (error) {
            alert(`Error submitting order: ${error.message}`);
        }
    }


    // --- RENDER FUNCTIONS ---

    function renderProducts(products) {
        productList.innerHTML = '';
        if (products.length === 0) {
            productList.innerHTML = '<p>No products found.</p>';
            return;
        }
        products.forEach(product => {
            const productEl = document.createElement('div');
            productEl.className = 'col';
            productEl.innerHTML = `
                <div class="card product-card" data-id="${product.id}" data-name="${product.name}" data-price="${product.price}">
                    <img src="${product.image_url || 'images/default.jpg'}" class="card-img-top" alt="${product.name}" onerror="this.onerror=null;this.src='images/default.jpg';">
                    <div class="card-body">
                        <h6 class="card-title">${product.name}</h6>
                        <p class="card-text">฿${parseFloat(product.price).toFixed(2)}</p>
                    </div>
                </div>
            `;
            productEl.addEventListener('click', () => addToCart(product));
            productList.appendChild(productEl);
        });
    }

    function renderCart() {
        cartItemsEl.innerHTML = '';
        if (cart.length === 0) {
            cartItemsEl.innerHTML = '<p class="text-center text-muted mt-4">ยังไม่มีสินค้าในตะกร้า</p>';
        } else {
            cart.forEach((item, index) => {
                const itemEl = document.createElement('div');
                itemEl.className = 'cart-item';
                itemEl.innerHTML = `
                    <span class="cart-item-name">${item.name}</span>
                    <input type="number" class="form-control form-control-sm cart-item-quantity" value="${item.quantity}" min="1" data-index="${index}">
                    <span class="cart-item-price">฿${(item.price * item.quantity).toFixed(2)}</span>
                    <button class="btn btn-sm cart-item-remove" data-index="${index}">&times;</button>
                `;
                cartItemsEl.appendChild(itemEl);
            });
        }
        updateTotal();
    }

    function renderOrders(orders) {
        orderFeedEl.innerHTML = '';
        if(orders.length === 0) {
            orderFeedEl.innerHTML = '<p class="text-center text-muted mt-4">กำลังรอออเดอร์...</p>';
            return;
        }
        orders.slice(0, 10).forEach(order => { // Show latest 10 orders
            const orderEl = document.createElement('div');
            orderEl.className = 'list-group-item list-group-item-action flex-column align-items-start';
            const itemsHtml = order.items.map(item => `<li>${item.quantity} x ${item.product_name}</li>`).join('');

            orderEl.innerHTML = `
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">Order #${order.id}</h6>
                    <small>${new Date(order.created_at).toLocaleTimeString()}</small>
                </div>
                <ul>${itemsHtml}</ul>
                <p class="mb-1 d-flex justify-content-between">
                    <span><strong>Total: ฿${parseFloat(order.total_price).toFixed(2)}</strong></span>
                    <span class="badge bg-secondary">${order.payment_method}</span>
                </p>
            `;
            orderFeedEl.appendChild(orderEl);
        });
    }


    // --- CART LOGIC ---

    function addToCart(product) {
        const existingItem = cart.find(item => item.id === product.id);
        if (existingItem) {
            existingItem.quantity++;
        } else {
            cart.push({ ...product, quantity: 1 });
        }
        renderCart();
    }

    function updateCartQuantity(index, quantity) {
        if (quantity > 0) {
            cart[index].quantity = quantity;
        } else {
            cart.splice(index, 1);
        }
        renderCart();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function clearCart() {
        cart = [];
        renderCart();
    }

    function calculateTotal() {
        return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    function updateTotal() {
        const total = calculateTotal();
        cartTotalEl.textContent = `฿${total.toFixed(2)}`;
        const hasItems = cart.length > 0;
        paymentCashBtn.disabled = !hasItems;
        paymentQrBtn.disabled = !hasItems;
    }


    // --- HELPERS ---

    function displayQrCode(total) {
        const amount = total.toFixed(2);
        // Using a public API for QR code generation
        // In a real application, you would use a Thai QR code generation library/service
        const qrApiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=Amount:${amount}`;
        qrCodeImageEl.src = qrApiUrl;
        qrTotalEl.textContent = `฿${amount}`;
        qrCodeModalEl.show();
    }

    // --- EVENT LISTENERS ---

    cartItemsEl.addEventListener('change', e => {
        if (e.target.classList.contains('cart-item-quantity')) {
            const index = e.target.dataset.index;
            const quantity = parseInt(e.target.value, 10);
            updateCartQuantity(index, quantity);
        }
    });

    cartItemsEl.addEventListener('click', e => {
        if (e.target.classList.contains('cart-item-remove')) {
            const index = e.target.dataset.index;
            removeFromCart(index);
        }
    });

    paymentCashBtn.addEventListener('click', () => submitOrder('Cash'));
    paymentQrBtn.addEventListener('click', () => submitOrder('QR Code'));

    // --- INITIALIZATION ---

    fetchProducts();
    fetchOrders();
    setInterval(fetchOrders, 5000); // Refresh orders every 5 seconds
});