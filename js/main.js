/**
 * SmartPOS Client Application JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {

    // --- State Management ---
    let categories = [];
    let products = [];
    let cart = [];
    let selectedCategory = 'all';
    let activePaymentType = null; // 'cash' or 'qr'

    // Modals
    const cashModal = new bootstrap.Modal(document.getElementById('cash-payment-modal'));
    const qrModal = new bootstrap.Modal(document.getElementById('qr-payment-modal'));
    const receiptModal = new bootstrap.Modal(document.getElementById('receipt-modal'));
    const productFormModal = new bootstrap.Modal(document.getElementById('product-form-modal'));

    // --- Clock ---
    function updateClock() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('th-TH');
        const dateStr = now.toLocaleDateString('th-TH', { year: 'numeric', month: 'short', day: 'numeric' });
        document.getElementById('live-clock').innerText = `${dateStr} ${timeStr}`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // --- API Helpers ---
    async function apiRequest(action, method = 'GET', data = null) {
        try {
            let url = `api/index.php?action=${action}`;
            const options = {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                }
            };
            if (data && (method === 'POST' || method === 'PUT' || method === 'DELETE')) {
                options.body = JSON.stringify(data);
            }

            const res = await fetch(url, options);
            const json = await res.json();
            if (json.status !== 'success') {
                throw new Error(json.message || 'API request failed');
            }
            return json;
        } catch (err) {
            console.error('API Error:', err);
            alert(`เกิดข้อผิดพลาด: ${err.message}`);
            return null;
        }
    }

    // --- Initialize Data ---
    async function initData() {
        await fetchCategories();
        await fetchProducts();
        renderCart();
    }

    // --- Categories Operations ---
    async function fetchCategories() {
        const res = await apiRequest('get_categories');
        if (res && res.data) {
            categories = res.data;
            renderCategoryPills();
            renderCategoriesTable();
            populateProductCategoryDropdown();
        }
    }

    function renderCategoryPills() {
        const container = document.getElementById('category-pills');
        if (!container) return;

        let html = `
            <button class="btn ${selectedCategory === 'all' ? 'btn-primary' : 'btn-outline-secondary'} btn-sm rounded-pill text-nowrap px-3" data-cat-id="all">
                <i class="bi bi-grid-fill me-1"></i> ทั้งหมด
            </button>
        `;

        categories.forEach(cat => {
            const isActive = selectedCategory == cat.id;
            html += `
                <button class="btn ${isActive ? 'btn-primary' : 'btn-outline-secondary'} btn-sm rounded-pill text-nowrap px-3" data-cat-id="${cat.id}">
                    <i class="bi ${cat.icon || 'bi-tag'} me-1"></i> ${cat.name}
                </button>
            `;
        });

        container.innerHTML = html;

        container.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', () => {
                selectedCategory = btn.dataset.catId;
                renderCategoryPills();
                fetchProducts();
            });
        });
    }

    function renderCategoriesTable() {
        const tbody = document.getElementById('categories-table-body');
        if (!tbody) return;

        if (categories.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-muted">ยังไม่มีหมวดหมู่สินค้า</td></tr>`;
            return;
        }

        tbody.innerHTML = categories.map(c => `
            <tr>
                <td>${c.id}</td>
                <td><i class="bi ${c.icon || 'bi-tag'} fs-5"></i> (${c.icon})</td>
                <td class="fw-semibold">${c.name}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-warning me-1 edit-cat-btn" data-id="${c.id}" data-name="${c.name}" data-icon="${c.icon}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-cat-btn" data-id="${c.id}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        // Attach edit/delete events
        tbody.querySelectorAll('.edit-cat-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('cat-id').value = btn.dataset.id;
                document.getElementById('cat-name').value = btn.dataset.name;
                document.getElementById('cat-icon').value = btn.dataset.icon;
                document.getElementById('cat-form-title').innerText = 'แก้ไขหมวดหมู่';
                document.getElementById('btn-cancel-cat').classList.remove('d-none');
            });
        });

        tbody.querySelectorAll('.delete-cat-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (confirm('คุณต้องการลบหมวดหมู่นี้ใช่หรือไม่?')) {
                    const res = await apiRequest('delete_category', 'POST', { id: btn.dataset.id });
                    if (res) {
                        fetchCategories();
                    }
                }
            });
        });
    }

    document.getElementById('form-category')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('cat-id').value;
        const name = document.getElementById('cat-name').value.trim();
        const icon = document.getElementById('cat-icon').value.trim() || 'bi-tag';

        const action = id ? 'update_category' : 'add_category';
        const res = await apiRequest(action, 'POST', { id, name, icon });
        if (res) {
            document.getElementById('form-category').reset();
            document.getElementById('cat-id').value = '';
            document.getElementById('cat-form-title').innerText = 'เพิ่มหมวดหมู่ใหม่';
            document.getElementById('btn-cancel-cat').classList.add('d-none');
            fetchCategories();
        }
    });

    document.getElementById('btn-cancel-cat')?.addEventListener('click', () => {
        document.getElementById('form-category').reset();
        document.getElementById('cat-id').value = '';
        document.getElementById('cat-form-title').innerText = 'เพิ่มหมวดหมู่ใหม่';
        document.getElementById('btn-cancel-cat').classList.add('d-none');
    });

    function populateProductCategoryDropdown() {
        const select = document.getElementById('prod-category');
        if (!select) return;

        select.innerHTML = categories.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
    }

    // --- Products Operations ---
    async function fetchProducts() {
        const searchVal = document.getElementById('search-product')?.value.trim() || '';
        let url = `get_products&category_id=${selectedCategory}`;
        if (searchVal) {
            url += `&search=${encodeURIComponent(searchVal)}`;
        }

        const res = await apiRequest(url);
        if (res && res.data) {
            products = res.data;
            renderProductGrid();
            renderProductsTable();
        }
    }

    document.getElementById('search-product')?.addEventListener('input', () => {
        fetchProducts();
    });

    function renderProductGrid() {
        const grid = document.getElementById('product-grid');
        if (!grid) return;

        if (products.length === 0) {
            grid.innerHTML = `
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    ไม่พบรายการสินค้า
                </div>
            `;
            return;
        }

        grid.innerHTML = products.map(p => {
            const isOutOfStock = p.stock <= 0;
            const defaultImg = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&auto=format&fit=crop&q=80';
            const imgUrl = p.image_url ? p.image_url : defaultImg;

            return `
                <div class="col">
                    <div class="card product-card ${isOutOfStock ? 'opacity-50' : ''}" data-product-id="${p.id}">
                        <div class="product-img-container">
                            <img src="${imgUrl}" class="product-img" alt="${p.name}" onerror="this.src='${defaultImg}'">
                            <span class="badge ${isOutOfStock ? 'bg-danger' : (p.stock < 10 ? 'bg-warning text-dark' : 'bg-dark text-white')} stock-badge">
                                ${isOutOfStock ? 'หมด' : `คงเหลือ ${p.stock}`}
                            </span>
                        </div>
                        <div class="product-body">
                            <div class="product-title">${p.name}</div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="product-price">฿${parseFloat(p.price).toFixed(2)}</span>
                                <button class="btn btn-sm ${isOutOfStock ? 'btn-secondary' : 'btn-primary'} rounded-circle" ${isOutOfStock ? 'disabled' : ''}>
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        // Cart click events
        grid.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', () => {
                const pId = parseInt(card.dataset.productId);
                const prod = products.find(p => p.id === pId);
                if (prod && prod.stock > 0) {
                    addToCart(prod);
                }
            });
        });
    }

    function renderProductsTable() {
        const tbody = document.getElementById('products-table-body');
        if (!tbody) return;

        if (products.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-muted">ไม่มีรายการสินค้า</td></tr>`;
            return;
        }

        const defaultImg = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&auto=format&fit=crop&q=80';

        tbody.innerHTML = products.map(p => `
            <tr>
                <td>
                    <img src="${p.image_url || defaultImg}" class="rounded" style="width: 48px; height: 48px; object-fit: cover;" onerror="this.src='${defaultImg}'">
                </td>
                <td class="font-monospace text-muted">${p.barcode || '-'}</td>
                <td class="fw-semibold">${p.name}</td>
                <td><span class="badge bg-light text-dark border">${p.category_name || 'ทั่วไป'}</span></td>
                <td class="text-end fw-bold text-primary">฿${parseFloat(p.price).toFixed(2)}</td>
                <td class="text-end text-muted">฿${parseFloat(p.cost).toFixed(2)}</td>
                <td class="text-center">
                    <span class="badge ${p.stock <= 0 ? 'bg-danger' : (p.stock < 10 ? 'bg-warning text-dark' : 'bg-success')}">
                        ${p.stock}
                    </span>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-warning me-1 edit-prod-btn" data-id="${p.id}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-prod-btn" data-id="${p.id}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        // Edit and Delete product handlers
        tbody.querySelectorAll('.edit-prod-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const prod = products.find(p => p.id == btn.dataset.id);
                if (prod) {
                    document.getElementById('prod-id').value = prod.id;
                    document.getElementById('prod-barcode').value = prod.barcode || '';
                    document.getElementById('prod-name').value = prod.name;
                    document.getElementById('prod-category').value = prod.category_id || '';
                    document.getElementById('prod-price').value = prod.price;
                    document.getElementById('prod-cost').value = prod.cost;
                    document.getElementById('prod-stock').value = prod.stock;
                    document.getElementById('prod-image').value = prod.image_url || '';

                    document.getElementById('prod-modal-title').innerText = 'แก้ไขข้อมูลสินค้า';
                    productFormModal.show();
                }
            });
        });

        tbody.querySelectorAll('.delete-prod-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (confirm('คุณต้องการลบสินค้านี้ใช่หรือไม่?')) {
                    const res = await apiRequest('delete_product', 'POST', { id: btn.dataset.id });
                    if (res) {
                        fetchProducts();
                    }
                }
            });
        });
    }

    document.getElementById('btn-add-product')?.addEventListener('click', () => {
        document.getElementById('form-product').reset();
        document.getElementById('prod-id').value = '';
        document.getElementById('prod-modal-title').innerText = 'เพิ่มสินค้าใหม่';
        productFormModal.show();
    });

    document.getElementById('btn-save-product')?.addEventListener('click', async () => {
        const id = document.getElementById('prod-id').value;
        const barcode = document.getElementById('prod-barcode').value.trim();
        const name = document.getElementById('prod-name').value.trim();
        const category_id = document.getElementById('prod-category').value;
        const price = parseFloat(document.getElementById('prod-price').value);
        const cost = parseFloat(document.getElementById('prod-cost').value) || 0;
        const stock = parseInt(document.getElementById('prod-stock').value) || 0;
        const image_url = document.getElementById('prod-image').value.trim();

        if (!name || isNaN(price)) {
            alert('กรุณากรอกชื่อสินค้าและราคาขายให้ถูกต้อง');
            return;
        }

        const data = { id, barcode, name, category_id, price, cost, stock, image_url };
        const action = id ? 'update_product' : 'add_product';

        const res = await apiRequest(action, 'POST', data);
        if (res) {
            productFormModal.hide();
            fetchProducts();
        }
    });

    // --- Shopping Cart Logic ---
    function addToCart(product) {
        const existing = cart.find(item => item.id === product.id);
        if (existing) {
            if (existing.quantity + 1 > product.stock) {
                alert(`สินค้า ${product.name} มีจำนวนในสต็อกคงเหลือเพียง ${product.stock} ชิ้น`);
                return;
            }
            existing.quantity += 1;
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                price: parseFloat(product.price),
                quantity: 1,
                maxStock: product.stock
            });
        }
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cart-items-list');
        if (!container) return;

        if (cart.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-cart-x fs-1 d-block mb-2"></i>
                    ยังไม่มีสินค้าในตะกร้า
                </div>
            `;
            updateCartCalculations();
            return;
        }

        container.innerHTML = cart.map((item, idx) => `
            <div class="cart-item d-flex justify-content-between align-items-center">
                <div class="flex-grow-1 me-2">
                    <div class="fw-semibold text-dark text-truncate" style="max-width: 180px;">${item.name}</div>
                    <div class="text-muted small">฿${item.price.toFixed(2)} x ${item.quantity} = <strong class="text-primary">฿${(item.price * item.quantity).toFixed(2)}</strong></div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button class="btn btn-outline-secondary btn-sm qty-btn cart-minus" data-index="${idx}">-</button>
                    <span class="px-2 fw-bold align-middle">${item.quantity}</span>
                    <button class="btn btn-outline-secondary btn-sm qty-btn cart-plus" data-index="${idx}">+</button>
                    <button class="btn btn-link text-danger btn-sm p-0 ms-2 cart-remove" data-index="${idx}"><i class="bi bi-x-circle-fill fs-5"></i></button>
                </div>
            </div>
        `).join('');

        // Event listeners for qty controls
        container.querySelectorAll('.cart-minus').forEach(btn => {
            btn.addEventListener('click', () => {
                const idx = parseInt(btn.dataset.index);
                if (cart[idx].quantity > 1) {
                    cart[idx].quantity -= 1;
                } else {
                    cart.splice(idx, 1);
                }
                renderCart();
            });
        });

        container.querySelectorAll('.cart-plus').forEach(btn => {
            btn.addEventListener('click', () => {
                const idx = parseInt(btn.dataset.index);
                if (cart[idx].quantity + 1 > cart[idx].maxStock) {
                    alert(`สินค้ามีจำนวนในสต็อกคงเหลือเพียง ${cart[idx].maxStock} ชิ้น`);
                    return;
                }
                cart[idx].quantity += 1;
                renderCart();
            });
        });

        container.querySelectorAll('.cart-remove').forEach(btn => {
            btn.addEventListener('click', () => {
                const idx = parseInt(btn.dataset.index);
                cart.splice(idx, 1);
                renderCart();
            });
        });

        updateCartCalculations();
    }

    function updateCartCalculations() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const discountInput = document.getElementById('cart-discount');
        const discount = parseFloat(discountInput?.value) || 0;
        const taxToggle = document.getElementById('cart-tax-toggle');
        const isTax = taxToggle ? taxToggle.checked : false;

        const taxableAmount = Math.max(0, subtotal - discount);
        const tax = isTax ? (taxableAmount * 0.07) : 0;
        const finalTotal = taxableAmount + tax;

        document.getElementById('cart-subtotal').innerText = `฿${subtotal.toFixed(2)}`;
        document.getElementById('cart-tax-amount').innerText = `฿${tax.toFixed(2)}`;
        document.getElementById('cart-final-total').innerText = `฿${finalTotal.toFixed(2)}`;

        const payCashBtn = document.getElementById('btn-pay-cash');
        const payQrBtn = document.getElementById('btn-pay-qr');

        const isDisabled = cart.length === 0 || finalTotal < 0;
        if (payCashBtn) payCashBtn.disabled = isDisabled;
        if (payQrBtn) payQrBtn.disabled = isDisabled;
    }

    document.getElementById('cart-discount')?.addEventListener('input', updateCartCalculations);
    document.getElementById('cart-tax-toggle')?.addEventListener('change', updateCartCalculations);

    document.getElementById('btn-clear-cart')?.addEventListener('click', () => {
        if (cart.length > 0 && confirm('ล้างรายการสินค้าทั้งหมดในตะกร้า?')) {
            cart = [];
            renderCart();
        }
    });

    // --- Checkout & Payment Processing ---
    function getCartTotals() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const discount = parseFloat(document.getElementById('cart-discount')?.value) || 0;
        const isTax = document.getElementById('cart-tax-toggle')?.checked || false;
        const taxableAmount = Math.max(0, subtotal - discount);
        const tax = isTax ? (taxableAmount * 0.07) : 0;
        const finalTotal = taxableAmount + tax;

        return { subtotal, discount, tax, finalTotal };
    }

    // Cash Payment Trigger
    document.getElementById('btn-pay-cash')?.addEventListener('click', () => {
        const { finalTotal } = getCartTotals();
        document.getElementById('modal-cash-total').innerText = `฿${finalTotal.toFixed(2)}`;
        const receivedInput = document.getElementById('cash-received-input');
        receivedInput.value = '';
        document.getElementById('cash-change-display').innerText = '฿0.00';
        document.getElementById('btn-confirm-cash-pay').disabled = true;
        activePaymentType = 'cash';
        cashModal.show();
    });

    // QR Payment Trigger
    document.getElementById('btn-pay-qr')?.addEventListener('click', () => {
        const { finalTotal } = getCartTotals();
        document.getElementById('modal-qr-total').innerText = `฿${finalTotal.toFixed(2)}`;
        activePaymentType = 'qr';
        qrModal.show();
    });

    // Quick cash buttons
    document.querySelectorAll('.quick-cash-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const { finalTotal } = getCartTotals();
            const input = document.getElementById('cash-received-input');
            const type = btn.dataset.amount;
            if (type === 'exact') {
                input.value = finalTotal.toFixed(2);
            } else {
                input.value = parseFloat(type).toFixed(2);
            }
            triggerCashInputUpdate();
        });
    });

    document.getElementById('cash-received-input')?.addEventListener('input', triggerCashInputUpdate);

    function triggerCashInputUpdate() {
        const { finalTotal } = getCartTotals();
        const inputVal = parseFloat(document.getElementById('cash-received-input').value) || 0;
        const change = inputVal - finalTotal;

        const changeDisplay = document.getElementById('cash-change-display');
        const confirmBtn = document.getElementById('btn-confirm-cash-pay');

        if (change >= 0) {
            changeDisplay.innerText = `฿${change.toFixed(2)}`;
            changeDisplay.classList.remove('text-danger');
            changeDisplay.classList.add('text-primary');
            confirmBtn.disabled = false;
        } else {
            changeDisplay.innerText = `เงินไม่พอ (ขาด ฿${Math.abs(change).toFixed(2)})`;
            changeDisplay.classList.remove('text-primary');
            changeDisplay.classList.add('text-danger');
            confirmBtn.disabled = true;
        }
    }

    // Confirm Cash Payment
    document.getElementById('btn-confirm-cash-pay')?.addEventListener('click', async () => {
        const { subtotal, discount, tax, finalTotal } = getCartTotals();
        const paidAmount = parseFloat(document.getElementById('cash-received-input').value) || 0;

        await processCheckout({
            items: cart,
            payment_method: 'cash',
            paid_amount: paidAmount,
            discount: discount,
            tax: tax
        });
        cashModal.hide();
    });

    // Confirm QR Payment
    document.getElementById('btn-confirm-qr-pay')?.addEventListener('click', async () => {
        const { subtotal, discount, tax, finalTotal } = getCartTotals();

        await processCheckout({
            items: cart,
            payment_method: 'qr',
            paid_amount: finalTotal,
            discount: discount,
            tax: tax
        });
        qrModal.hide();
    });

    async function processCheckout(payload) {
        const res = await apiRequest('checkout', 'POST', payload);
        if (res && res.status === 'success') {
            // Show Receipt
            showReceiptModal(res.data);

            // Reset cart & refresh stock
            cart = [];
            document.getElementById('cart-discount').value = 0;
            document.getElementById('cart-tax-toggle').checked = false;
            renderCart();
            fetchProducts();
        }
    }

    function showReceiptModal(order) {
        const container = document.getElementById('receipt-content');
        if (!container) return;

        const isCash = order.payment_method === 'cash';

        container.innerHTML = `
            <div class="receipt-box text-center">
                <h4 class="fw-bold mb-1">SmartPOS Cafe & Bistro</h4>
                <div class="text-muted mb-2">123 ถนนสุขุมวิท กรุงเทพมหานคร</div>
                <div class="border-top border-bottom py-1 mb-2 text-start small">
                    <div><strong>เลขที่:</strong> ${order.order_number}</div>
                    <div><strong>วันที่:</strong> ${order.created_at}</div>
                    <div><strong>ชำระโดย:</strong> ${isCash ? 'เงินสด' : 'สแกน QR PromptPay'}</div>
                </div>

                <table class="w-100 text-start small mb-2">
                    <thead>
                        <tr class="border-bottom">
                            <th>รายการ</th>
                            <th class="text-center">จำนวน</th>
                            <th class="text-end">รวม</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${order.items.map(item => `
                            <tr>
                                <td>${item.product_name}</td>
                                <td class="text-center">${item.quantity}</td>
                                <td class="text-end">${parseFloat(item.subtotal).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>

                <div class="border-top pt-2 text-end small">
                    <div>รวมเงิน: ฿${parseFloat(order.subtotal).toFixed(2)}</div>
                    ${order.discount > 0 ? `<div class="text-danger">ส่วนลด: -฿${parseFloat(order.discount).toFixed(2)}</div>` : ''}
                    ${order.tax > 0 ? `<div>VAT (7%): ฿${parseFloat(order.tax).toFixed(2)}</div>` : ''}
                    <div class="fw-bold fs-6 mt-1">ยอดสุทธิ: ฿${parseFloat(order.final_amount).toFixed(2)}</div>
                    ${isCash ? `
                        <div>รับเงิน: ฿${parseFloat(order.paid_amount).toFixed(2)}</div>
                        <div>เงินทอน: ฿${parseFloat(order.change_amount).toFixed(2)}</div>
                    ` : ''}
                </div>

                <div class="mt-4 pt-2 border-top text-center text-muted small">
                    *** ขอบคุณที่อุดหนุน ***
                </div>
            </div>
        `;

        receiptModal.show();
    }

    // --- Orders History Tab ---
    async function fetchOrdersHistory() {
        const res = await apiRequest('get_orders');
        const tbody = document.getElementById('orders-table-body');
        if (!tbody) return;

        if (!res || !res.data || res.data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">ยังไม่มีประวัติการขาย</td></tr>`;
            return;
        }

        tbody.innerHTML = res.data.map(o => `
            <tr>
                <td class="font-monospace fw-bold">${o.order_number}</td>
                <td>${o.created_at}</td>
                <td>
                    <span class="badge ${o.payment_method === 'cash' ? 'bg-success' : 'bg-primary'}">
                        ${o.payment_method === 'cash' ? 'เงินสด' : 'สแกน QR'}
                    </span>
                </td>
                <td class="text-end">฿${parseFloat(o.subtotal).toFixed(2)}</td>
                <td class="text-end text-danger">${o.discount > 0 ? `-฿${parseFloat(o.discount).toFixed(2)}` : '-'}</td>
                <td class="text-end fw-bold text-primary">฿${parseFloat(o.final_amount).toFixed(2)}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-secondary view-order-btn" data-id="${o.id}">
                        <i class="bi bi-printer me-1"></i> พิมพ์
                    </button>
                </td>
            </tr>
        `).join('');

        tbody.querySelectorAll('.view-order-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const orderRes = await apiRequest(`get_order_details&id=${btn.dataset.id}`);
                if (orderRes && orderRes.data) {
                    showReceiptModal(orderRes.data);
                }
            });
        });
    }

    // --- Dashboard & Reports Tab ---
    async function fetchDashboard() {
        const res = await apiRequest('get_dashboard_summary');
        if (!res || !res.data) return;

        const data = res.data;

        document.getElementById('dash-today-sales').innerText = `฿${parseFloat(data.today.total_sales).toFixed(2)}`;
        document.getElementById('dash-today-orders').innerText = `${data.today.total_orders} รายการ`;
        document.getElementById('dash-total-sales').innerText = `฿${parseFloat(data.overall.total_sales).toFixed(2)}`;
        document.getElementById('dash-total-products').innerText = `${data.total_products} รายการ`;

        // Top products
        const topList = document.getElementById('dash-top-products');
        if (topList) {
            if (data.top_products.length === 0) {
                topList.innerHTML = `<li class="list-group-item text-center text-muted py-3">ยังไม่มีข้อมูลสินค้าขายดี</li>`;
            } else {
                topList.innerHTML = data.top_products.map((tp, idx) => `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-primary rounded-pill me-2">${idx + 1}</span>
                            <span class="fw-semibold">${tp.product_name}</span>
                        </div>
                        <span class="badge bg-light text-dark border">ขายได้ ${tp.total_qty} ชิ้น (฿${parseFloat(tp.total_amount).toFixed(2)})</span>
                    </li>
                `).join('');
            }
        }

        // Low stock
        const lowStockList = document.getElementById('dash-low-stock');
        if (lowStockList) {
            if (data.low_stock.length === 0) {
                lowStockList.innerHTML = `<li class="list-group-item text-center text-success py-3"><i class="bi bi-check-circle me-1"></i> สินค้าในสต็อกเพียงพอทุกรายการ</li>`;
            } else {
                lowStockList.innerHTML = data.low_stock.map(p => `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">${p.name}</span>
                        <span class="badge bg-danger">คงเหลือ ${p.stock} ชิ้น</span>
                    </li>
                `).join('');
            }
        }
    }

    // --- Tab Change Listeners ---
    document.getElementById('orders-tab')?.addEventListener('click', fetchOrdersHistory);
    document.getElementById('dashboard-tab')?.addEventListener('click', fetchDashboard);
    document.getElementById('products-tab')?.addEventListener('click', fetchProducts);
    document.getElementById('categories-tab')?.addEventListener('click', fetchCategories);

    // Initial Load
    initData();
});
