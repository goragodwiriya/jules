<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLOTHE_ - ร้านเสื้อผ้าแฟชั่นออนไลน์ครบวงจร</title>

    <!-- Google Fonts: Prompt & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Top Announcement Bar -->
    <div class="announcement-bar py-2 text-center text-white small fw-medium">
        <span>🎉 ต้อนรับซีซันใหม่! ใส่โค้ด <strong class="badge bg-warning text-dark mx-1">SUMMER20</strong> ลดทันที 20% | ส่งฟรีทั่วไทยเมื่อช้อปครบ ฿1,200</span>
    </div>

    <!-- Navigation Header -->
    <header class="sticky-top bg-white border-bottom shadow-sm">
        <nav class="navbar navbar-expand-lg py-3">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2 fw-bold fs-3 text-dark tracking-wide" href="#">
                    <span class="bg-dark text-white px-2 py-1 rounded-3 fs-4">C</span> CLOTHE<span class="text-primary">.</span>
                </a>

                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-medium">
                        <li class="nav-item"><a class="nav-link active" href="#hero">หน้าแรก</a></li>
                        <li class="nav-item"><a class="nav-link" href="#shop">สินค้าทั้งหมด</a></li>
                        <li class="nav-item"><a class="nav-link" href="#categories">หมวดหมู่</a></li>
                        <li class="nav-item"><a class="nav-link text-danger fw-semibold" href="#promotions"><i class="bi bi-fire me-1"></i> โปรโมชั่นพิเศษ</a></li>
                        <li class="nav-item"><a class="nav-link" href="#about">เกี่ยวกับเรา</a></li>
                    </ul>

                    <!-- Search & Action Buttons -->
                    <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                        <!-- Quick Search Bar -->
                        <div class="search-box position-relative">
                            <input type="text" id="searchInputNav" class="form-control form-control-sm rounded-pill ps-4 pe-5" placeholder="ค้นหาเสื้อผ้า, เดรส, เสื้อเชิ้ต...">
                            <button class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted p-0 pe-3 border-0" id="btnSearchNav">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>

                        <!-- Wishlist Button -->
                        <button class="btn btn-outline-dark rounded-circle position-relative p-2 d-flex align-items-center justify-content-center" style="width:42px; height:42px;" id="btnWishlistDrawer" data-bs-toggle="offcanvas" data-bs-target="#wishlistOffcanvas">
                            <i class="bi bi-heart fs-5"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="wishlistBadge">0</span>
                        </button>

                        <!-- Cart Button -->
                        <button class="btn btn-dark rounded-pill px-3 py-2 d-flex align-items-center gap-2" id="btnCartDrawer" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                            <i class="bi bi-bag-fill fs-5"></i>
                            <span class="fw-semibold d-none d-sm-inline">ตะกร้า</span>
                            <span class="badge bg-primary text-white rounded-pill px-2" id="cartBadge">0</span>
                        </button>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section id="hero" class="hero-section position-relative overflow-hidden bg-light py-5">
        <div class="container py-lg-4">
            <div class="row align-items-center gy-4">
                <div class="col-lg-6">
                    <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-2 rounded-pill mb-3">NEW ARRIVALS 2025</span>
                    <h1 class="display-4 fw-bold lh-sm mb-3">ยกระดับสไตล์ของคุณ<br>ด้วยเสื้อผ้าดีไซน์ทันสมัย</h1>
                    <p class="lead text-muted mb-4 fs-6">ค้นพบสตูดิโอแฟชั่นเสื้อผ้าผู้ชายและผู้หญิง คอลเลกชันใหม่ล่าสุด คุณภาพพรีเมียม สวมใส่สบาย เหมาะสำหรับทุกโอกาส</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#shop" class="btn btn-dark btn-lg rounded-pill px-4 shadow-sm">ช้อปเลยตอนนี้ <i class="bi bi-arrow-right ms-2"></i></a>
                        <a href="#categories" class="btn btn-outline-secondary btn-lg rounded-pill px-4">ดูคอลเลกชัน</a>
                    </div>
                    <div class="row mt-5 pt-3 border-top g-3">
                        <div class="col-4">
                            <h3 class="fw-bold mb-0">500+</h3>
                            <small class="text-muted">สินค้าแฟชั่น</small>
                        </div>
                        <div class="col-4">
                            <h3 class="fw-bold mb-0">4.9 ★</h3>
                            <small class="text-muted">รีวิวจากลูกค้า</small>
                        </div>
                        <div class="col-4">
                            <h3 class="fw-bold mb-0">100%</h3>
                            <small class="text-muted">การันตีของแท้</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="hero-image-wrapper position-relative mx-auto">
                        <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1000&q=80" alt="Fashion Store Banner" class="img-fluid rounded-4 shadow-lg hero-img">
                        <div class="hero-card-floating card border-0 shadow-lg p-3 rounded-4 bg-white text-start position-absolute bottom-0 start-0 mb-4 ms-lg-n3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                                    <i class="bi bi-truck fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">จัดส่งด่วนฟรี</h6>
                                    <small class="text-muted">เมื่อช้อปครบ 1,200 บาท</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Banner Grid -->
    <section id="categories" class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <span class="text-primary fw-semibold text-uppercase small tracking-wider">Collections</span>
                    <h2 class="fw-bold mb-0">หมวดหมู่สินค้ายอดนิยม</h2>
                </div>
                <button class="btn btn-link text-dark text-decoration-none fw-semibold p-0 filter-cat-all" onclick="filterByCategory('all')">ดูทั้งหมด <i class="bi bi-chevron-right"></i></button>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="category-card position-relative overflow-hidden rounded-4 text-white shadow-sm" onclick="filterByCategory('women')">
                        <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=800&q=80" alt="Women Clothing" class="w-100 category-img">
                        <div class="category-overlay p-4 d-flex flex-column justify-content-end">
                            <h3 class="fw-bold mb-1">เสื้อผ้าผู้หญิง</h3>
                            <p class="small text-white-50 mb-2">ชุดเดรส, เสื้อครอป, กระโปรง และอื่นๆ</p>
                            <span class="btn btn-sm btn-light rounded-pill align-self-start px-3">เลือกซื้อ <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="category-card position-relative overflow-hidden rounded-4 text-white shadow-sm" onclick="filterByCategory('men')">
                        <img src="https://images.unsplash.com/photo-1617137968427-85924c800a22?auto=format&fit=crop&w=800&q=80" alt="Men Clothing" class="w-100 category-img">
                        <div class="category-overlay p-4 d-flex flex-column justify-content-end">
                            <h3 class="fw-bold mb-1">เสื้อผ้าผู้ชาย</h3>
                            <p class="small text-white-50 mb-2">เสื้อเชิ้ต, เสื้อยืด, กางเกง และสตรีทแวร์</p>
                            <span class="btn btn-sm btn-light rounded-pill align-self-start px-3">เลือกซื้อ <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="category-card position-relative overflow-hidden rounded-4 text-white shadow-sm" onclick="filterByCategory('outerwear')">
                        <img src="https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=800&q=80" alt="Jackets & Outerwear" class="w-100 category-img">
                        <div class="category-overlay p-4 d-flex flex-column justify-content-end">
                            <h3 class="fw-bold mb-1">แจ็คเก็ต & เสื้อคลุม</h3>
                            <p class="small text-white-50 mb-2">เสื้อกันหนาว, เสื้อฮู้ดดี้ และเบลเซอร์</p>
                            <span class="btn btn-sm btn-light rounded-pill align-self-start px-3">เลือกซื้อ <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Shop Section -->
    <section id="shop" class="py-5 bg-light">
        <div class="container">
            <div class="text-center max-w-2xl mx-auto mb-5">
                <span class="text-primary fw-semibold text-uppercase small tracking-wider">Catalog</span>
                <h2 class="fw-bold">สินค้าทั้งหมดของเรา</h2>
                <p class="text-muted">ค้นหาชุดที่ใช่สำหรับคุณด้วยตัวกรองสินค้าที่ง่ายดายและสะดวกสบาย</p>
            </div>

            <!-- Filter Toolbar & Tabs -->
            <div class="bg-white p-3 p-md-4 rounded-4 shadow-sm mb-4">
                <div class="row g-3 align-items-center">
                    <!-- Category Pills -->
                    <div class="col-lg-7">
                        <div class="d-flex gap-2 overflow-x-auto pb-2 pb-lg-0 no-scrollbar" id="categoryPills">
                            <button class="btn btn-dark rounded-pill px-3 py-2 btn-category active" data-category="all">ทั้งหมด</button>
                            <button class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-category" data-category="women">เสื้อผ้าผู้หญิง</button>
                            <button class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-category" data-category="men">เสื้อผ้าผู้ชาย</button>
                            <button class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-category" data-category="outerwear">แจ็คเก็ต/ฮู้ดดี้</button>
                            <button class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-category" data-category="pants">กางเกง</button>
                            <button class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-category" data-category="accessories">เครื่องประดับ</button>
                        </div>
                    </div>

                    <!-- Search Input & Sort Selector -->
                    <div class="col-lg-5">
                        <div class="d-flex gap-2">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="ค้นชื่อสินค้า...">
                            </div>

                            <select id="sortSelect" class="form-select w-auto">
                                <option value="featured">สินค้าแนะนำ</option>
                                <option value="newest">ใหม่ล่าสุด</option>
                                <option value="price-low">ราคา: ต่ำ ➔ สูง</option>
                                <option value="price-high">ราคา: สูง ➔ ต่ำ</option>
                                <option value="rating">คะแนนรีวิวสูงสุด</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Filters Summary -->
            <div id="activeFiltersContainer" class="mb-3 d-none align-items-center gap-2 flex-wrap">
                <span class="small text-muted">ตัวกรองที่เลือก:</span>
                <span id="activeCategoryBadge" class="badge bg-secondary rounded-pill"></span>
                <span id="activeSearchBadge" class="badge bg-secondary rounded-pill"></span>
                <button class="btn btn-link btn-sm text-danger p-0 ms-2 text-decoration-none" id="btnClearFilters"><i class="bi bi-x-circle"></i> ล้างตัวกรองทั้งหมด</button>
            </div>

            <!-- Product Grid -->
            <div class="row g-4" id="productGrid">
                <!-- Dynamic products rendered by main.js -->
            </div>

            <!-- Empty State -->
            <div id="noProductsFound" class="text-center py-5 d-none">
                <i class="bi bi-search-heart display-1 text-muted opacity-50"></i>
                <h4 class="mt-3 fw-bold">ไม่พบสินค้าที่คุณค้นหา</h4>
                <p class="text-muted">ลองค้นหาด้วยคำอื่น หรือรีเซ็ตตัวกรองหมวดหมู่</p>
                <button class="btn btn-outline-dark rounded-pill px-4 mt-2" onclick="resetFilters()">ดูสินค้าทั้งหมด</button>
            </div>
        </div>
    </section>

    <!-- Banner / Promotion Section -->
    <section id="promotions" class="py-5 bg-dark text-white position-relative overflow-hidden">
        <div class="container position-relative z-1 py-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill mb-3">EXCLUSIVE OFFER</span>
                    <h2 class="display-5 fw-bold mb-3">รับส่วนลดพิเศษ 20% สำหรับคำสั่งซื้อแรก</h2>
                    <p class="text-white-50 mb-4 fs-5">ใช้โค้ด <mark class="bg-warning text-dark px-2 rounded fw-bold">SUMMER20</mark> ในขั้นตอนการชำระเงินเมื่อสั่งซื้อครบ 500 บาทขึ้นไป</p>
                    <div class="d-flex align-items-center gap-3">
                        <div class="input-group max-w-md">
                            <input type="text" class="form-control form-control-lg bg-white bg-opacity-10 text-white border-secondary" value="SUMMER20" readonly id="promoCodeInput">
                            <button class="btn btn-warning btn-lg fw-bold px-4" id="btnCopyPromo"><i class="bi bi-copy me-1"></i> คัดลอกโค้ด</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section id="about" class="py-5">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-3">
                    <div class="p-4 rounded-4 bg-light h-100">
                        <i class="bi bi-truck display-4 text-primary mb-3"></i>
                        <h5 class="fw-bold">จัดส่งรวดเร็ว</h5>
                        <p class="small text-muted mb-0">ส่งถึงหน้าบ้านภายใน 1-3 วันทำการทั่วประเทศ</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 rounded-4 bg-light h-100">
                        <i class="bi bi-shield-check display-4 text-primary mb-3"></i>
                        <h5 class="fw-bold">การันตีคุณภาพ</h5>
                        <p class="small text-muted mb-0">สินค้าคุณภาพสูง ตัดเย็บประณีต คัดสรรอย่างดี</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 rounded-4 bg-light h-100">
                        <i class="bi bi-arrow-counterclockwise display-4 text-primary mb-3"></i>
                        <h5 class="fw-bold">เปลี่ยนสินค้าได้ใน 7 วัน</h5>
                        <p class="small text-muted mb-0">ไซส์ไม่พอดีสามารถเปลี่ยนสินค้าได้อย่างสะดวกสบาย</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 rounded-4 bg-light h-100">
                        <i class="bi bi-headset display-4 text-primary mb-3"></i>
                        <h5 class="fw-bold">บริการลูกค้า 24/7</h5>
                        <p class="small text-muted mb-0">ทีมงานพร้อมให้คำแนะนำช่วยเหลือตลอดเวลา</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white-50 pt-5 pb-4 border-top border-secondary">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-4">
                    <a class="d-flex align-items-center gap-2 fw-bold fs-3 text-white text-decoration-none mb-3" href="#">
                        <span class="bg-white text-dark px-2 py-1 rounded-3 fs-4">C</span> CLOTHE<span class="text-primary">.</span>
                    </a>
                    <p class="small">ร้านขายเสื้อผ้าแฟชั่นออนไลน์ชั้นนำ รวมสตรีทแวร์ มินิมอล และแคชชวล ลุคที่ออกแบบมาเพื่อสไตล์ที่เป็นคุณ</p>
                    <div class="d-flex gap-3 fs-5 text-white">
                        <a href="#" class="text-white-50 hover-white"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white-50 hover-white"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white-50 hover-white"><i class="bi bi-line"></i></a>
                        <a href="#" class="text-white-50 hover-white"><i class="bi bi-tiktok"></i></a>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="text-white fw-bold mb-3">หมวดหมู่</h6>
                    <ul class="list-unstyled small d-grid gap-2">
                        <li><a href="#shop" onclick="filterByCategory('women')" class="text-white-50 text-decoration-none">เสื้อผ้าผู้หญิง</a></li>
                        <li><a href="#shop" onclick="filterByCategory('men')" class="text-white-50 text-decoration-none">เสื้อผ้าผู้ชาย</a></li>
                        <li><a href="#shop" onclick="filterByCategory('outerwear')" class="text-white-50 text-decoration-none">แจ็คเก็ต & ฮู้ดดี้</a></li>
                        <li><a href="#shop" onclick="filterByCategory('pants')" class="text-white-50 text-decoration-none">กางเกงแฟชั่น</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="text-white fw-bold mb-3">ช่วยเหลือ</h6>
                    <ul class="list-unstyled small d-grid gap-2">
                        <li><a href="#" class="text-white-50 text-decoration-none">ตารางขนาดไซส์ (Size Chart)</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">สถานะการจัดส่ง</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">การคืนสินค้า & คืนเงิน</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">คำถามที่พบบ่อย (FAQ)</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="text-white fw-bold mb-3">รับข่าวสารและสิทธิพิเศษ</h6>
                    <p class="small">ลงทะเบียนรับข่าวสารเพื่อไม่พลาดคอลเลกชันใหม่และส่วนลดพิเศษ</p>
                    <form onsubmit="event.preventDefault(); alert('ขอบคุณสำหรับการติดตาม!');">
                        <div class="input-group">
                            <input type="email" class="form-control border-0" placeholder="อีเมลของคุณ..." required>
                            <button class="btn btn-primary fw-semibold" type="submit">ติดตาม</button>
                        </div>
                    </form>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small">
                <p class="mb-0">&copy; 2025 CLOTHE Store. All rights reserved. Designed for Modern E-Commerce.</p>
                <div class="d-flex gap-3 mt-2 mt-md-0">
                    <span><i class="bi bi-credit-card-2-front"></i> รองรับ PromptPay / บัตรเครดิต / เก็บเงินปลายทาง</span>
                </div>
            </div>
        </div>
    </footer>


    <!-- Slide-over Cart Offcanvas Drawer -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel" style="width: 420px; max-width: 100vw;">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold d-flex align-items-center gap-2" id="cartOffcanvasLabel">
                <i class="bi bi-bag-check"></i> ตะกร้าสินค้าของคุณ (<span id="cartDrawerCount">0</span>)
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column p-0">
            <!-- Cart Items Container -->
            <div class="flex-grow-1 overflow-y-auto p-3" id="cartItemsList">
                <!-- Cart items dynamically rendered -->
            </div>

            <!-- Cart Summary Footer -->
            <div class="border-top p-3 bg-light" id="cartSummarySection">
                <!-- Coupon Input -->
                <div class="mb-3">
                    <div class="input-group input-group-sm">
                        <input type="text" id="couponInput" class="form-control text-uppercase" placeholder="ใส่โค้ดส่วนลด (เช่น SUMMER20)">
                        <button class="btn btn-outline-dark fw-semibold" id="btnApplyCoupon">ใช้โค้ด</button>
                    </div>
                    <div id="couponMessage" class="form-text small mt-1"></div>
                </div>

                <div class="d-flex justify-content-between mb-1 small text-muted">
                    <span>ราคาสินค้ารวม:</span>
                    <span id="cartSubtotal">฿0</span>
                </div>
                <div class="d-flex justify-content-between mb-1 small text-muted">
                    <span>ส่วนลด:</span>
                    <span id="cartDiscount" class="text-success">-฿0</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small text-muted">
                    <span>ค่าจัดส่ง:</span>
                    <span id="cartShipping">฿0</span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between mb-3 fs-5 fw-bold text-dark">
                    <span>ยอดชำระสุทธิ:</span>
                    <span id="cartTotal" class="text-primary">฿0</span>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-dark btn-lg rounded-pill fw-bold" id="btnGoToCheckout" data-bs-dismiss="offcanvas" data-bs-toggle="modal" data-bs-target="#checkoutModal">
                        ดำเนินการสั่งซื้อ <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                    <button class="btn btn-link text-danger text-decoration-none btn-sm" id="btnClearCart">
                        <i class="bi bi-trash"></i> ล้างตะกร้าสินค้า
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Wishlist Offcanvas Drawer -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="wishlistOffcanvas" aria-labelledby="wishlistOffcanvasLabel" style="width: 380px; max-width: 100vw;">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold d-flex align-items-center gap-2" id="wishlistOffcanvasLabel">
                <i class="bi bi-heart-fill text-danger"></i> รายการที่ชอบ (<span id="wishlistDrawerCount">0</span>)
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-3 overflow-y-auto" id="wishlistItemsList">
            <!-- Wishlist items dynamically rendered -->
        </div>
    </div>

    <!-- Product Quick View Modal -->
    <div class="modal fade" id="quickViewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 overflow-hidden">
                <div class="modal-body p-0 position-relative">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3 z-3 bg-white p-2 rounded-circle shadow-sm" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="row g-0" id="quickViewContent">
                        <!-- Quick View dynamically populated -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-bottom">
                    <h5 class="modal-header-title fw-bold m-0"><i class="bi bi-credit-card me-2"></i> ชำระเงินและระบุที่อยู่จัดส่ง</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="checkoutForm">
                        <div class="row g-3">
                            <div class="col-12">
                                <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="bi bi-person-lines-fill me-1"></i> ข้อมูลผู้ซื้อ & ที่อยู่จัดส่ง</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="custName" required placeholder="สมชาย ใจดี">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="custPhone" required placeholder="0812345678">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">อีเมล <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="custEmail" required placeholder="somchai@example.com">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">ที่อยู่สำหรับจัดส่ง <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="custAddress" rows="2" required placeholder="บ้านเลขที่, ถนน, แขวง/ตำบล, เขต/อำเภอ, จังหวัด, รหัสไปรษณีย์"></textarea>
                            </div>

                            <div class="col-12 mt-4">
                                <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="bi bi-wallet2 me-1"></i> วิธีการชำระเงิน</h6>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <div class="form-check payment-option-card border rounded-3 p-3 cursor-pointer">
                                            <input class="form-check-input" type="radio" name="paymentMethod" id="payQR" value="qr" checked>
                                            <label class="form-check-input-label fw-semibold ms-2" for="payQR">
                                                <i class="bi bi-qr-code-scan d-block fs-3 text-primary mb-1"></i>
                                                PromptPay QR Code
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check payment-option-card border rounded-3 p-3 cursor-pointer">
                                            <input class="form-check-input" type="radio" name="paymentMethod" id="payCard" value="card">
                                            <label class="form-check-input-label fw-semibold ms-2" for="payCard">
                                                <i class="bi bi-credit-card-2-back d-block fs-3 text-success mb-1"></i>
                                                บัตรเดบิต / เครดิต
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check payment-option-card border rounded-3 p-3 cursor-pointer">
                                            <input class="form-check-input" type="radio" name="paymentMethod" id="payCOD" value="cod">
                                            <label class="form-check-input-label fw-semibold ms-2" for="payCOD">
                                                <i class="bi bi-cash-stack d-block fs-3 text-warning mb-1"></i>
                                                เก็บเงินปลายทาง (COD)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- QR Code Payment Info Display -->
                            <div class="col-12 text-center p-3 bg-light rounded-3 mt-3" id="qrPaymentBox">
                                <p class="small text-muted mb-2 fw-semibold">สแกน QR Code พร้อมเพย์ด้วยแอปพลิเคชันธนาคารเพื่อชำระเงิน</p>
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=PROMPTPAY-CLOTHE-STORE" alt="PromptPay QR Code" class="img-thumbnail rounded shadow-sm mb-2" style="width: 160px; height: 160px;">
                                <div class="fw-bold text-dark fs-5" id="checkoutPayAmount">฿0</div>
                                <small class="text-muted d-block">ชื่อบัญชี: บจก. โคลธ แฟชั่น (ประเทศไทย)</small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold" id="btnSubmitOrder">ยืนยันการสั่งซื้อ <i class="bi bi-check-circle ms-1"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Success Modal -->
    <div class="modal fade" id="orderSuccessModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 text-center p-4">
                <div class="modal-body">
                    <div class="bg-success-subtle text-success rounded-circle d-inline-flex p-3 mb-3">
                        <i class="bi bi-check-circle-fill display-3"></i>
                    </div>
                    <h3 class="fw-bold mb-2">สั่งซื้อสินค้าสำเร็จ!</h3>
                    <p class="text-muted mb-3">ขอบคุณสำหรับการสั่งซื้อกับ CLOTHE. เราจะจัดเตรียมสินค้าและจัดส่งโดยเร็วที่สุด</p>

                    <div class="bg-light rounded-3 p-3 text-start mb-4 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">หมายเลขคำสั่งซื้อ:</span>
                            <strong id="orderSuccessId">#ORD-000000</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">ผู้รับ:</span>
                            <span id="orderSuccessCustomer" class="fw-semibold">-</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">ยอดรวมทั้งสิ้น:</span>
                            <span id="orderSuccessAmount" class="fw-bold text-primary">-</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">สถานะ:</span>
                            <span class="badge bg-success">กำลังเตรียมจัดส่ง</span>
                        </div>
                    </div>

                    <button class="btn btn-dark btn-lg rounded-pill px-5 fw-bold w-100" data-bs-dismiss="modal">ช้อปสินค้าเพิ่มเติม</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
        <div id="liveToast" class="toast align-items-center text-white bg-dark border-0 rounded-3 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill text-success fs-5" id="toastIcon"></i>
                    <span id="toastMessage">เพิ่มสินค้าลงในตะกร้าเรียบร้อยแล้ว!</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="js/main.js"></script>
</body>
</html>
