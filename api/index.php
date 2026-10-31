<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/db.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Helper to get JSON input
function getJsonInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}

try {
    switch ($action) {
        // ================= WALLETS =================
        case 'wallets':
            if ($method === 'GET') {
                // Get wallets with calculated current balances
                $sql = "
                    SELECT
                        w.id, w.name, w.type, w.initial_balance, w.color, w.icon, w.created_at,
                        (
                            w.initial_balance
                            + COALESCE((SELECT SUM(amount) FROM transactions WHERE wallet_id = w.id AND type = 'income'), 0)
                            - COALESCE((SELECT SUM(amount) FROM transactions WHERE wallet_id = w.id AND type = 'expense'), 0)
                            - COALESCE((SELECT SUM(amount) FROM transactions WHERE wallet_id = w.id AND type = 'transfer'), 0)
                            + COALESCE((SELECT SUM(amount) FROM transactions WHERE target_wallet_id = w.id AND type = 'transfer'), 0)
                        ) AS current_balance
                    FROM wallets w
                    ORDER BY w.id ASC
                ";
                $stmt = $pdo->query($sql);
                $wallets = $stmt->fetchAll();
                echo json_encode(['status' => 'success', 'data' => $wallets]);
            } elseif ($method === 'POST') {
                $data = getJsonInput();
                if (empty($data['name'])) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'ชื่อกระเป๋าเงินจำเป็นต้องระบุ']);
                    exit;
                }
                $stmt = $pdo->prepare("INSERT INTO wallets (name, type, initial_balance, color, icon) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $data['name'],
                    $data['type'] ?? 'cash',
                    floatval($data['initial_balance'] ?? 0),
                    $data['color'] ?? '#3B82F6',
                    $data['icon'] ?? 'bi-wallet2'
                ]);
                echo json_encode(['status' => 'success', 'id' => $pdo->lastInsertId(), 'message' => 'เพิ่มกระเป๋าเงินเรียบร้อย']);
            }
            break;

        case 'wallet_delete':
            if ($method === 'POST' || $method === 'DELETE') {
                $id = intval($_GET['id'] ?? ($_POST['id'] ?? 0));
                if ($id <= 0) {
                    $data = getJsonInput();
                    $id = intval($data['id'] ?? 0);
                }
                // Check if wallet count > 1
                $cnt = $pdo->query("SELECT COUNT(*) FROM wallets")->fetchColumn();
                if ($cnt <= 1) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'ต้องมีกระเป๋าเงินอย่างน้อย 1 รายการ']);
                    exit;
                }
                $stmt = $pdo->prepare("DELETE FROM wallets WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['status' => 'success', 'message' => 'ลบกระเป๋าเงินเรียบร้อย']);
            }
            break;

        // ================= CATEGORIES =================
        case 'categories':
            if ($method === 'GET') {
                $type = $_GET['type'] ?? '';
                if ($type) {
                    $stmt = $pdo->prepare("SELECT * FROM categories WHERE type = ? ORDER BY name ASC");
                    $stmt->execute([$type]);
                } else {
                    $stmt = $pdo->query("SELECT * FROM categories ORDER BY type DESC, name ASC");
                }
                echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
            } elseif ($method === 'POST') {
                $data = getJsonInput();
                if (empty($data['name']) || empty($data['type'])) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'ระบุชื่อและประเภทหมวดหมู่ให้ครบถ้วน']);
                    exit;
                }
                $stmt = $pdo->prepare("INSERT INTO categories (name, type, icon, color) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $data['name'],
                    $data['type'],
                    $data['icon'] ?? 'bi-tag',
                    $data['color'] ?? '#64748B'
                ]);
                echo json_encode(['status' => 'success', 'id' => $pdo->lastInsertId(), 'message' => 'สร้างหมวดหมู่สำเร็จ']);
            }
            break;

        // ================= TRANSACTIONS =================
        case 'transactions':
            if ($method === 'GET') {
                $limit = intval($_GET['limit'] ?? 100);
                $month = $_GET['month'] ?? ''; // Format: YYYY-MM
                $walletId = intval($_GET['wallet_id'] ?? 0);
                $type = $_GET['type'] ?? '';
                $search = trim($_GET['search'] ?? '');

                $where = [];
                $params = [];

                if ($month) {
                    $where[] = "strftime('%Y-%m', t.transaction_date) = ?";
                    $params[] = $month;
                }
                if ($walletId > 0) {
                    $where[] = "(t.wallet_id = ? OR t.target_wallet_id = ?)";
                    $params[] = $walletId;
                    $params[] = $walletId;
                }
                if ($type) {
                    $where[] = "t.type = ?";
                    $params[] = $type;
                }
                if ($search !== '') {
                    $where[] = "(t.note LIKE ? OR c.name LIKE ?)";
                    $params[] = "%$search%";
                    $params[] = "%$search%";
                }

                $whereClause = count($where) ? "WHERE " . implode(' AND ', $where) : "";

                $sql = "
                    SELECT
                        t.*,
                        w.name as wallet_name,
                        w.icon as wallet_icon,
                        w.color as wallet_color,
                        tw.name as target_wallet_name,
                        c.name as category_name,
                        c.icon as category_icon,
                        c.color as category_color
                    FROM transactions t
                    LEFT JOIN wallets w ON t.wallet_id = w.id
                    LEFT JOIN wallets tw ON t.target_wallet_id = tw.id
                    LEFT JOIN categories c ON t.category_id = c.id
                    $whereClause
                    ORDER BY t.transaction_date DESC, t.id DESC
                    LIMIT $limit
                ";

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $transactions = $stmt->fetchAll();

                echo json_encode(['status' => 'success', 'data' => $transactions]);
            } elseif ($method === 'POST') {
                $data = getJsonInput();
                $type = $data['type'] ?? 'expense';
                $walletId = intval($data['wallet_id'] ?? 0);
                $targetWalletId = !empty($data['target_wallet_id']) ? intval($data['target_wallet_id']) : null;
                $categoryId = !empty($data['category_id']) ? intval($data['category_id']) : null;
                $amount = floatval($data['amount'] ?? 0);
                $date = $data['transaction_date'] ?? date('Y-m-d');
                $note = trim($data['note'] ?? '');

                if ($amount <= 0 || !$walletId) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'จำนวนเงินและกระเป๋าเงินไม่ถูกต้อง']);
                    exit;
                }

                if ($type === 'transfer' && (!$targetWalletId || $walletId === $targetWalletId)) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'กรุณาเลือกกระเป๋าเงินปลายทางที่แตกต่างกัน']);
                    exit;
                }

                $stmt = $pdo->prepare("
                    INSERT INTO transactions (type, wallet_id, target_wallet_id, category_id, amount, transaction_date, note)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$type, $walletId, $targetWalletId, $categoryId, $amount, $date, $note]);

                echo json_encode([
                    'status' => 'success',
                    'id' => $pdo->lastInsertId(),
                    'message' => 'บันทึกรายการสำเร็จ'
                ]);
            }
            break;

        case 'transaction_delete':
            if ($method === 'POST' || $method === 'DELETE') {
                $id = intval($_GET['id'] ?? ($_POST['id'] ?? 0));
                if ($id <= 0) {
                    $data = getJsonInput();
                    $id = intval($data['id'] ?? 0);
                }
                $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['status' => 'success', 'message' => 'ลบรายการสำเร็จ']);
            }
            break;

        // ================= ANALYTICS & SUMMARY =================
        case 'summary':
            $month = $_GET['month'] ?? date('Y-m'); // Default current month

            // 1. Total Net Balance across all wallets
            $balanceSql = "
                SELECT
                    SUM(
                        w.initial_balance
                        + COALESCE((SELECT SUM(amount) FROM transactions WHERE wallet_id = w.id AND type = 'income'), 0)
                        - COALESCE((SELECT SUM(amount) FROM transactions WHERE wallet_id = w.id AND type = 'expense'), 0)
                        - COALESCE((SELECT SUM(amount) FROM transactions WHERE wallet_id = w.id AND type = 'transfer'), 0)
                        + COALESCE((SELECT SUM(amount) FROM transactions WHERE target_wallet_id = w.id AND type = 'transfer'), 0)
                    ) as total_balance
                FROM wallets w
            ";
            $totalBalance = floatval($pdo->query($balanceSql)->fetchColumn() ?? 0);

            // 2. Month income & expense
            $stmt = $pdo->prepare("
                SELECT
                    COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as month_income,
                    COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as month_expense
                FROM transactions
                WHERE strftime('%Y-%m', transaction_date) = ?
            ");
            $stmt->execute([$month]);
            $monthTotals = $stmt->fetch();
            $monthIncome = floatval($monthTotals['month_income']);
            $monthExpense = floatval($monthTotals['month_expense']);
            $netSavings = $monthIncome - $monthExpense;

            // 3. Category Breakdown for expense in month
            $catStmt = $pdo->prepare("
                SELECT
                    c.id, c.name, c.icon, c.color,
                    COALESCE(SUM(t.amount), 0) as total_amount
                FROM transactions t
                JOIN categories c ON t.category_id = c.id
                WHERE t.type = 'expense' AND strftime('%Y-%m', t.transaction_date) = ?
                GROUP BY c.id, c.name, c.icon, c.color
                ORDER BY total_amount DESC
            ");
            $catStmt->execute([$month]);
            $expenseCategories = $catStmt->fetchAll();

            // 4. Daily flow in month
            $dailyStmt = $pdo->prepare("
                SELECT
                    transaction_date,
                    SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as daily_income,
                    SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as daily_expense
                FROM transactions
                WHERE strftime('%Y-%m', transaction_date) = ?
                GROUP BY transaction_date
                ORDER BY transaction_date ASC
            ");
            $dailyStmt->execute([$month]);
            $dailyFlow = $dailyStmt->fetchAll();

            echo json_encode([
                'status' => 'success',
                'data' => [
                    'month' => $month,
                    'total_balance' => $totalBalance,
                    'month_income' => $monthIncome,
                    'month_expense' => $monthExpense,
                    'net_savings' => $netSavings,
                    'expense_by_category' => $expenseCategories,
                    'daily_flow' => $dailyFlow
                ]
            ]);
            break;

        // ================= SAVINGS GOALS =================
        case 'goals':
            if ($method === 'GET') {
                $stmt = $pdo->query("SELECT * FROM savings_goals ORDER BY id ASC");
                echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
            } elseif ($method === 'POST') {
                $data = getJsonInput();
                if (empty($data['name']) || empty($data['target_amount'])) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกชื่อเป้าหมายและยอดเงินเป้าหมาย']);
                    exit;
                }
                $stmt = $pdo->prepare("INSERT INTO savings_goals (name, target_amount, current_amount, target_date, color, icon) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $data['name'],
                    floatval($data['target_amount']),
                    floatval($data['current_amount'] ?? 0),
                    $data['target_date'] ?? null,
                    $data['color'] ?? '#10B981',
                    $data['icon'] ?? 'bi-piggy-bank'
                ]);
                echo json_encode(['status' => 'success', 'id' => $pdo->lastInsertId(), 'message' => 'สร้างเป้าหมายสำเร็จ']);
            }
            break;

        case 'goal_update_deposit':
            if ($method === 'POST') {
                $data = getJsonInput();
                $id = intval($data['id'] ?? 0);
                $deposit = floatval($data['deposit_amount'] ?? 0);
                if ($id <= 0 || $deposit <= 0) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'ข้อมูลเงินออมไม่ถูกต้อง']);
                    exit;
                }
                $stmt = $pdo->prepare("UPDATE savings_goals SET current_amount = current_amount + ? WHERE id = ?");
                $stmt->execute([$deposit, $id]);
                echo json_encode(['status' => 'success', 'message' => 'อัปเดตยอดออมสำเร็จ']);
            }
            break;

        case 'goal_delete':
            if ($method === 'POST' || $method === 'DELETE') {
                $id = intval($_GET['id'] ?? ($_POST['id'] ?? 0));
                if ($id <= 0) {
                    $data = getJsonInput();
                    $id = intval($data['id'] ?? 0);
                }
                $stmt = $pdo->prepare("DELETE FROM savings_goals WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['status' => 'success', 'message' => 'ลบเป้าหมายสำเร็จ']);
            }
            break;

        // ================= EXPORT CSV =================
        case 'export_csv':
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="omsin_transactions_' . date('Ymd_His') . '.csv"');

            // Output UTF-8 BOM for Excel compatibility
            echo "\xEF\xBB\xBF";

            $output = fopen('php://output', 'w');
            fputcsv($output, ['ID', 'วันที่', 'ประเภท', 'กระเป๋าเงิน', 'กระเป๋าเงินปลายทาง', 'หมวดหมู่', 'จำนวนเงิน (บาท)', 'บันทึกช่วยจำ']);

            $stmt = $pdo->query("
                SELECT
                    t.id, t.transaction_date, t.type,
                    w.name as wallet_name,
                    tw.name as target_wallet_name,
                    c.name as category_name,
                    t.amount,
                    t.note
                FROM transactions t
                LEFT JOIN wallets w ON t.wallet_id = w.id
                LEFT JOIN wallets tw ON t.target_wallet_id = tw.id
                LEFT JOIN categories c ON t.category_id = c.id
                ORDER BY t.transaction_date DESC, t.id DESC
            ");

            $typeNames = [
                'income' => 'รายรับ',
                'expense' => 'รายจ่าย',
                'transfer' => 'โอนเงิน'
            ];

            while ($row = $stmt->fetch()) {
                fputcsv($output, [
                    $row['id'],
                    $row['transaction_date'],
                    $typeNames[$row['type']] ?? $row['type'],
                    $row['wallet_name'],
                    $row['target_wallet_name'] ?? '-',
                    $row['category_name'] ?? ($row['type'] === 'transfer' ? 'โอนเงินระหว่างกระเป๋า' : 'ไม่ระบุ'),
                    number_format($row['amount'], 2, '.', ''),
                    $row['note']
                ]);
            }
            fclose($output);
            exit;

        default:
            echo json_encode([
                'status' => 'ok',
                'service' => 'Omsin Personal Finance API',
                'version' => '1.0.0',
                'endpoints' => [
                    'GET /api/?action=wallets',
                    'POST /api/?action=wallets',
                    'POST /api/?action=wallet_delete',
                    'GET /api/?action=categories',
                    'POST /api/?action=categories',
                    'GET /api/?action=transactions',
                    'POST /api/?action=transactions',
                    'POST /api/?action=transaction_delete',
                    'GET /api/?action=summary',
                    'GET /api/?action=goals',
                    'POST /api/?action=goals',
                    'POST /api/?action=goal_update_deposit',
                    'GET /api/?action=export_csv'
                ]
            ]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
