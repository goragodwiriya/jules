/* ==========================================================================
   CLOTHE E-Commerce Interactive Script (Vanilla JavaScript)
   ========================================================================== */

// --- Mockup Products Data ---
const PRODUCTS = [
    {
        id: 101,
        name: "เสื้อเชิ้ตโอเวอร์ไซส์ ลินินพรีเมียม",
        category: "men",
        price: 890,
        originalPrice: 1190,
        rating: 4.8,
        reviewsCount: 124,
        isNew: true,
        colors: [
            { name: "ขาวมินิมอล", hex: "#ffffff" },
            { name: "น้ำตาลเบจ", hex: "#d7c4b7" },
            { name: "เขียวโอลีฟ", hex: "#556b2f" }
        ],
        sizes: ["S", "M", "L", "XL"],
        image: "https://images.unsplash.com/photo-1598033129183-c4f50c736f10?auto=format&fit=crop&w=800&q=80",
        gallery: [
            "https://images.unsplash.com/photo-1598033129183-c4f50c736f10?auto=format&fit=crop&w=800&q=80",
            "https://images.unsplash.com/photo-1617137968427-85924c800a22?auto=format&fit=crop&w=800&q=80"
        ],
        description: "เสื้อเชิ้ตคอจีนผ้าลินินผสมฝ้าย ให้ความรู้สึกโปร่งสบาย ระบายอากาศได้ดีเยี่ยม ตัดเย็บทรงโอเวอร์ไซส์ทันสมัย สวมใส่ได้ทั้งลุคชิลๆ และลุคทางการ"
    },
    {
        id: 102,
        name: "เดรสยาวสายเดี่ยว ผ้าซาตินพรีเมียม",
        category: "women",
        price: 1290,
        originalPrice: 1590,
        rating: 4.9,
        reviewsCount: 98,
        isNew: true,
        colors: [
            { name: "ดำคลาสสิก", hex: "#111111" },
            { name: "ชมพูโรสโกลด์", hex: "#e8c5c8" },
            { name: "เขียวเอเมอรัลด์", hex: "#0f5257" }
        ],
        sizes: ["XS", "S", "M", "L"],
        image: "https://images.unsplash.com/photo-1539109136881-3be0616acf4b?auto=format&fit=crop&w=800&q=80",
        gallery: [
            "https://images.unsplash.com/photo-1539109136881-3be0616acf4b?auto=format&fit=crop&w=800&q=80",
            "https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=800&q=80"
        ],
        description: "เดรสยาวผ้าซาตินเนื้อนุ่มพริ้วไหว ดีไซน์สายเดี่ยวปรับระดับได้ ทรงสวยเข้ารูปอย่างเป็นธรรมชาติ เพิ่มเสน่ห์ให้ลุคยามค่ำคืนและออกงานพิเศษ"
    },
    {
        id: 103,
        name: "เสื้อฮู้ดดี้ สตรีทสไตล์ Cotton 100%",
        category: "outerwear",
        price: 990,
        originalPrice: 1390,
        rating: 4.7,
        reviewsCount: 210,
        isNew: false,
        colors: [
            { name: "เทาชาร์โคล", hex: "#36454f" },
            { name: "ดำแมตต์", hex: "#1a1a1a" },
            { name: "ครีมโอ๊ตมีล", hex: "#e5ded4" }
        ],
        sizes: ["M", "L", "XL", "2XL"],
        image: "https://images.unsplash.com/photo-1556905055-8f358a7a47b2?auto=format&fit=crop&w=800&q=80",
        gallery: [
            "https://images.unsplash.com/photo-1556905055-8f358a7a47b2?auto=format&fit=crop&w=800&q=80",
            "https://images.unsplash.com/photo-1509967419530-da38b4704bc6?auto=format&fit=crop&w=800&q=80"
        ],
        description: "เสื้อฮู้ดผ้าคอตตอนเนื้อหนานุ่ม ซับในผ้านุ่มพิเศษ กระเป๋าหน้าใหญ่จุของสะดวก ทรงสตรีททรงหลวม แมตช์ง่ายกับกางเกงยีนส์หรือกางเกงวอร์ม"
    },
    {
        id: 104,
        name: "กางเกงขายาวทรงสแล็ค มินิมอลทรงลวดลาย",
        category: "pants",
        price: 790,
        originalPrice: 990,
        rating: 4.6,
        reviewsCount: 85,
        isNew: false,
        colors: [
            { name: "ดำ", hex: "#000000" },
            { name: "เทาเข้ม", hex: "#4b5563" },
            { name: "น้ำตาลคาเมล", hex: "#c19a6b" }
        ],
        sizes: ["28", "30", "32", "34", "36"],
        image: "https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?auto=format&fit=crop&w=800&q=80",
        gallery: [
            "https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?auto=format&fit=crop&w=800&q=80"
        ],
        description: "กางเกงสแล็คทรงกระบอกเล็ก ยืดหยุ่นนิดๆ ใส่สบาย ไม่รัดแน่น ทรงสวยช่วยเสริมสรีระให้ดูเพรียว เหมาะสำหรับใส่ทำงานหรือเที่ยวคาเฟ่"
    },
    {
        id: 105,
        name: "เสื้อเบลเซอร์ทรงหลวม สมาร์ทแคชชวล",
        category: "outerwear",
        price: 1590,
        originalPrice: 1990,
        rating: 4.9,
        reviewsCount: 76,
        isNew: true,
        colors: [
            { name: "เบจเกาหลี", hex: "#e3d5ca" },
            { name: "กรมท่า", hex: "#1d2d44" }
        ],
        sizes: ["S", "M", "L"],
        image: "https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=800&q=80",
        gallery: [
            "https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=800&q=80"
        ],
        description: "เบลเซอร์สไตล์เกาหลีทรงโอเวอร์ไซส์ มีฟองน้ำซับบ่าบางๆ ให้ลุคสวยสง่า เรียบหรู แต่ยังดูผ่อนคลาย"
    },
    {
        id: 106,
        name: "เสื้อยืดคอตตอนแท้ 100% Heavyweight",
        category: "men",
        price: 490,
        originalPrice: 690,
        rating: 4.8,
        reviewsCount: 340,
        isNew: false,
        colors: [
            { name: "ขาวสนิท", hex: "#ffffff" },
            { name: "ดำเข้ม", hex: "#0a0a0a" },
            { name: "เขียวซาลเวีย", hex: "#9caf88" }
        ],
        sizes: ["S", "M", "L", "XL", "2XL"],
        image: "https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=800&q=80",
        gallery: [
            "https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=800&q=80"
        ],
        description: "เสื้อยืดคอกลมผ้าหนา 220 GSM อยู่ทรงสวย ไม่หดไม่ย้วย คอเสื้อฟิตพอดี ไม่ย้วยง่าย แมตช์ได้กับทุกชุดในตู้"
    },
    {
        id: 107,
        name: "กางเกงยีนส์เอวสูงทรงกระบอกใหญ่ Vintage",
        category: "pants",
        price: 1190,
        originalPrice: 1490,
        rating: 4.7,
        reviewsCount: 155,
        isNew: false,
        colors: [
            { name: "ยีนส์ฟอกซีด", hex: "#7b9ec9" },
            { name: "ยีนส์เข้ม", hex: "#2b4162" }
        ],
        sizes: ["26", "28", "30", "32"],
        image: "https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&w=800&q=80",
        gallery: [
            "https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&w=800&q=80"
        ],
        description: "กางเกงยีนส์เอวสูงสไตล์วินเทจ ยีนส์เนื้อแท้ไม่ยืด แต่งดีเทลฟอกสีละมุน ช่วยเก็บทรงขาให้ดูยาวสวยงาม"
    },
    {
        id: 108,
        name: "กระเป๋าสะพายข้างหนังแท้สไตล์มินิมอล",
        category: "accessories",
        price: 890,
        originalPrice: 1290,
        rating: 4.9,
        reviewsCount: 62,
        isNew: true,
        colors: [
            { name: "ดำ", hex: "#111111" },
            { name: "น้ำตาลคาราเมล", hex: "#a0522d" }
        ],
        sizes: ["Free Size"],
        image: "https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=800&q=80",
        gallery: [
            "https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=800&q=80"
        ],
        description: "กระเป๋าสะพายหนัง PU เกรดพรีเมียม สัมผัสนุ่ม เรียบหรู จุของได้เยอะ เหมาะสำหรับการใช้งานประจำวัน"
    }
];

// --- Application State ---
let cart = JSON.parse(localStorage.getItem('clothe_cart')) || [];
let wishlist = JSON.parse(localStorage.getItem('clothe_wishlist')) || [];
let activeCategory = 'all';
let searchQuery = '';
let activeSort = 'featured';
let appliedCoupon = null; // { code: 'SUMMER20', discountPercent: 20 }

// --- DOM Elements ---
document.addEventListener('DOMContentLoaded', () => {
    initApp();
});

function initApp() {
    renderProducts();
    updateCartUI();
    updateWishlistUI();
    setupEventListeners();
}

// --- Render Products Grid ---
function renderProducts() {
    const productGrid = document.getElementById('productGrid');
    const noProductsFound = document.getElementById('noProductsFound');

    let filtered = PRODUCTS.filter(p => {
        const matchesCategory = activeCategory === 'all' || p.category === activeCategory;
        const matchesSearch = p.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
                              p.description.toLowerCase().includes(searchQuery.toLowerCase());
        return matchesCategory && matchesSearch;
    });

    // Sorting
    if (activeSort === 'price-low') {
        filtered.sort((a, b) => a.price - b.price);
    } else if (activeSort === 'price-high') {
        filtered.sort((a, b) => b.price - a.price);
    } else if (activeSort === 'rating') {
        filtered.sort((a, b) => b.rating - a.rating);
    } else if (activeSort === 'newest') {
        filtered.sort((a, b) => (b.isNew ? 1 : 0) - (a.isNew ? 1 : 0));
    }

    if (filtered.length === 0) {
        productGrid.innerHTML = '';
        noProductsFound.classList.remove('d-none');
        return;
    }

    noProductsFound.classList.add('d-none');

    productGrid.innerHTML = filtered.map(product => {
        const isWishlisted = wishlist.some(id => id === product.id);
        const discountPercent = product.originalPrice ? Math.round(((product.originalPrice - product.price) / product.originalPrice) * 100) : 0;

        return `
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100 rounded-4 overflow-hidden product-card bg-white">
                    <div class="product-img-wrapper">
                        ${product.isNew ? `<span class="badge bg-primary badge-discount">NEW</span>` : ''}
                        ${discountPercent > 0 ? `<span class="badge bg-danger badge-discount ${product.isNew ? 'ms-5' : ''}">-${discountPercent}%</span>` : ''}

                        <button class="btn-wishlist ${isWishlisted ? 'active' : ''}" onclick="toggleWishlist(${product.id})" title="เพิ่มในรายการโปรด">
                            <i class="bi ${isWishlisted ? 'bi-heart-fill' : 'bi-heart'}"></i>
                        </button>

                        <img src="${product.image}" alt="${product.name}" loading="lazy">

                        <button class="btn btn-sm btn-dark rounded-pill px-3 shadow btn-quickview" onclick="openQuickView(${product.id})">
                            <i class="bi bi-eye me-1"></i> ดูรายละเอียด
                        </button>
                    </div>

                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-uppercase text-muted fw-semibold" style="font-size: 0.75rem;">${getCategoryName(product.category)}</small>
                                <small class="text-warning fw-semibold"><i class="bi bi-star-fill"></i> ${product.rating}</small>
                            </div>
                            <h6 class="card-title fw-bold text-dark text-truncate mb-2" title="${product.name}">${product.name}</h6>
                        </div>

                        <div>
                            <div class="d-flex align-items-baseline gap-2 mb-3">
                                <span class="fs-5 fw-bold text-dark">฿${product.price.toLocaleString()}</span>
                                ${product.originalPrice ? `<span class="small text-muted text-decoration-line-through">฿${product.originalPrice.toLocaleString()}</span>` : ''}
                            </div>

                            <button class="btn btn-outline-dark btn-sm rounded-pill w-100 fw-semibold" onclick="quickAddToCart(${product.id})">
                                <i class="bi bi-cart-plus me-1"></i> ใส่ตะกร้า
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');

    updateFilterBadges();
}

function getCategoryName(cat) {
    const names = {
        men: 'เสื้อผ้าผู้ชาย',
        women: 'เสื้อผ้าผู้หญิง',
        outerwear: 'แจ็คเก็ต/ฮู้ดดี้',
        pants: 'กางเกง',
        accessories: 'เครื่องประดับ'
    };
    return names[cat] || 'สินค้าแฟชั่น';
}

// --- Cart Operations ---
function addToCart(productId, selectedSize, selectedColor, quantity = 1) {
    const product = PRODUCTS.find(p => p.id === productId);
    if (!product) return;

    const size = selectedSize || product.sizes[0];
    const color = selectedColor || product.colors[0].name;

    const existingIndex = cart.findIndex(item => item.id === productId && item.size === size && item.color === color);

    if (existingIndex > -1) {
        cart[existingIndex].quantity += quantity;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            image: product.image,
            size: size,
            color: color,
            quantity: quantity
        });
    }

    saveCart();
    updateCartUI();
    showToast(`เพิ่ม "${product.name}" ลงในตะกร้าเรียบร้อยแล้ว!`);
}

function quickAddToCart(productId) {
    addToCart(productId);
}

function removeFromCart(index) {
    cart.splice(index, 1);
    saveCart();
    updateCartUI();
}

function updateQuantity(index, delta) {
    if (cart[index]) {
        cart[index].quantity += delta;
        if (cart[index].quantity <= 0) {
            cart.splice(index, 1);
        }
        saveCart();
        updateCartUI();
    }
}

function clearCart() {
    cart = [];
    appliedCoupon = null;
    saveCart();
    updateCartUI();
    showToast("ล้างตะกร้าสินค้าเรียบร้อยแล้ว", "info");
}

function saveCart() {
    localStorage.setItem('clothe_cart', JSON.stringify(cart));
}

function updateCartUI() {
    const cartBadge = document.getElementById('cartBadge');
    const cartDrawerCount = document.getElementById('cartDrawerCount');
    const cartItemsList = document.getElementById('cartItemsList');
    const cartSubtotal = document.getElementById('cartSubtotal');
    const cartDiscount = document.getElementById('cartDiscount');
    const cartShipping = document.getElementById('cartShipping');
    const cartTotal = document.getElementById('cartTotal');
    const checkoutPayAmount = document.getElementById('checkoutPayAmount');

    const totalQty = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartBadge.textContent = totalQty;
    cartDrawerCount.textContent = totalQty;

    if (cart.length === 0) {
        cartItemsList.innerHTML = `
            <div class="text-center py-5">
                <i class="bi bi-bag-x display-1 text-muted opacity-50"></i>
                <p class="mt-3 text-muted">ยังไม่มีสินค้าในตะกร้าของคุณ</p>
                <button class="btn btn-outline-dark btn-sm rounded-pill px-4" data-bs-dismiss="offcanvas">เริ่มช้อปเลย</button>
            </div>
        `;
        cartSubtotal.textContent = '฿0';
        cartDiscount.textContent = '-฿0';
        cartShipping.textContent = '฿0';
        cartTotal.textContent = '฿0';
        if (checkoutPayAmount) checkoutPayAmount.textContent = '฿0';
        return;
    }

    cartItemsList.innerHTML = cart.map((item, index) => `
        <div class="d-flex gap-3 align-items-center mb-3 pb-3 border-bottom">
            <img src="${item.image}" alt="${item.name}" class="rounded-3" style="width: 70px; height: 85px; object-fit: cover;">
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1 small text-truncate" style="max-width: 180px;">${item.name}</h6>
                <div class="small text-muted mb-2">ไซส์: ${item.size} | สี: ${item.color}</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="input-group input-group-sm" style="width: 90px;">
                        <button class="btn btn-outline-secondary px-2" onclick="updateQuantity(${index}, -1)">-</button>
                        <span class="form-control text-center px-1 fw-semibold">${item.quantity}</span>
                        <button class="btn btn-outline-secondary px-2" onclick="updateQuantity(${index}, 1)">+</button>
                    </div>
                    <span class="fw-bold text-dark">฿${(item.price * item.quantity).toLocaleString()}</span>
                </div>
            </div>
            <button class="btn btn-link text-muted p-0 ms-1" onclick="removeFromCart(${index})">
                <i class="bi bi-x-lg fs-5"></i>
            </button>
        </div>
    `).join('');

    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    let discount = 0;
    if (appliedCoupon) {
        discount = Math.round((subtotal * appliedCoupon.discountPercent) / 100);
    }
    const shipping = subtotal >= 1200 || subtotal === 0 ? 0 : 50;
    const finalTotal = Math.max(0, subtotal - discount + shipping);

    cartSubtotal.textContent = `฿${subtotal.toLocaleString()}`;
    cartDiscount.textContent = `-฿${discount.toLocaleString()}`;
    cartShipping.textContent = shipping === 0 ? 'ส่งฟรี' : `฿${shipping}`;
    cartTotal.textContent = `฿${finalTotal.toLocaleString()}`;
    if (checkoutPayAmount) checkoutPayAmount.textContent = `฿${finalTotal.toLocaleString()}`;
}

// --- Wishlist Operations ---
function toggleWishlist(productId) {
    const index = wishlist.indexOf(productId);
    if (index > -1) {
        wishlist.splice(index, 1);
        showToast("ลบออกจากรายการโปรดแล้ว", "info");
    } else {
        wishlist.push(productId);
        showToast("เพิ่มลงในรายการโปรดแล้ว!", "success");
    }
    localStorage.setItem('clothe_wishlist', JSON.stringify(wishlist));
    updateWishlistUI();
    renderProducts();
}

function updateWishlistUI() {
    const wishlistBadge = document.getElementById('wishlistBadge');
    const wishlistDrawerCount = document.getElementById('wishlistDrawerCount');
    const wishlistItemsList = document.getElementById('wishlistItemsList');

    wishlistBadge.textContent = wishlist.length;
    wishlistDrawerCount.textContent = wishlist.length;

    if (wishlist.length === 0) {
        wishlistItemsList.innerHTML = `
            <div class="text-center py-5">
                <i class="bi bi-heart-fill display-1 text-muted opacity-25"></i>
                <p class="mt-3 text-muted">คุณยังไม่มีรายการสินค้าที่ถูกใจ</p>
            </div>
        `;
        return;
    }

    const items = PRODUCTS.filter(p => wishlist.includes(p.id));
    wishlistItemsList.innerHTML = items.map(product => `
        <div class="d-flex gap-3 align-items-center mb-3 pb-3 border-bottom">
            <img src="${product.image}" alt="${product.name}" class="rounded-3" style="width: 65px; height: 80px; object-fit: cover;">
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1 small text-truncate" style="max-width: 170px;">${product.name}</h6>
                <div class="fw-bold text-dark mb-2">฿${product.price.toLocaleString()}</div>
                <button class="btn btn-dark btn-sm rounded-pill px-3" onclick="quickAddToCart(${product.id}); toggleWishlist(${product.id});">
                    <i class="bi bi-bag-plus"></i> ย้ายไปตะกร้า
                </button>
            </div>
            <button class="btn btn-link text-danger p-0" onclick="toggleWishlist(${product.id})">
                <i class="bi bi-trash fs-5"></i>
            </button>
        </div>
    `).join('');
}

// --- Quick View Modal ---
function openQuickView(productId) {
    const product = PRODUCTS.find(p => p.id === productId);
    if (!product) return;

    const quickViewContent = document.getElementById('quickViewContent');
    let selectedSize = product.sizes[0];
    let selectedColor = product.colors[0].name;

    quickViewContent.innerHTML = `
        <div class="col-md-6 bg-light d-flex align-items-center justify-content-center p-3">
            <img src="${product.image}" id="qvMainImage" class="img-fluid rounded-3 shadow-sm" style="max-height: 420px; object-fit: cover;">
        </div>
        <div class="col-md-6 p-4 d-flex flex-column justify-content-between">
            <div>
                <span class="badge bg-primary mb-2 text-uppercase">${getCategoryName(product.category)}</span>
                <h4 class="fw-bold mb-2">${product.name}</h4>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="fs-4 fw-bold text-dark">฿${product.price.toLocaleString()}</span>
                    ${product.originalPrice ? `<span class="text-muted text-decoration-line-through">฿${product.originalPrice.toLocaleString()}</span>` : ''}
                    <span class="badge bg-warning text-dark ms-2"><i class="bi bi-star-fill"></i> ${product.rating} (${product.reviewsCount} รีวิว)</span>
                </div>
                <p class="text-muted small mb-3">${product.description}</p>

                <!-- Color Selection -->
                <div class="mb-3">
                    <label class="form-label small fw-bold">เลือกสี: <span id="selectedColorName" class="text-primary">${selectedColor}</span></label>
                    <div class="d-flex gap-2">
                        ${product.colors.map((c, i) => `
                            <span class="color-option-btn ${i === 0 ? 'active' : ''}" style="background-color: ${c.hex};" onclick="selectQVColor(this, '${c.name}')" title="${c.name}"></span>
                        `).join('')}
                    </div>
                </div>

                <!-- Size Selection -->
                <div class="mb-4">
                    <label class="form-label small fw-bold">เลือกขนาด (Size): <span id="selectedSizeName" class="text-primary">${selectedSize}</span></label>
                    <div class="d-flex gap-2 flex-wrap">
                        ${product.sizes.map((s, i) => `
                            <span class="size-badge-btn ${i === 0 ? 'active' : ''}" onclick="selectQVSize(this, '${s}')">${s}</span>
                        `).join('')}
                    </div>
                </div>
            </div>

            <div>
                <button class="btn btn-dark btn-lg rounded-pill w-100 fw-bold" id="btnQVAddToCart">
                    <i class="bi bi-bag-plus me-2"></i> เพิ่มลงในตะกร้า
                </button>
            </div>
        </div>
    `;

    // Modal Instance
    const qvModal = new bootstrap.Modal(document.getElementById('quickViewModal'));
    qvModal.show();

    // Event listener for add to cart from quick view
    document.getElementById('btnQVAddToCart').onclick = () => {
        const curColor = document.getElementById('selectedColorName').textContent;
        const curSize = document.getElementById('selectedSizeName').textContent;
        addToCart(product.id, curSize, curColor);
        qvModal.hide();
    };
}

function selectQVColor(element, colorName) {
    document.querySelectorAll('.color-option-btn').forEach(btn => btn.classList.remove('active'));
    element.classList.add('active');
    document.getElementById('selectedColorName').textContent = colorName;
}

function selectQVSize(element, sizeName) {
    document.querySelectorAll('.size-badge-btn').forEach(btn => btn.classList.remove('active'));
    element.classList.add('active');
    document.getElementById('selectedSizeName').textContent = sizeName;
}

// --- Filtering & Sorting ---
function filterByCategory(category) {
    activeCategory = category;

    document.querySelectorAll('#categoryPills button').forEach(btn => {
        if (btn.dataset.category === category) {
            btn.classList.add('btn-dark', 'active');
            btn.classList.remove('btn-outline-secondary');
        } else {
            btn.classList.remove('btn-dark', 'active');
            btn.classList.add('btn-outline-secondary');
        }
    });

    renderProducts();
}

function updateFilterBadges() {
    const container = document.getElementById('activeFiltersContainer');
    const catBadge = document.getElementById('activeCategoryBadge');
    const searchBadge = document.getElementById('activeSearchBadge');

    let hasFilter = false;

    if (activeCategory !== 'all') {
        catBadge.textContent = `หมวดหมู่: ${getCategoryName(activeCategory)}`;
        catBadge.classList.remove('d-none');
        hasFilter = true;
    } else {
        catBadge.classList.add('d-none');
    }

    if (searchQuery.trim() !== '') {
        searchBadge.textContent = `ค้นหา: "${searchQuery}"`;
        searchBadge.classList.remove('d-none');
        hasFilter = true;
    } else {
        searchBadge.classList.add('d-none');
    }

    if (hasFilter) {
        container.classList.remove('d-none');
        container.classList.add('d-flex');
    } else {
        container.classList.add('d-none');
        container.classList.remove('d-flex');
    }
}

function resetFilters() {
    activeCategory = 'all';
    searchQuery = '';
    document.getElementById('searchInput').value = '';
    document.getElementById('searchInputNav').value = '';
    filterByCategory('all');
}

// --- Toast Notifications ---
function showToast(message, type = 'success') {
    const liveToast = document.getElementById('liveToast');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');

    toastMessage.textContent = message;
    if (type === 'info') {
        toastIcon.className = 'bi bi-info-circle-fill text-info fs-5';
    } else {
        toastIcon.className = 'bi bi-check-circle-fill text-success fs-5';
    }

    const toast = new bootstrap.Toast(liveToast);
    toast.show();
}

// --- Setup Event Listeners ---
function setupEventListeners() {
    // Category pills click
    document.querySelectorAll('#categoryPills button').forEach(button => {
        button.addEventListener('click', () => {
            filterByCategory(button.dataset.category);
        });
    });

    // Search input catalog
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', (e) => {
        searchQuery = e.target.value;
        renderProducts();
    });

    // Search input nav
    const searchInputNav = document.getElementById('searchInputNav');
    searchInputNav.addEventListener('keyup', (e) => {
        if (e.key === 'Enter') {
            searchQuery = e.target.value;
            searchInput.value = searchQuery;
            document.getElementById('shop').scrollIntoView({ behavior: 'smooth' });
            renderProducts();
        }
    });

    document.getElementById('btnSearchNav').addEventListener('click', () => {
        searchQuery = searchInputNav.value;
        searchInput.value = searchQuery;
        document.getElementById('shop').scrollIntoView({ behavior: 'smooth' });
        renderProducts();
    });

    // Sort select
    document.getElementById('sortSelect').addEventListener('change', (e) => {
        activeSort = e.value || e.target.value;
        renderProducts();
    });

    // Clear filters button
    document.getElementById('btnClearFilters').addEventListener('click', () => {
        resetFilters();
    });

    // Clear cart button
    document.getElementById('btnClearCart').addEventListener('click', () => {
        if (confirm('คุณต้องการล้างสินค้าทั้งหมดในตะกร้าใช่หรือไม่?')) {
            clearCart();
        }
    });

    // Promo Coupon Copy
    document.getElementById('btnCopyPromo').addEventListener('click', () => {
        const promoCode = document.getElementById('promoCodeInput').value;
        navigator.clipboard.writeText(promoCode).then(() => {
            showToast(`คัดลอกโค้ด ${promoCode} สำเร็จแล้ว!`);
        });
    });

    // Apply Coupon
    document.getElementById('btnApplyCoupon').addEventListener('click', () => {
        const input = document.getElementById('couponInput').value.trim().toUpperCase();
        const msg = document.getElementById('couponMessage');

        if (input === 'SUMMER20') {
            appliedCoupon = { code: 'SUMMER20', discountPercent: 20 };
            msg.className = 'form-text small mt-1 text-success fw-semibold';
            msg.textContent = '🎉 ใช้โค้ด SUMMER20 สำเร็จ (รับส่วนลด 20%)';
            updateCartUI();
            showToast("ใช้โค้ดส่วนลดสำเร็จ!");
        } else if (input === '') {
            appliedCoupon = null;
            msg.textContent = '';
            updateCartUI();
        } else {
            msg.className = 'form-text small mt-1 text-danger';
            msg.textContent = '❌ โค้ดส่วนลดไม่ถูกต้องหรือหมดอายุ';
        }
    });

    // Submit Order
    document.getElementById('btnSubmitOrder').addEventListener('click', () => {
        const form = document.getElementById('checkoutForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        if (cart.length === 0) {
            alert('ไม่มีสินค้าในตะกร้า กรุณาเลือกสินค้าก่อนทำการสั่งซื้อ');
            return;
        }

        const name = document.getElementById('custName').value;
        const totalAmount = document.getElementById('cartTotal').textContent;
        const orderId = '#ORD-' + Math.floor(100000 + Math.random() * 900000);

        // Hide checkout modal
        const checkoutModalEl = document.getElementById('checkoutModal');
        const checkoutModal = bootstrap.Modal.getInstance(checkoutModalEl);
        if (checkoutModal) checkoutModal.hide();

        // Populate order success modal
        document.getElementById('orderSuccessId').textContent = orderId;
        document.getElementById('orderSuccessCustomer').textContent = name;
        document.getElementById('orderSuccessAmount').textContent = totalAmount;

        // Clear Cart
        cart = [];
        appliedCoupon = null;
        saveCart();
        updateCartUI();

        // Show Success Modal
        const orderSuccessModal = new bootstrap.Modal(document.getElementById('orderSuccessModal'));
        orderSuccessModal.show();
    });
}
