<?php
/**
 * SQLite Database Connection and Schema Initialization for Omsin (Personal Finance)
 */

$dbPath = __DIR__ . '/../finance.sqlite';

try {
    $pdo = new PDO("sqlite:" . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Enable Foreign Keys in SQLite
    $pdo->exec("PRAGMA foreign_keys = ON;");

    // Initialize Schema
    initFinanceDatabase($pdo);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit;
}

function initFinanceDatabase($pdo) {
    // 1. Wallets table (กระเป๋าเงิน / บัญชี)
    $pdo->exec("CREATE TABLE IF NOT EXISTS wallets (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        type TEXT NOT NULL DEFAULT 'cash', -- cash, bank, ewallet, credit
        initial_balance REAL NOT NULL DEFAULT 0,
        color TEXT DEFAULT '#4F46E5',
        icon TEXT DEFAULT 'bi-wallet2',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Categories table (หมวดหมู่รายรับ-รายจ่าย)
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        type TEXT NOT NULL, -- income, expense
        icon TEXT DEFAULT 'bi-tag',
        color TEXT DEFAULT '#64748B',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 3. Transactions table (บันทึกรายรับ-รายจ่าย-โอนเงิน)
    $pdo->exec("CREATE TABLE IF NOT EXISTS transactions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        type TEXT NOT NULL, -- income, expense, transfer
        wallet_id INTEGER NOT NULL,
        target_wallet_id INTEGER, -- used when type is transfer
        category_id INTEGER,
        amount REAL NOT NULL,
        transaction_date DATE NOT NULL,
        note TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE,
        FOREIGN KEY (target_wallet_id) REFERENCES wallets(id) ON DELETE SET NULL,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    )");

    // 4. Savings Goals table (เป้าหมายเงินออม)
    $pdo->exec("CREATE TABLE IF NOT EXISTS savings_goals (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        target_amount REAL NOT NULL,
        current_amount REAL NOT NULL DEFAULT 0,
        target_date DATE,
        color TEXT DEFAULT '#10B981',
        icon TEXT DEFAULT 'bi-piggy-bank',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Seed default wallets if empty
    $walletCount = $pdo->query("SELECT COUNT(*) FROM wallets")->fetchColumn();
    if ($walletCount == 0) {
        $stmt = $pdo->prepare("INSERT INTO wallets (name, type, initial_balance, color, icon) VALUES (?, ?, ?, ?, ?)");
        $defaultWallets = [
            ['เงินสด (Cash)', 'cash', 1500.0, '#10B981', 'bi-cash-coin'],
            ['บัญชีธนาคาร (Bank)', 'bank', 25000.0, '#3B82F6', 'bi-bank'],
            ['วอลเล็ท (e-Wallet)', 'ewallet', 850.0, '#F59E0B', 'bi-phone']
        ];
        foreach ($defaultWallets as $w) {
            $stmt->execute($w);
        }
    }

    // Seed default categories if empty
    $catCount = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if ($catCount == 0) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, type, icon, color) VALUES (?, ?, ?, ?)");
        $defaultCategories = [
            // Expense
            ['อาหารและเครื่องดื่ม', 'expense', 'bi-cup-hot', '#EF4444'],
            ['การเดินทาง / คมนาคม', 'expense', 'bi-car-front', '#F97316'],
            ['ช้อปปิ้ง / ของใช้', 'expense', 'bi-bag', '#EC4899'],
            ['บิล & ค่าสาธารณูปโภค', 'expense', 'bi-lightning-charge', '#EAB308'],
            ['ที่อยู่อาศัย / ค่าเช่า', 'expense', 'bi-house-door', '#8B5CF6'],
            ['สุขภาพ & การแพทย์', 'expense', 'bi-heart-pulse', '#06B6D4'],
            ['ความบันเทิง / พักผ่อน', 'expense', 'bi-controller', '#6366F1'],
            ['การศึกษา & พัฒนาตนเอง', 'expense', 'bi-book', '#14B8A6'],
            ['อื่นๆ (รายจ่าย)', 'expense', 'bi-three-dots', '#94A3B8'],

            // Income
            ['เงินเดือน / ค่าจ้าง', 'income', 'bi-cash-stack', '#10B981'],
            ['โบนัส / เงินพิเศษ', 'income', 'bi-gift', '#059669'],
            ['ธุรกิจ / ค้าขาย', 'income', 'bi-shop', '#047857'],
            ['เงินปันผล / ดอกเบี้ย', 'income', 'bi-graph-up-arrow', '#0D9488'],
            ['เงินโอนเข้า / ให้โดยเสน่หา', 'income', 'bi-wallet2', '#3B82F6'],
            ['อื่นๆ (รายรับ)', 'income', 'bi-plus-circle', '#64748B']
        ];
        foreach ($defaultCategories as $c) {
            $stmt->execute($c);
        }
    }

    // Seed initial sample transactions if empty
    $txCount = $pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
    if ($txCount == 0) {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $twoDaysAgo = date('Y-m-d', strtotime('-2 days'));

        $stmt = $pdo->prepare("INSERT INTO transactions (type, wallet_id, target_wallet_id, category_id, amount, transaction_date, note) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $sampleTx = [
            ['income', 2, null, 10, 35000.0, date('Y-m-01'), 'เงินเดือนประจำเดือน'],
            ['expense', 1, null, 1, 120.0, $today, 'ข้าวกะเพราหมูกรอบ + ไข่ดาว'],
            ['expense', 1, null, 1, 65.0, $today, 'กาแฟอเมซอนยามบ่าย'],
            ['expense', 2, null, 4, 1450.0, $yesterday, 'ค่าไฟฟ้ารอบบิลล่าสุด'],
            ['expense', 3, null, 3, 390.0, $twoDaysAgo, 'สั่งซื้อของใช้จากช้อปปี้'],
            ['transfer', 2, 1, null, 1000.0, $yesterday, 'กดเงินสดจากตู้ ATM']
        ];
        foreach ($sampleTx as $tx) {
            $stmt->execute($tx);
        }
    }

    // Seed sample savings goals if empty
    $goalCount = $pdo->query("SELECT COUNT(*) FROM savings_goals")->fetchColumn();
    if ($goalCount == 0) {
        $stmt = $pdo->prepare("INSERT INTO savings_goals (name, target_amount, current_amount, target_date, color, icon) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['เงินสำรองฉุกเฉิน 6 เดือน', 60000.0, 25000.0, date('Y-12-31'), '#10B981', 'bi-shield-check']);
        $stmt->execute(['ท่องเที่ยวปลายปีที่ญี่ปุ่น', 35000.0, 12000.0, date('Y-11-30'), '#3B82F6', 'bi-airplane']);
    }
}
