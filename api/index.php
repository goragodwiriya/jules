<?php
// --- POS REST API Router ---

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once 'db.php';

function send_json($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function get_json_payload() {
    $raw = file_get_contents('php://input');
    if (empty($raw)) return [];
    $data = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        send_json(['status' => 'error', 'message' => 'Invalid JSON input'], 400);
    }
    return $data;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        // --- CATEGORIES ---
        case 'get_categories':
            $stmt = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
            send_json(['status' => 'success', 'data' => $stmt->fetchAll()]);
            break;

        case 'add_category':
            if ($method !== 'POST') send_json(['status' => 'error', 'message' => 'Method not allowed'], 405);
            $data = get_json_payload();
            if (empty($data['name'])) send_json(['status' => 'error', 'message' => 'Category name is required'], 400);

            $stmt = $pdo->prepare("INSERT INTO categories (name, icon) VALUES (?, ?)");
            $stmt->execute([$data['name'], $data['icon'] ?? 'bi-tag']);
            send_json(['status' => 'success', 'id' => $pdo->lastInsertId(), 'message' => 'Category created']);
            break;

        case 'update_category':
            if ($method !== 'POST' && $method !== 'PUT') send_json(['status' => 'error', 'message' => 'Method not allowed'], 405);
            $data = get_json_payload();
            if (empty($data['id']) || empty($data['name'])) send_json(['status' => 'error', 'message' => 'ID and Name required'], 400);

            $stmt = $pdo->prepare("UPDATE categories SET name = ?, icon = ? WHERE id = ?");
            $stmt->execute([$data['name'], $data['icon'] ?? 'bi-tag', $data['id']]);
            send_json(['status' => 'success', 'message' => 'Category updated']);
            break;

        case 'delete_category':
            if ($method !== 'POST' && $method !== 'DELETE') send_json(['status' => 'error', 'message' => 'Method not allowed'], 405);
            $data = get_json_payload();
            $id = $data['id'] ?? $_GET['id'] ?? null;
            if (!$id) send_json(['status' => 'error', 'message' => 'Category ID required'], 400);

            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            send_json(['status' => 'success', 'message' => 'Category deleted']);
            break;

        // --- PRODUCTS ---
        case 'get_products':
            $catId = $_GET['category_id'] ?? null;
            $search = $_GET['search'] ?? null;

            $sql = "SELECT p.*, c.name as category_name
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE 1=1";
            $params = [];

            if ($catId && $catId !== 'all') {
                $sql .= " AND p.category_id = ?";
                $params[] = $catId;
            }

            if ($search) {
                $sql .= " AND (p.name LIKE ? OR p.barcode LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }

            $sql .= " ORDER BY p.id DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            send_json(['status' => 'success', 'data' => $stmt->fetchAll()]);
            break;

        case 'add_product':
            if ($method !== 'POST') send_json(['status' => 'error', 'message' => 'Method not allowed'], 405);
            $data = get_json_payload();

            if (empty($data['name']) || !isset($data['price'])) {
                send_json(['status' => 'error', 'message' => 'Product name and price are required'], 400);
            }

            $stmt = $pdo->prepare("INSERT INTO products (barcode, name, category_id, price, cost, stock, image_url)
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['barcode'] ?? null,
                $data['name'],
                $data['category_id'] ?? null,
                floatval($data['price']),
                floatval($data['cost'] ?? 0),
                intval($data['stock'] ?? 0),
                $data['image_url'] ?? ''
            ]);
            send_json(['status' => 'success', 'id' => $pdo->lastInsertId(), 'message' => 'Product created']);
            break;

        case 'update_product':
            if ($method !== 'POST' && $method !== 'PUT') send_json(['status' => 'error', 'message' => 'Method not allowed'], 405);
            $data = get_json_payload();

            if (empty($data['id']) || empty($data['name']) || !isset($data['price'])) {
                send_json(['status' => 'error', 'message' => 'Product ID, name, and price are required'], 400);
            }

            $stmt = $pdo->prepare("UPDATE products
                                   SET barcode = ?, name = ?, category_id = ?, price = ?, cost = ?, stock = ?, image_url = ?
                                   WHERE id = ?");
            $stmt->execute([
                $data['barcode'] ?? null,
                $data['name'],
                $data['category_id'] ?? null,
                floatval($data['price']),
                floatval($data['cost'] ?? 0),
                intval($data['stock'] ?? 0),
                $data['image_url'] ?? '',
                $data['id']
            ]);
            send_json(['status' => 'success', 'message' => 'Product updated']);
            break;

        case 'delete_product':
            if ($method !== 'POST' && $method !== 'DELETE') send_json(['status' => 'error', 'message' => 'Method not allowed'], 405);
            $data = get_json_payload();
            $id = $data['id'] ?? $_GET['id'] ?? null;

            if (!$id) send_json(['status' => 'error', 'message' => 'Product ID required'], 400);

            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            send_json(['status' => 'success', 'message' => 'Product deleted']);
            break;

        // --- CHECKOUT / ORDERS ---
        case 'checkout':
            if ($method !== 'POST') send_json(['status' => 'error', 'message' => 'Method not allowed'], 405);
            $data = get_json_payload();

            $items = $data['items'] ?? [];
            if (empty($items)) {
                send_json(['status' => 'error', 'message' => 'Cart is empty'], 400);
            }

            $paymentMethod = $data['payment_method'] ?? 'cash';
            $paidAmount = floatval($data['paid_amount'] ?? 0);
            $discount = floatval($data['discount'] ?? 0);
            $tax = floatval($data['tax'] ?? 0);

            // Start transaction
            $pdo->beginTransaction();

            try {
                $subtotal = 0;
                $orderItemsToInsert = [];

                foreach ($items as $item) {
                    $prodId = $item['id'];
                    $qty = intval($item['quantity']);

                    // Check product and stock
                    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                    $stmt->execute([$prodId]);
                    $product = $stmt->fetch();

                    if (!$product) {
                        throw new Exception("Product ID {$prodId} not found");
                    }

                    if ($product['stock'] < $qty) {
                        throw new Exception("สินค้า {$product['name']} มีจำนวนไม่พอในสต็อก (เหลือ {$product['stock']})");
                    }

                    $unitPrice = floatval($product['price']);
                    $itemSubtotal = $unitPrice * $qty;
                    $subtotal += $itemSubtotal;

                    // Deduct stock
                    $updateStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                    $updateStock->execute([$qty, $prodId]);

                    $orderItemsToInsert[] = [
                        'product_id' => $prodId,
                        'product_name' => $product['name'],
                        'unit_price' => $unitPrice,
                        'quantity' => $qty,
                        'subtotal' => $itemSubtotal
                    ];
                }

                $finalAmount = max(0, ($subtotal - $discount) + $tax);

                if ($paymentMethod === 'cash' && $paidAmount < $finalAmount) {
                    throw new Exception("จำนวนเงินที่รับชำระไม่พอ (ขาดอีก " . number_format($finalAmount - $paidAmount, 2) . " บาท)");
                }

                $changeAmount = ($paymentMethod === 'cash') ? ($paidAmount - $finalAmount) : 0;
                if ($paymentMethod === 'qr') {
                    $paidAmount = $finalAmount;
                }

                $orderNumber = 'ORD-' . date('YmdHis') . '-' . rand(100, 999);

                $stmt = $pdo->prepare("INSERT INTO orders (order_number, subtotal, discount, tax, final_amount, payment_method, paid_amount, change_amount)
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $orderNumber,
                    $subtotal,
                    $discount,
                    $tax,
                    $finalAmount,
                    $paymentMethod,
                    $paidAmount,
                    $changeAmount
                ]);
                $orderId = $pdo->lastInsertId();

                $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity, subtotal)
                                           VALUES (?, ?, ?, ?, ?, ?)");
                foreach ($orderItemsToInsert as $item) {
                    $itemStmt->execute([
                        $orderId,
                        $item['product_id'],
                        $item['product_name'],
                        $item['unit_price'],
                        $item['quantity'],
                        $item['subtotal']
                    ]);
                }

                $pdo->commit();

                send_json([
                    'status' => 'success',
                    'message' => 'ชำระเงินสำเร็จ',
                    'data' => [
                        'order_id' => $orderId,
                        'order_number' => $orderNumber,
                        'subtotal' => $subtotal,
                        'discount' => $discount,
                        'tax' => $tax,
                        'final_amount' => $finalAmount,
                        'payment_method' => $paymentMethod,
                        'paid_amount' => $paidAmount,
                        'change_amount' => $changeAmount,
                        'items' => $orderItemsToInsert,
                        'created_at' => date('Y-m-d H:i:s')
                    ]
                ], 201);

            } catch (Exception $e) {
                $pdo->rollBack();
                send_json(['status' => 'error', 'message' => $e->getMessage()], 400);
            }
            break;

        case 'get_orders':
            $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 50");
            $orders = $stmt->fetchAll();
            send_json(['status' => 'success', 'data' => $orders]);
            break;

        case 'get_order_details':
            $id = $_GET['id'] ?? null;
            if (!$id) send_json(['status' => 'error', 'message' => 'Order ID required'], 400);

            $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
            $stmt->execute([$id]);
            $order = $stmt->fetch();

            if (!$order) send_json(['status' => 'error', 'message' => 'Order not found'], 404);

            $itemStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $itemStmt->execute([$id]);
            $items = $itemStmt->fetchAll();

            $order['items'] = $items;
            send_json(['status' => 'success', 'data' => $order]);
            break;

        // --- DASHBOARD / REPORTS ---
        case 'get_dashboard_summary':
            // Today Sales
            $today = date('Y-m-d');
            $stmt = $pdo->prepare("SELECT COUNT(*) as total_orders, COALESCE(SUM(final_amount), 0) as total_sales FROM orders WHERE DATE(created_at) = ?");
            $stmt->execute([$today]);
            $todayStats = $stmt->fetch();

            // Total All Time Sales
            $stmt = $pdo->query("SELECT COUNT(*) as total_orders, COALESCE(SUM(final_amount), 0) as total_sales FROM orders");
            $totalStats = $stmt->fetch();

            // Total Products count
            $stmt = $pdo->query("SELECT COUNT(*) as total_products FROM products");
            $prodStats = $stmt->fetch();

            // Low Stock Items (< 10)
            $stmt = $pdo->query("SELECT * FROM products WHERE stock < 10 ORDER BY stock ASC");
            $lowStockItems = $stmt->fetchAll();

            // Top 5 selling products
            $stmt = $pdo->query("SELECT product_name, SUM(quantity) as total_qty, SUM(subtotal) as total_amount
                                 FROM order_items
                                 GROUP BY product_name
                                 ORDER BY total_qty DESC
                                 LIMIT 5");
            $topProducts = $stmt->fetchAll();

            // Sales breakdown by payment method
            $stmt = $pdo->query("SELECT payment_method, COUNT(*) as count, COALESCE(SUM(final_amount), 0) as total
                                 FROM orders
                                 GROUP BY payment_method");
            $paymentBreakdown = $stmt->fetchAll();

            send_json([
                'status' => 'success',
                'data' => [
                    'today' => $todayStats,
                    'overall' => $totalStats,
                    'total_products' => $prodStats['total_products'],
                    'low_stock' => $lowStockItems,
                    'top_products' => $topProducts,
                    'payment_breakdown' => $paymentBreakdown
                ]
            ]);
            break;

        default:
            send_json(['status' => 'error', 'message' => 'Endpoint action not found'], 404);
            break;
    }
} catch (PDOException $e) {
    send_json(['status' => 'error', 'message' => 'Database query error: ' . $e->getMessage()], 500);
} catch (Exception $e) {
    send_json(['status' => 'error', 'message' => $e->getMessage()], 500);
}
