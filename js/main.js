/**
 * Omsin - Personal Finance Application Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    // State management
    const state = {
        currentMonth: new Date().toISOString().slice(0, 7), // YYYY-MM
        wallets: [],
        categories: [],
        goals: [],
        transactions: [],
        summary: null,
        activeTab: 'dashboard'
    };

    // Chart instances
    let dashChartInstance = null;
    let analyticsDailyChartInstance = null;
    let analyticsCatChartInstance = null;

    // DOM Elements
    const globalMonthPicker = document.getElementById('globalMonthPicker');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.querySelector('.sidebar');
    const menuItems = document.querySelectorAll('.sidebar-menu .menu-item');
    const tabPanes = document.querySelectorAll('.tab-pane');

    // Modals
    const modalAddTx = document.getElementById('modalAddTransaction');
    const modalAddWallet = document.getElementById('modalAddWallet');
    const modalAddGoal = document.getElementById('modalAddGoal');
    const modalDepositGoal = document.getElementById('modalDepositGoal');

    // Forms
    const formAddTx = document.getElementById('formAddTransaction');
    const formAddWallet = document.getElementById('formAddWallet');
    const formAddGoal = document.getElementById('formAddGoal');
    const formDepositGoal = document.getElementById('formDepositGoal');

    // Set initial date picker
    globalMonthPicker.value = state.currentMonth;
    document.getElementById('txDate').value = new Date().toISOString().slice(0, 10);

    // Initial Load
    init();

    async function init() {
        setupEventListeners();
        await loadInitialData();
    }

    async function loadInitialData() {
        await Promise.all([
            loadWallets(),
            loadCategories(),
            loadGoals()
        ]);
        await refreshDashboard();
    }

    // ================= EVENT LISTENERS =================
    function setupEventListeners() {
        // Month Picker Change
        globalMonthPicker.addEventListener('change', (e) => {
            state.currentMonth = e.target.value;
            refreshDashboard();
            if (state.activeTab === 'transactions') loadTransactions();
            if (state.activeTab === 'analytics') loadAnalytics();
        });

        // Mobile Menu Toggle
        mobileMenuBtn?.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        // Navigation Tabs
        menuItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const tab = item.getAttribute('data-tab');
                switchTab(tab);
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('open');
                }
            });
        });

        // Modal Open Buttons
        document.getElementById('openAddTxModalBtn')?.addEventListener('click', () => {
            openModal(modalAddTx);
        });

        document.getElementById('openAddWalletModalBtn')?.addEventListener('click', () => {
            openModal(modalAddWallet);
        });

        document.getElementById('openAddGoalModalBtn')?.addEventListener('click', () => {
            openModal(modalAddGoal);
        });

        // Close Modals
        document.querySelectorAll('[data-close-modal]').forEach(btn => {
            btn.addEventListener('click', () => {
                closeAllModals();
            });
        });

        document.querySelectorAll('.modal-backdrop').forEach(bd => {
            bd.addEventListener('click', () => {
                closeAllModals();
            });
        });

        // Form Submit: Add Transaction
        formAddTx?.addEventListener('submit', handleAddTransaction);

        // Form Submit: Add Wallet
        formAddWallet?.addEventListener('submit', handleAddWallet);

        // Form Submit: Add Goal
        formAddGoal?.addEventListener('submit', handleAddGoal);

        // Form Submit: Deposit Goal
        formDepositGoal?.addEventListener('submit', handleDepositGoal);

        // Type Tabs in Add Transaction modal
        const typeTabBtns = document.querySelectorAll('.tx-type-tabs .type-tab-btn');
        typeTabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                typeTabBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const radio = btn.querySelector('input');
                radio.checked = true;
                updateTxFormFields(radio.value);
            });
        });

        // Transaction list filters
        document.getElementById('txSearchInput')?.addEventListener('input', debounce(loadTransactions, 300));
        document.getElementById('txFilterType')?.addEventListener('change', loadTransactions);
        document.getElementById('txFilterWallet')?.addEventListener('change', loadTransactions);
        document.getElementById('btnResetTxFilters')?.addEventListener('click', () => {
            document.getElementById('txSearchInput').value = '';
            document.getElementById('txFilterType').value = '';
            document.getElementById('txFilterWallet').value = '';
            loadTransactions();
        });
    }

    // Tab Switching
    window.switchTab = function(tabName) {
        state.activeTab = tabName;
        menuItems.forEach(item => {
            item.classList.toggle('active', item.getAttribute('data-tab') === tabName);
        });
        tabPanes.forEach(pane => {
            pane.classList.toggle('active', pane.id === `pane-${tabName}`);
        });

        if (tabName === 'dashboard') refreshDashboard();
        if (tabName === 'transactions') loadTransactions();
        if (tabName === 'analytics') loadAnalytics();
        if (tabName === 'wallets') renderWalletsManagement();
        if (tabName === 'goals') renderGoals();
    };

    // Modal Helpers
    function openModal(modal) {
        if (!modal) return;
        modal.classList.add('active');
    }

    function closeAllModals() {
        document.querySelectorAll('.modal').forEach(m => m.classList.remove('active'));
    }

    // Toggle fields based on Tx Type (Expense, Income, Transfer)
    function updateTxFormFields(type) {
        const catGroup = document.getElementById('txCategoryGroup');
        const targetWalletGroup = document.getElementById('txTargetWalletGroup');
        const walletLabel = document.getElementById('txWalletLabel');

        if (type === 'transfer') {
            catGroup.style.display = 'none';
            targetWalletGroup.style.display = 'block';
            walletLabel.innerText = 'จากกระเป๋าเงินต้นทาง *';
        } else {
            catGroup.style.display = 'block';
            targetWalletGroup.style.display = 'none';
            walletLabel.innerText = 'กระเป๋าเงิน / บัญชี *';
            populateCategorySelect(type);
        }
    }

    // Populate Category dropdown based on type
    function populateCategorySelect(type) {
        const select = document.getElementById('txCategorySelect');
        if (!select) return;
        select.innerHTML = '';
        const filtered = state.categories.filter(c => c.type === type);
        filtered.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = c.name;
            select.appendChild(opt);
        });
    }

    // Populate Wallet dropdowns
    function populateWalletDropdowns() {
        const txWalletSelect = document.getElementById('txWalletSelect');
        const txTargetWalletSelect = document.getElementById('txTargetWalletSelect');
        const filterWallet = document.getElementById('txFilterWallet');

        if (txWalletSelect) txWalletSelect.innerHTML = '';
        if (txTargetWalletSelect) txTargetWalletSelect.innerHTML = '';
        if (filterWallet) {
            filterWallet.innerHTML = '<option value="">ทุกกระเป๋าเงิน</option>';
        }

        state.wallets.forEach(w => {
            const opt1 = new Option(`${w.name} (฿${formatNumber(w.current_balance)})`, w.id);
            const opt2 = new Option(w.name, w.id);
            const opt3 = new Option(w.name, w.id);

            txWalletSelect?.appendChild(opt1);
            txTargetWalletSelect?.appendChild(opt2);
            filterWallet?.appendChild(opt3);
        });
    }

    // ================= API CALLS =================
    async function loadWallets() {
        try {
            const res = await fetch('api/index.php?action=wallets');
            const data = await res.json();
            if (data.status === 'success') {
                state.wallets = data.data;
                populateWalletDropdowns();
                renderDashboardWallets();
            }
        } catch (err) {
            console.error('Error loading wallets:', err);
        }
    }

    async function loadCategories() {
        try {
            const res = await fetch('api/index.php?action=categories');
            const data = await res.json();
            if (data.status === 'success') {
                state.categories = data.data;
                populateCategorySelect('expense'); // Default expense
            }
        } catch (err) {
            console.error('Error loading categories:', err);
        }
    }

    async function loadGoals() {
        try {
            const res = await fetch('api/index.php?action=goals');
            const data = await res.json();
            if (data.status === 'success') {
                state.goals = data.data;
                renderGoals();
            }
        } catch (err) {
            console.error('Error loading goals:', err);
        }
    }

    async function refreshDashboard() {
        await Promise.all([
            loadSummary(),
            loadRecentTransactions(),
            loadWallets()
        ]);
    }

    async function loadSummary() {
        try {
            const res = await fetch(`api/index.php?action=summary&month=${state.currentMonth}`);
            const data = await res.json();
            if (data.status === 'success') {
                state.summary = data.data;
                renderSummaryMetrics();
                renderDashboardChart();
            }
        } catch (err) {
            console.error('Error loading summary:', err);
        }
    }

    async function loadRecentTransactions() {
        try {
            const res = await fetch(`api/index.php?action=transactions&limit=6&month=${state.currentMonth}`);
            const data = await res.json();
            if (data.status === 'success') {
                renderRecentTable(data.data);
            }
        } catch (err) {
            console.error('Error loading recent tx:', err);
        }
    }

    async function loadTransactions() {
        const type = document.getElementById('txFilterType')?.value || '';
        const walletId = document.getElementById('txFilterWallet')?.value || '';
        const search = document.getElementById('txSearchInput')?.value || '';

        try {
            const url = `api/index.php?action=transactions&month=${state.currentMonth}&type=${type}&wallet_id=${walletId}&search=${encodeURIComponent(search)}`;
            const res = await fetch(url);
            const data = await res.json();
            if (data.status === 'success') {
                state.transactions = data.data;
                renderAllTransactionsTable(data.data);
            }
        } catch (err) {
            console.error('Error loading transactions:', err);
        }
    }

    async function loadAnalytics() {
        await loadSummary();
        renderAnalyticsCharts();
    }

    // ================= RENDER FUNCTIONS =================
    function renderSummaryMetrics() {
        if (!state.summary) return;
        const { total_balance, month_income, month_expense, net_savings } = state.summary;

        document.getElementById('dashTotalBalance').innerText = `฿${formatNumber(total_balance)}`;
        document.getElementById('dashMonthIncome').innerText = `+฿${formatNumber(month_income)}`;
        document.getElementById('dashMonthExpense').innerText = `-฿${formatNumber(month_expense)}`;

        const netEl = document.getElementById('dashNetSavings');
        netEl.innerText = `${net_savings >= 0 ? '+' : ''}฿${formatNumber(net_savings)}`;
        netEl.className = `metric-value ${net_savings >= 0 ? 'text-primary' : 'text-danger'}`;
    }

    function renderDashboardWallets() {
        const container = document.getElementById('dashWalletsList');
        if (!container) return;
        container.innerHTML = '';

        state.wallets.forEach(w => {
            const card = document.createElement('div');
            card.className = 'wallet-badge-card';
            card.style.setProperty('--wallet-color', w.color || '#4F46E5');
            card.innerHTML = `
                <div class="wallet-badge-icon" style="background: ${w.color}15; color: ${w.color};">
                    <i class="bi ${w.icon || 'bi-wallet2'}"></i>
                </div>
                <div class="wallet-badge-info">
                    <h5>${escapeHtml(w.name)}</h5>
                    <p>฿${formatNumber(w.current_balance)}</p>
                </div>
            `;
            container.appendChild(card);
        });
    }

    function renderRecentTable(transactions) {
        const tbody = document.getElementById('dashRecentTxBody');
        if (!tbody) return;
        tbody.innerHTML = '';

        if (transactions.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted" style="padding: 24px;">ไม่มีรายการในเดือนนี้</td></tr>`;
            return;
        }

        transactions.forEach(t => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${formatThaiDate(t.transaction_date)}</td>
                <td>${renderTxTypeAndCat(t)}</td>
                <td><i class="bi bi-wallet2 text-muted"></i> ${escapeHtml(t.wallet_name || '-')}</td>
                <td>${escapeHtml(t.note || '-')}</td>
                <td class="text-right font-weight-bold ${getTxAmountColorClass(t.type)}">
                    ${getTxAmountPrefix(t.type)}฿${formatNumber(t.amount)}
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function renderAllTransactionsTable(transactions) {
        const tbody = document.getElementById('allTxTableBody');
        if (!tbody) return;
        tbody.innerHTML = '';

        if (transactions.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted" style="padding: 30px;">ไม่พบรายการที่ค้นหา</td></tr>`;
            return;
        }

        transactions.forEach(t => {
            const tr = document.createElement('tr');
            let walletDisplay = escapeHtml(t.wallet_name || '-');
            if (t.type === 'transfer' && t.target_wallet_name) {
                walletDisplay = `${escapeHtml(t.wallet_name)} <i class="bi bi-arrow-right"></i> ${escapeHtml(t.target_wallet_name)}`;
            }

            tr.innerHTML = `
                <td>${formatThaiDate(t.transaction_date)}</td>
                <td>${renderTypeBadge(t.type)}</td>
                <td>${escapeHtml(t.category_name || (t.type === 'transfer' ? 'โอนเงิน' : '-'))}</td>
                <td>${walletDisplay}</td>
                <td>${escapeHtml(t.note || '-')}</td>
                <td class="text-right ${getTxAmountColorClass(t.type)}" style="font-weight: 600;">
                    ${getTxAmountPrefix(t.type)}฿${formatNumber(t.amount)}
                </td>
                <td class="text-center">
                    <button class="btn-icon-danger" onclick="deleteTransaction(${t.id})" title="ลบรายการ">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function renderWalletsManagement() {
        const container = document.getElementById('allWalletsGrid');
        if (!container) return;
        container.innerHTML = '';

        state.wallets.forEach(w => {
            const card = document.createElement('div');
            card.className = 'wallet-card-detailed';
            card.style.setProperty('--card-color', w.color || '#3B82F6');
            card.innerHTML = `
                <div class="wallet-card-header">
                    <div class="wallet-card-title">
                        <h4>${escapeHtml(w.name)}</h4>
                        <span>ประเภท: ${escapeHtml(w.type.toUpperCase())}</span>
                    </div>
                    <div class="wallet-badge-icon" style="background: ${w.color}20; color: ${w.color};">
                        <i class="bi ${w.icon || 'bi-wallet2'}"></i>
                    </div>
                </div>
                <div class="wallet-card-body">
                    <span class="balance-label">ยอดเงินคงเหลือปัจจุบัน</span>
                    <div class="balance-num">฿${formatNumber(w.current_balance)}</div>
                    <div class="text-muted" style="font-size: 12px;">ยอดตั้งต้น: ฿${formatNumber(w.initial_balance)}</div>
                </div>
                <div class="wallet-card-footer">
                    <button class="btn btn-sm btn-secondary" onclick="deleteWallet(${w.id})">
                        <i class="bi bi-trash"></i> ลบกระเป๋าเงิน
                    </button>
                </div>
            `;
            container.appendChild(card);
        });
    }

    function renderGoals() {
        const container = document.getElementById('allGoalsGrid');
        if (!container) return;
        container.innerHTML = '';

        if (state.goals.length === 0) {
            container.innerHTML = `<div class="empty-state-hint" style="grid-column: 1/-1;"><i class="bi bi-trophy"></i> ยังไม่มีเป้าหมายเงินออม คลิก "สร้างเป้าหมายใหม่" เพื่อเริ่มต้น</div>`;
            return;
        }

        state.goals.forEach(g => {
            const percent = Math.min(100, Math.round((g.current_amount / g.target_amount) * 100));
            const card = document.createElement('div');
            card.className = 'goal-card';
            card.innerHTML = `
                <div class="goal-card-header">
                    <div class="goal-icon-badge">
                        <i class="bi ${g.icon || 'bi-piggy-bank'}"></i>
                    </div>
                    <button class="btn-icon-danger" onclick="deleteGoal(${g.id})" title="ลบเป้าหมาย">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <h4 class="goal-name">${escapeHtml(g.name)}</h4>
                <div class="goal-progress-wrap">
                    <div class="goal-progress-bar">
                        <div class="goal-progress-fill" style="width: ${percent}%;"></div>
                    </div>
                    <div class="goal-amounts-row">
                        <span>เก็บได้ <strong>฿${formatNumber(g.current_amount)}</strong></span>
                        <span>เป้าหมาย <strong>฿${formatNumber(g.target_amount)}</strong></span>
                    </div>
                </div>
                <div class="text-muted mb-2" style="font-size: 13px;">
                    ความคืบหน้า: <strong class="text-success">${percent}%</strong>
                    ${g.target_date ? ` • สิ้นสุด: ${formatThaiDate(g.target_date)}` : ''}
                </div>
                <div class="goal-footer-actions">
                    <button class="btn btn-sm btn-primary w-100" onclick="openDepositModal(${g.id}, '${escapeHtml(g.name)}')">
                        <i class="bi bi-plus-circle"></i> ออมเงินเพิ่ม
                    </button>
                </div>
            `;
            container.appendChild(card);
        });
    }

    // ================= CHARTS =================
    function renderDashboardChart() {
        const ctx = document.getElementById('dashExpenseChart')?.getContext('2d');
        const emptyHint = document.getElementById('dashExpenseEmpty');
        if (!ctx || !state.summary) return;

        const categories = state.summary.expense_by_category || [];

        if (categories.length === 0) {
            if (dashChartInstance) dashChartInstance.destroy();
            emptyHint.style.display = 'block';
            return;
        }

        emptyHint.style.display = 'none';

        const labels = categories.map(c => c.name);
        const data = categories.map(c => c.total_amount);
        const colors = categories.map(c => c.color || '#4F46E5');

        if (dashChartInstance) dashChartInstance.destroy();

        dashChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { family: 'Prompt' } }
                    }
                },
                cutout: '65%'
            }
        });
    }

    function renderAnalyticsCharts() {
        const dailyCtx = document.getElementById('analyticsDailyChart')?.getContext('2d');
        const catCtx = document.getElementById('analyticsCategoryChart')?.getContext('2d');
        const catTableBody = document.getElementById('categoryExpenseBody');

        if (!state.summary) return;

        // 1. Daily Bar Chart
        if (dailyCtx) {
            const daily = state.summary.daily_flow || [];
            const labels = daily.map(d => d.transaction_date.slice(8) + ' ' + formatThaiMonthShort(d.transaction_date.slice(5, 7)));
            const incomeData = daily.map(d => d.daily_income);
            const expenseData = daily.map(d => d.daily_expense);

            if (analyticsDailyChartInstance) analyticsDailyChartInstance.destroy();

            analyticsDailyChartInstance = new Chart(dailyCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'รายรับ',
                            data: incomeData,
                            backgroundColor: '#10B981',
                            borderRadius: 4
                        },
                        {
                            label: 'รายจ่าย',
                            data: expenseData,
                            backgroundColor: '#EF4444',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { font: { family: 'Prompt' } } }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // 2. Category Expense Chart & Table
        const categories = state.summary.expense_by_category || [];
        const totalExpense = state.summary.month_expense || 1;

        if (catCtx) {
            if (analyticsCatChartInstance) analyticsCatChartInstance.destroy();

            analyticsCatChartInstance = new Chart(catCtx, {
                type: 'pie',
                data: {
                    labels: categories.map(c => c.name),
                    datasets: [{
                        data: categories.map(c => c.total_amount),
                        backgroundColor: categories.map(c => c.color || '#4F46E5')
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right', labels: { font: { family: 'Prompt' } } }
                    }
                }
            });
        }

        // Table
        if (catTableBody) {
            catTableBody.innerHTML = '';
            if (categories.length === 0) {
                catTableBody.innerHTML = `<tr><td colspan="3" class="text-center text-muted" style="padding: 20px;">ไม่มีค่าใช้จ่ายในเดือนนี้</td></tr>`;
            } else {
                categories.forEach(c => {
                    const pct = ((c.total_amount / totalExpense) * 100).toFixed(1);
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>
                            <div class="category-pill">
                                <span class="category-dot" style="background: ${c.color};"></span>
                                <i class="bi ${c.icon || 'bi-tag'}"></i>
                                ${escapeHtml(c.name)}
                            </div>
                        </td>
                        <td class="text-right">฿${formatNumber(c.total_amount)}</td>
                        <td class="text-right"><strong>${pct}%</strong></td>
                    `;
                    catTableBody.appendChild(tr);
                });
            }
        }
    }

    // ================= ACTIONS & HANDLERS =================
    async function handleAddTransaction(e) {
        e.preventDefault();
        const type = document.querySelector('input[name="txType"]:checked').value;
        const amount = parseFloat(document.getElementById('txAmount').value);
        const walletId = document.getElementById('txWalletSelect').value;
        const targetWalletId = document.getElementById('txTargetWalletSelect').value;
        const categoryId = document.getElementById('txCategorySelect').value;
        const date = document.getElementById('txDate').value;
        const note = document.getElementById('txNote').value;

        const payload = {
            type,
            amount,
            wallet_id: walletId,
            target_wallet_id: type === 'transfer' ? targetWalletId : null,
            category_id: type !== 'transfer' ? categoryId : null,
            transaction_date: date,
            note
        };

        try {
            const res = await fetch('api/index.php?action=transactions', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.status === 'success') {
                showToast('บันทึกรายการสำเร็จ', 'success');
                formAddTx.reset();
                document.getElementById('txDate').value = new Date().toISOString().slice(0, 10);
                closeAllModals();
                await refreshDashboard();
                if (state.activeTab === 'transactions') loadTransactions();
            } else {
                showToast(data.message || 'เกิดข้อผิดพลาด', 'error');
            }
        } catch (err) {
            showToast('เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์', 'error');
        }
    }

    async function handleAddWallet(e) {
        e.preventDefault();
        const name = document.getElementById('walletName').value;
        const type = document.getElementById('walletType').value;
        const initial_balance = parseFloat(document.getElementById('walletInitialBalance').value) || 0;
        const color = document.getElementById('walletColor').value;

        try {
            const res = await fetch('api/index.php?action=wallets', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, type, initial_balance, color })
            });
            const data = await res.json();
            if (data.status === 'success') {
                showToast('เพิ่มกระเป๋าเงินสำเร็จ', 'success');
                formAddWallet.reset();
                closeAllModals();
                await loadWallets();
                if (state.activeTab === 'wallets') renderWalletsManagement();
            } else {
                showToast(data.message || 'ไม่สามารถเพิ่มได้', 'error');
            }
        } catch (err) {
            showToast('เกิดข้อผิดพลาด', 'error');
        }
    }

    async function handleAddGoal(e) {
        e.preventDefault();
        const name = document.getElementById('goalName').value;
        const target_amount = parseFloat(document.getElementById('goalTargetAmount').value);
        const current_amount = parseFloat(document.getElementById('goalCurrentAmount').value) || 0;
        const target_date = document.getElementById('goalTargetDate').value || null;

        try {
            const res = await fetch('api/index.php?action=goals', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, target_amount, current_amount, target_date })
            });
            const data = await res.json();
            if (data.status === 'success') {
                showToast('สร้างเป้าหมายเงินออมสำเร็จ', 'success');
                formAddGoal.reset();
                closeAllModals();
                await loadGoals();
            } else {
                showToast(data.message || 'เกิดข้อผิดพลาด', 'error');
            }
        } catch (err) {
            showToast('เกิดข้อผิดพลาด', 'error');
        }
    }

    async function handleDepositGoal(e) {
        e.preventDefault();
        const id = document.getElementById('depositGoalId').value;
        const deposit_amount = parseFloat(document.getElementById('depositAmount').value);

        try {
            const res = await fetch('api/index.php?action=goal_update_deposit', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, deposit_amount })
            });
            const data = await res.json();
            if (data.status === 'success') {
                showToast('ออมเงินเข้าเป้าหมายสำเร็จ', 'success');
                formDepositGoal.reset();
                closeAllModals();
                await loadGoals();
            } else {
                showToast(data.message || 'เกิดข้อผิดพลาด', 'error');
            }
        } catch (err) {
            showToast('เกิดข้อผิดพลาด', 'error');
        }
    }

    window.openDepositModal = function(id, name) {
        document.getElementById('depositGoalId').value = id;
        document.getElementById('depositGoalName').innerText = name;
        openModal(modalDepositGoal);
    };

    window.deleteTransaction = async function(id) {
        if (!confirm('คุณแน่ใจว่าต้องการลบรายการนี้ใช่หรือไม่?')) return;
        try {
            const res = await fetch(`api/index.php?action=transaction_delete&id=${id}`, { method: 'POST' });
            const data = await res.json();
            if (data.status === 'success') {
                showToast('ลบรายการเรียบร้อย', 'success');
                await refreshDashboard();
                if (state.activeTab === 'transactions') loadTransactions();
            }
        } catch (err) {
            showToast('เกิดข้อผิดพลาดในการลบ', 'error');
        }
    };

    window.deleteWallet = async function(id) {
        if (!confirm('ยืนยันลบกระเป๋าเงินนี้? (รายการธุรกรรมที่ผูกกับกระเป๋านี้จะถูกลบไปด้วย)')) return;
        try {
            const res = await fetch(`api/index.php?action=wallet_delete&id=${id}`, { method: 'POST' });
            const data = await res.json();
            if (data.status === 'success') {
                showToast('ลบกระเป๋าเงินสำเร็จ', 'success');
                await loadWallets();
                renderWalletsManagement();
                await refreshDashboard();
            } else {
                showToast(data.message || 'ไม่สามารถลบได้', 'error');
            }
        } catch (err) {
            showToast('เกิดข้อผิดพลาด', 'error');
        }
    };

    window.deleteGoal = async function(id) {
        if (!confirm('ต้องการลบเป้าหมายนี้หรือไม่?')) return;
        try {
            const res = await fetch(`api/index.php?action=goal_delete&id=${id}`, { method: 'POST' });
            const data = await res.json();
            if (data.status === 'success') {
                showToast('ลบเป้าหมายสำเร็จ', 'success');
                await loadGoals();
            }
        } catch (err) {
            showToast('เกิดข้อผิดพลาด', 'error');
        }
    };

    // ================= HELPERS =================
    function showToast(msg, type = 'info') {
        const container = document.getElementById('toastContainer');
        if (!container) return;
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `<i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'}"></i> ${escapeHtml(msg)}`;
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 3200);
    }

    function formatNumber(num) {
        return Number(num || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatThaiDate(dateStr) {
        if (!dateStr) return '-';
        const [y, m, d] = dateStr.split('-');
        const thaiMonths = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        const monthIndex = parseInt(m, 10) - 1;
        const thaiYear = parseInt(y, 10) + 543;
        return `${parseInt(d, 10)} ${thaiMonths[monthIndex]} ${thaiYear}`;
    }

    function formatThaiMonthShort(monthNum) {
        const thaiMonths = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        return thaiMonths[parseInt(monthNum, 10) - 1] || '';
    }

    function renderTypeBadge(type) {
        if (type === 'income') return `<span class="badge-type badge-income"><i class="bi bi-arrow-down-left"></i> รายรับ</span>`;
        if (type === 'expense') return `<span class="badge-type badge-expense"><i class="bi bi-arrow-up-right"></i> รายจ่าย</span>`;
        return `<span class="badge-type badge-transfer"><i class="bi bi-arrow-left-right"></i> โอนเงิน</span>`;
    }

    function renderTxTypeAndCat(t) {
        if (t.type === 'transfer') {
            return `<span class="badge-type badge-transfer"><i class="bi bi-arrow-left-right"></i> โอนเงิน</span>`;
        }
        return `
            <div class="category-pill">
                <span class="category-dot" style="background: ${t.category_color || '#64748B'};"></span>
                <span>${escapeHtml(t.category_name || '-')}</span>
            </div>
        `;
    }

    function getTxAmountPrefix(type) {
        if (type === 'income') return '+';
        if (type === 'expense') return '-';
        return '';
    }

    function getTxAmountColorClass(type) {
        if (type === 'income') return 'text-success';
        if (type === 'expense') return 'text-danger';
        return 'text-primary';
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }
});
