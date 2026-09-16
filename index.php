<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบขายหน้าร้าน POS System</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom POS CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center me-4" href="#">
                <i class="bi bi-shop fs-3 me-2 text-warning"></i>
                <span class="fw-bold fs-4">SmartPOS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 nav-pills" id="mainTab" role="tablist">
                    <li class="nav-item me-1">
                        <button class="nav-link active" id="pos-tab" data-bs-toggle="tab" data-bs-target="#pos-view" type="button">
                            <i class="bi bi-calculator me-1"></i> ขายหน้าร้าน (POS)
                        </button>
                    </li>
                    <li class="nav-item me-1">
                        <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products-view" type="button">
                            <i class="bi bi-box-seam me-1"></i> จัดการสินค้า & สต็อก
                        </button>
                    </li>
                    <li class="nav-item me-1">
                        <button class="nav-link" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories-view" type="button">
                            <i class="bi bi-tags me-1"></i> หมวดหมู่สินค้า
                        </button>
                    </li>
                    <li class="nav-item me-1">
                        <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders-view" type="button">
                            <i class="bi bi-receipt me-1"></i> ประวัติการขาย
                        </button>
                    </li>
                    <li class="nav-item me-1">
                        <button class="nav-link" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard-view" type="button">
                            <i class="bi bi-bar-chart-line me-1"></i> สรุปยอดขาย & รายงาน
                        </button>
                    </li>
                </ul>
                <div class="d-flex text-white align-items-center">
                    <i class="bi bi-clock me-2"></i>
                    <span id="live-clock" class="fw-medium">--:--:--</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="container-fluid my-3">
        <div class="tab-content" id="mainTabContent">

            <!-- 1. POS TAB VIEW -->
            <div class="tab-pane fade show active" id="pos-view" role="tabpanel">
                <div class="row g-3">
                    <!-- Left Column: Product Selection & Categories -->
                    <div class="col-lg-7 col-xl-8">
                        <!-- Search & Category Filter Bar -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body p-3">
                                <div class="row g-2 align-items-center">
                                    <div class="col-md-6 col-lg-5">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                            <input type="text" id="search-product" class="form-control border-start-0" placeholder="ค้นหาชื่อสินค้า หรือ รหัสบาร์โค้ด...">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-7">
                                        <div class="d-flex gap-2 overflow-x-auto pb-1" id="category-pills">
                                            <!-- Category pills rendered dynamically -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Grid Container -->
                        <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3" id="product-grid">
                            <!-- Product cards rendered dynamically -->
                        </div>
                    </div>

                    <!-- Right Column: Shopping Cart Side Panel -->
                    <div class="col-lg-5 col-xl-4">
                        <div class="cart-panel">
                            <div class="cart-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-cart3 me-2"></i>รายการสั่งซื้อ</h5>
                                <button type="button" class="btn btn-outline-danger btn-sm" id="btn-clear-cart">
                                    <i class="bi bi-trash me-1"></i> ล้างตะกร้า
                                </button>
                            </div>

                            <div class="cart-items-container" id="cart-items-list">
                                <!-- Cart items rendered dynamically -->
                            </div>

                            <div class="cart-summary">
                                <div class="d-flex justify-content-between text-muted mb-1">
                                    <span>ราคารวมสินค้า:</span>
                                    <span id="cart-subtotal">฿0.00</span>
                                </div>
                                <div class="d-flex justify-content-between text-muted mb-1 align-items-center">
                                    <span>ส่วนลด (บาท):</span>
                                    <input type="number" id="cart-discount" class="form-control form-control-sm text-end" style="width: 90px;" value="0" min="0">
                                </div>
                                <div class="d-flex justify-content-between text-muted mb-2 align-items-center">
                                    <span>ภาษี VAT (7%):</span>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" id="cart-tax-toggle">
                                        <span id="cart-tax-amount" class="ms-1">฿0.00</span>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between fw-bold fs-4 text-primary mb-3">
                                    <span>ยอดชำระสุทธิ:</span>
                                    <span id="cart-final-total">฿0.00</span>
                                </div>

                                <div class="row g-2">
                                    <div class="col-6">
                                        <button class="btn btn-success btn-lg w-100 fw-bold py-2" id="btn-pay-cash" disabled>
                                            <i class="bi bi-cash-stack me-1"></i> เงินสด
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-primary btn-lg w-100 fw-bold py-2" id="btn-pay-qr" disabled>
                                            <i class="bi bi-qr-code-scan me-1"></i> สแกน QR
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. PRODUCTS MANAGEMENT VIEW -->
            <div class="tab-pane fade" id="products-view" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-box-seam me-2"></i>รายการสินค้าทั้งหมด</h5>
                        <button class="btn btn-primary" id="btn-add-product">
                            <i class="bi bi-plus-circle me-1"></i> เพิ่มสินค้าใหม่
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 70px;">รูปภาพ</th>
                                        <th>บาร์โค้ด</th>
                                        <th>ชื่อสินค้า</th>
                                        <th>หมวดหมู่</th>
                                        <th class="text-end">ราคาขาย</th>
                                        <th class="text-end">ต้นทุน</th>
                                        <th class="text-center">คงเหลือ</th>
                                        <th class="text-center" style="width: 140px;">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="products-table-body">
                                    <!-- Table rows rendered dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. CATEGORIES MANAGEMENT VIEW -->
            <div class="tab-pane fade" id="categories-view" role="tabpanel">
                <div class="row g-3">
                    <div class="col-md-5">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 fw-bold" id="cat-form-title"><i class="bi bi-plus-circle me-2"></i>เพิ่มหมวดหมู่ใหม่</h5>
                            </div>
                            <div class="card-body">
                                <form id="form-category">
                                    <input type="hidden" id="cat-id">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">ชื่อหมวดหมู่</label>
                                        <input type="text" id="cat-name" class="form-control" placeholder="เช่น กาแฟ & เครื่องดื่ม" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">ไอคอน (Bootstrap Icon)</label>
                                        <input type="text" id="cat-icon" class="form-control" value="bi-tag" placeholder="เช่น bi-cup-hot">
                                        <div class="form-text">สามารถระบุ class ของ Bootstrap Icon ได้</div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary flex-grow-1" id="btn-save-cat">บันทึกหมวดหมู่</button>
                                        <button type="button" class="btn btn-light d-none" id="btn-cancel-cat">ยกเลิก</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 fw-bold"><i class="bi bi-tags me-2"></i>รายการหมวดหมู่สินค้า</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>ไอคอน</th>
                                                <th>ชื่อหมวดหมู่</th>
                                                <th class="text-center">จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="categories-table-body">
                                            <!-- Category rows -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. ORDERS HISTORY VIEW -->
            <div class="tab-pane fade" id="orders-view" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-receipt me-2"></i>ประวัติรายการขาย</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>เลขที่ใบเสร็จ</th>
                                        <th>วัน-เวลา</th>
                                        <th>ชำระโดย</th>
                                        <th class="text-end">ยอดรวม</th>
                                        <th class="text-end">ส่วนลด</th>
                                        <th class="text-end">ยอดสุทธิ</th>
                                        <th class="text-center">ใบเสร็จ</th>
                                    </tr>
                                </thead>
                                <tbody id="orders-table-body">
                                    <!-- Orders table rows -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. DASHBOARD & REPORTS VIEW -->
            <div class="tab-pane fade" id="dashboard-view" role="tabpanel">
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm bg-primary text-white stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50">ยอดขายวันนี้</h6>
                                        <h3 class="fw-bold mb-0" id="dash-today-sales">฿0.00</h3>
                                    </div>
                                    <i class="bi bi-currency-dollar fs-1 text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm bg-success text-white stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50">จำนวนออเดอร์วันนี้</h6>
                                        <h3 class="fw-bold mb-0" id="dash-today-orders">0 รายการ</h3>
                                    </div>
                                    <i class="bi bi-bag-check fs-1 text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm bg-warning text-dark stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-dark-50">ยอดขายสะสมทั้งหมด</h6>
                                        <h3 class="fw-bold mb-0" id="dash-total-sales">฿0.00</h3>
                                    </div>
                                    <i class="bi bi-graph-up-arrow fs-1 text-dark-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm bg-info text-white stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50">รายการสินค้าในระบบ</h6>
                                        <h3 class="fw-bold mb-0" id="dash-total-products">0 รายการ</h3>
                                    </div>
                                    <i class="bi bi-box-seam fs-1 text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-trophy text-warning me-2"></i>สินค้าขายดี Top 5</h6>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush" id="dash-top-products">
                                    <!-- Top products -->
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-exclamation-triangle text-danger me-2"></i>สินค้าสต็อกต่ำ (น้อยกว่า 10 ชิ้น)</h6>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush" id="dash-low-stock">
                                    <!-- Low stock -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Cash Payment Modal -->
    <div class="modal fade" id="cash-payment-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-cash-stack me-2"></i>ชำระเงินด้วยเงินสด</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="text-muted mb-1">ยอดเงินที่ต้องชำระ</div>
                        <div class="fs-1 fw-bold text-success" id="modal-cash-total">฿0.00</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">จำนวนเงินที่รับชำระ (บาท)</label>
                        <input type="number" id="cash-received-input" class="form-control form-control-lg text-center fs-3 fw-bold" placeholder="0.00" min="0" step="0.5">
                    </div>

                    <div class="d-flex gap-2 mb-3">
                        <button class="btn btn-outline-secondary flex-fill quick-cash-btn" data-amount="exact">พอดี</button>
                        <button class="btn btn-outline-secondary flex-fill quick-cash-btn" data-amount="100">100</button>
                        <button class="btn btn-outline-secondary flex-fill quick-cash-btn" data-amount="500">500</button>
                        <button class="btn btn-outline-secondary flex-fill quick-cash-btn" data-amount="1000">1,000</button>
                    </div>

                    <div class="card bg-light border-0 p-3 text-center">
                        <div class="text-muted mb-1">เงินทอน</div>
                        <div class="fs-2 fw-bold text-primary" id="cash-change-display">฿0.00</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-success fw-bold px-4" id="btn-confirm-cash-pay" disabled>ยืนยันการรับเงิน</button>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Payment Modal -->
    <div class="modal fade" id="qr-payment-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-qr-code-scan me-2"></i>สแกนชำระเงิน QR PromptPay</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="text-muted mb-1">ยอดเงินที่ต้องชำระ</div>
                    <div class="fs-2 fw-bold text-primary mb-3" id="modal-qr-total">฿0.00</div>

                    <!-- PromptPay Mockup QR Frame -->
                    <div class="d-inline-block border p-3 bg-white rounded shadow-sm mb-3">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=PROMPTPAY-MOCKUP-POS" alt="PromptPay QR Code" class="img-fluid" style="width: 200px; height: 200px;">
                        <div class="mt-2 text-muted small fw-bold">PromptPay / สแกนจ่ายผ่านแอปธนาคาร</div>
                    </div>

                    <div class="alert alert-info py-2 mb-0 small">
                        <i class="bi bi-info-circle me-1"></i> เมื่อลูกค้าสแกนเรียบร้อย กดปุ่มยืนยันด้านล่าง
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary fw-bold px-4" id="btn-confirm-qr-pay">ยืนยันได้รับชำระเงิน</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt / Order Detail Modal -->
    <div class="modal fade" id="receipt-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body p-4" id="receipt-content">
                    <!-- Receipt rendered dynamically -->
                </div>
                <div class="modal-footer no-print">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i> พิมพ์ใบเสร็จ</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Form Modal (Add / Edit) -->
    <div class="modal fade" id="product-form-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="prod-modal-title">เพิ่มสินค้าใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-product">
                        <input type="hidden" id="prod-id">
                        <div class="mb-3">
                            <label class="form-label fw-medium">รหัสบาร์โค้ด / SKU</label>
                            <input type="text" id="prod-barcode" class="form-control" placeholder="เช่น 8850001">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">ชื่อสินค้า *</label>
                            <input type="text" id="prod-name" class="form-control" placeholder="เช่น กาแฟลาเต้" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">หมวดหมู่</label>
                            <select id="prod-category" class="form-select">
                                <!-- Categories populated dynamically -->
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-medium">ราคาขาย (บาท) *</label>
                                <input type="number" id="prod-price" class="form-control" step="0.5" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-medium">ต้นทุน (บาท)</label>
                                <input type="number" id="prod-cost" class="form-control" step="0.5" value="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">จำนวนคงเหลือ (สต็อก) *</label>
                            <input type="number" id="prod-stock" class="form-control" value="10" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">URL รูปภาพสินค้าจาก Internet</label>
                            <input type="url" id="prod-image" class="form-control" placeholder="https://example.com/image.jpg">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="btn-save-product">บันทึกข้อมูลสินค้า</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom POS Main Script -->
    <script src="js/main.js"></script>
</body>
</html>
