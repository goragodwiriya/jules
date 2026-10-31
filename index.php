<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ออมสิน (Omsin) - บัญชีรายรับ-รายจ่ายส่วนตัว</title>
    <!-- Google Fonts: Prompt / Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="app-layout">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="brand-logo">
                    <i class="bi bi-piggy-bank-fill"></i>
                </div>
                <div class="brand-text">
                    <h2>ออมสิน <span>Omsin</span></h2>
                    <p>ระบบบันทึกรายรับ-รายจ่าย</p>
                </div>
            </div>

            <nav class="sidebar-menu">
                <a href="#dashboard" class="menu-item active" data-tab="dashboard">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>ภาพรวม (Dashboard)</span>
                </a>
                <a href="#transactions" class="menu-item" data-tab="transactions">
                    <i class="bi bi-journal-text"></i>
                    <span>รายการธุรกรรม</span>
                </a>
                <a href="#analytics" class="menu-item" data-tab="analytics">
                    <i class="bi bi-pie-chart-fill"></i>
                    <span>รายงาน & สถิติ</span>
                </a>
                <a href="#wallets" class="menu-item" data-tab="wallets">
                    <i class="bi bi-wallet2"></i>
                    <span>กระเป๋าเงิน & บัญชี</span>
                </a>
                <a href="#goals" class="menu-item" data-tab="goals">
                    <i class="bi bi-trophy-fill"></i>
                    <span>เป้าหมายเงินออม</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="quick-export">
                    <a href="api/index.php?action=export_csv" class="btn btn-outline-light w-100" download>
                        <i class="bi bi-cloud-arrow-down"></i> ส่งออกข้อมูล (CSV)
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Top Navigation Bar -->
            <header class="topbar">
                <div class="topbar-left">
                    <button id="mobileMenuBtn" class="mobile-toggle" aria-label="Toggle menu">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="month-selector-wrapper">
                        <i class="bi bi-calendar3"></i>
                        <input type="month" id="globalMonthPicker" class="form-control-month">
                    </div>
                </div>
                <div class="topbar-right">
                    <button class="btn btn-primary btn-add-tx" id="openAddTxModalBtn">
                        <i class="bi bi-plus-circle-fill"></i> บันทึกรายการใหม่
                    </button>
                </div>
            </header>

            <!-- Container for Tab Panes -->
            <div class="content-container">
                <!-- TAB 1: DASHBOARD -->
                <section id="pane-dashboard" class="tab-pane active">
                    <!-- Overview Metrics -->
                    <div class="metrics-grid">
                        <div class="metric-card card-balance">
                            <div class="metric-icon"><i class="bi bi-wallet-fill"></i></div>
                            <div class="metric-info">
                                <span class="metric-label">ยอดเงินคงเหลือรวมทั้งหมด</span>
                                <h3 class="metric-value" id="dashTotalBalance">฿0.00</h3>
                            </div>
                        </div>

                        <div class="metric-card card-income">
                            <div class="metric-icon"><i class="bi bi-arrow-down-left-circle-fill"></i></div>
                            <div class="metric-info">
                                <span class="metric-label">รายรับเดือนนี้</span>
                                <h3 class="metric-value text-success" id="dashMonthIncome">+฿0.00</h3>
                            </div>
                        </div>

                        <div class="metric-card card-expense">
                            <div class="metric-icon"><i class="bi bi-arrow-up-right-circle-fill"></i></div>
                            <div class="metric-info">
                                <span class="metric-label">รายจ่ายเดือนนี้</span>
                                <h3 class="metric-value text-danger" id="dashMonthExpense">-฿0.00</h3>
                            </div>
                        </div>

                        <div class="metric-card card-savings">
                            <div class="metric-icon"><i class="bi bi-piggy-bank-fill"></i></div>
                            <div class="metric-info">
                                <span class="metric-label">เงินออมสุทธิเดือนนี้</span>
                                <h3 class="metric-value text-primary" id="dashNetSavings">฿0.00</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Wallets Quick Horizontal Cards -->
                    <div class="section-title-row">
                        <h3><i class="bi bi-credit-card-2-front"></i> กระเป๋าเงินของคุณ</h3>
                        <a href="#wallets" class="btn-link" onclick="switchTab('wallets')">จัดการทั้งหมด <i class="bi bi-chevron-right"></i></a>
                    </div>
                    <div class="wallets-scroller" id="dashWalletsList">
                        <!-- Populated by JS -->
                    </div>

                    <!-- Recent Transactions & Expense Pie Chart -->
                    <div class="dashboard-split-grid">
                        <div class="content-card recent-transactions-card">
                            <div class="card-header-flex">
                                <h4><i class="bi bi-clock-history"></i> รายการล่าสุด</h4>
                                <a href="#transactions" class="btn-link" onclick="switchTab('transactions')">ดูทั้งหมด</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table" id="dashRecentTxTable">
                                    <thead>
                                        <tr>
                                            <th>วันที่</th>
                                            <th>ประเภท / หมวดหมู่</th>
                                            <th>กระเป๋าเงิน</th>
                                            <th>บันทึก</th>
                                            <th class="text-right">จำนวนเงิน</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dashRecentTxBody">
                                        <!-- JS populates -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="content-card chart-mini-card">
                            <div class="card-header-flex">
                                <h4><i class="bi bi-pie-chart"></i> สัดส่วนรายจ่ายเดือนนี้</h4>
                            </div>
                            <div class="chart-box">
                                <canvas id="dashExpenseChart"></canvas>
                            </div>
                            <div id="dashExpenseEmpty" class="empty-state-hint" style="display: none;">
                                <i class="bi bi-inbox"></i> ไม่มีรายการค่าใช้จ่ายในเดือนนี้
                            </div>
                        </div>
                    </div>
                </section>

                <!-- TAB 2: TRANSACTIONS LIST -->
                <section id="pane-transactions" class="tab-pane">
                    <div class="content-card">
                        <div class="filters-toolbar">
                            <div class="filter-group">
                                <input type="text" id="txSearchInput" class="form-control" placeholder="ค้นหาบันทึก หรือหมวดหมู่...">
                            </div>
                            <div class="filter-group">
                                <select id="txFilterType" class="form-control">
                                    <option value="">ทุกประเภทรายการ</option>
                                    <option value="expense">เฉพาะรายจ่าย</option>
                                    <option value="income">เฉพาะรายรับ</option>
                                    <option value="transfer">เฉพาะโอนเงิน</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <select id="txFilterWallet" class="form-control">
                                    <option value="">ทุกกระเป๋าเงิน</option>
                                </select>
                            </div>
                            <button id="btnResetTxFilters" class="btn btn-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i> ล้างตัวกรอง
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table" id="allTransactionsTable">
                                <thead>
                                    <tr>
                                        <th>วันที่</th>
                                        <th>ประเภท</th>
                                        <th>หมวดหมู่</th>
                                        <th>กระเป๋าเงิน</th>
                                        <th>บันทึกช่วยจำ</th>
                                        <th class="text-right">จำนวนเงิน</th>
                                        <th class="text-center" style="width: 80px;">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="allTxTableBody">
                                    <!-- JS Populates -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- TAB 3: ANALYTICS -->
                <section id="pane-analytics" class="tab-pane">
                    <div class="analytics-grid">
                        <div class="content-card">
                            <div class="card-header-flex">
                                <h4><i class="bi bi-graph-up"></i> แนวโน้มรายรับ-รายจ่ายรายวันในเดือนนี้</h4>
                            </div>
                            <div class="chart-box chart-box-large">
                                <canvas id="analyticsDailyChart"></canvas>
                            </div>
                        </div>

                        <div class="content-card">
                            <div class="card-header-flex">
                                <h4><i class="bi bi-pie-chart-fill"></i> รายจ่ายจำแนกตามหมวดหมู่</h4>
                            </div>
                            <div class="chart-box chart-box-large">
                                <canvas id="analyticsCategoryChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Category Breakdown Table -->
                    <div class="content-card mt-4">
                        <h4><i class="bi bi-card-checklist"></i> รายละเอียดค่าใช้จ่ายแต่ละหมวดหมู่</h4>
                        <div class="table-responsive">
                            <table class="table" id="categoryExpenseTable">
                                <thead>
                                    <tr>
                                        <th>หมวดหมู่</th>
                                        <th class="text-right">ยอดรวม (บาท)</th>
                                        <th class="text-right">สัดส่วน (%)</th>
                                    </tr>
                                </thead>
                                <tbody id="categoryExpenseBody">
                                    <!-- JS Populates -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- TAB 4: WALLETS -->
                <section id="pane-wallets" class="tab-pane">
                    <div class="section-title-row">
                        <h3><i class="bi bi-wallet2"></i> รายการกระเป๋าเงินและบัญชีทั้งหมด</h3>
                        <button class="btn btn-primary" id="openAddWalletModalBtn">
                            <i class="bi bi-plus-circle"></i> เพิ่มกระเป๋าเงินใหม่
                        </button>
                    </div>

                    <div class="wallets-grid-full" id="allWalletsGrid">
                        <!-- JS populates -->
                    </div>
                </section>

                <!-- TAB 5: SAVINGS GOALS -->
                <section id="pane-goals" class="tab-pane">
                    <div class="section-title-row">
                        <h3><i class="bi bi-trophy"></i> เป้าหมายเงินออม (Savings Goals)</h3>
                        <button class="btn btn-primary" id="openAddGoalModalBtn">
                            <i class="bi bi-plus-circle"></i> สร้างเป้าหมายใหม่
                        </button>
                    </div>

                    <div class="goals-grid" id="allGoalsGrid">
                        <!-- JS populates -->
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- MODAL: ADD TRANSACTION -->
    <div class="modal" id="modalAddTransaction">
        <div class="modal-backdrop"></div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4><i class="bi bi-pencil-square"></i> บันทึกรายการ</h4>
                    <button class="btn-close" data-close-modal>&times;</button>
                </div>
                <form id="formAddTransaction">
                    <div class="modal-body">
                        <!-- Type Selector (Tabs) -->
                        <div class="tx-type-tabs">
                            <label class="type-tab-btn active" data-type="expense">
                                <input type="radio" name="txType" value="expense" checked>
                                <i class="bi bi-dash-circle"></i> รายจ่าย
                            </label>
                            <label class="type-tab-btn" data-type="income">
                                <input type="radio" name="txType" value="income">
                                <i class="bi bi-plus-circle"></i> รายรับ
                            </label>
                            <label class="type-tab-btn" data-type="transfer">
                                <input type="radio" name="txType" value="transfer">
                                <i class="bi bi-arrow-left-right"></i> โอนเงิน
                            </label>
                        </div>

                        <!-- Amount -->
                        <div class="form-group">
                            <label for="txAmount">จำนวนเงิน (บาท) *</label>
                            <input type="number" step="0.01" min="0.01" class="form-control form-control-lg text-center" id="txAmount" placeholder="0.00" required autofocus>
                        </div>

                        <!-- Wallet selector -->
                        <div class="form-row">
                            <div class="form-group flex-1">
                                <label for="txWalletSelect" id="txWalletLabel">จากกระเป๋าเงิน *</label>
                                <select class="form-control" id="txWalletSelect" required>
                                    <!-- Populated -->
                                </select>
                            </div>

                            <!-- Target Wallet (for Transfer) -->
                            <div class="form-group flex-1" id="txTargetWalletGroup" style="display: none;">
                                <label for="txTargetWalletSelect">ไปยังกระเป๋าเงิน *</label>
                                <select class="form-control" id="txTargetWalletSelect">
                                    <!-- Populated -->
                                </select>
                            </div>
                        </div>

                        <!-- Category (for Income / Expense) -->
                        <div class="form-group" id="txCategoryGroup">
                            <label for="txCategorySelect">หมวดหมู่ *</label>
                            <select class="form-control" id="txCategorySelect">
                                <!-- Populated based on type -->
                            </select>
                        </div>

                        <!-- Date & Time -->
                        <div class="form-group">
                            <label for="txDate">วันที่ทำรายการ *</label>
                            <input type="date" class="form-control" id="txDate" required>
                        </div>

                        <!-- Note -->
                        <div class="form-group">
                            <label for="txNote">บันทึกช่วยจำ (Note)</label>
                            <input type="text" class="form-control" id="txNote" placeholder="รายละเอียด เช่น ค่าข้าว, เติมน้ำมัน, เบิกเงิน">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-close-modal>ยกเลิก</button>
                        <button type="submit" class="btn btn-primary" id="btnSaveTransaction">
                            <i class="bi bi-check-circle"></i> บันทึกข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: ADD WALLET -->
    <div class="modal" id="modalAddWallet">
        <div class="modal-backdrop"></div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4><i class="bi bi-wallet2"></i> เพิ่มกระเป๋าเงินใหม่</h4>
                    <button class="btn-close" data-close-modal>&times;</button>
                </div>
                <form id="formAddWallet">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="walletName">ชื่อกระเป๋าเงิน / บัญชี *</label>
                            <input type="text" class="form-control" id="walletName" placeholder="เช่น กสิกรไทย, TrueMoney, เงินเก็บ" required>
                        </div>
                        <div class="form-group">
                            <label for="walletType">ประเภทบัญชี</label>
                            <select class="form-control" id="walletType">
                                <option value="cash">เงินสด (Cash)</option>
                                <option value="bank">บัญชีธนาคาร (Bank)</option>
                                <option value="ewallet">วอลเล็ท (e-Wallet)</option>
                                <option value="credit">บัตรเครดิต (Credit Card)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="walletInitialBalance">ยอดเงินตั้งต้น (บาท)</label>
                            <input type="number" step="0.01" class="form-control" id="walletInitialBalance" placeholder="0.00" value="0">
                        </div>
                        <div class="form-group">
                            <label for="walletColor">เลือกสีประจำกระเป๋า</label>
                            <input type="color" class="form-control form-control-color" id="walletColor" value="#3B82F6">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-close-modal>ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึกกระเป๋าเงิน</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: ADD SAVINGS GOAL -->
    <div class="modal" id="modalAddGoal">
        <div class="modal-backdrop"></div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4><i class="bi bi-trophy"></i> สร้างเป้าหมายเงินออมใหม่</h4>
                    <button class="btn-close" data-close-modal>&times;</button>
                </div>
                <form id="formAddGoal">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="goalName">ชื่อเป้าหมาย *</label>
                            <input type="text" class="form-control" id="goalName" placeholder="เช่น ซื้อโน้ตบุ๊กใหม่, ท่องเที่ยวเกาหลี" required>
                        </div>
                        <div class="form-group">
                            <label for="goalTargetAmount">ยอดเงินเป้าหมาย (บาท) *</label>
                            <input type="number" step="0.01" min="1" class="form-control" id="goalTargetAmount" placeholder="10000" required>
                        </div>
                        <div class="form-group">
                            <label for="goalCurrentAmount">เงินที่เก็บได้แล้ว (ถ้ามี)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="goalCurrentAmount" placeholder="0" value="0">
                        </div>
                        <div class="form-group">
                            <label for="goalTargetDate">วันที่ต้องการบรรลุเป้าหมาย</label>
                            <input type="date" class="form-control" id="goalTargetDate">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-close-modal>ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">สร้างเป้าหมาย</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: DEPOSIT TO GOAL -->
    <div class="modal" id="modalDepositGoal">
        <div class="modal-backdrop"></div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4><i class="bi bi-piggy-bank"></i> ออมเงินเข้าเป้าหมาย</h4>
                    <button class="btn-close" data-close-modal>&times;</button>
                </div>
                <form id="formDepositGoal">
                    <input type="hidden" id="depositGoalId">
                    <div class="modal-body">
                        <p class="mb-2">เป้าหมาย: <strong id="depositGoalName"></strong></p>
                        <div class="form-group">
                            <label for="depositAmount">ยอดเงินที่ต้องการออมเพิ่ม (บาท) *</label>
                            <input type="number" step="0.01" min="1" class="form-control form-control-lg text-center" id="depositAmount" placeholder="1000" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-close-modal>ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">ยืนยันการออม</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast-container" id="toastContainer"></div>

    <script src="js/main.js"></script>
</body>
</html>
