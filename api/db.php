<?php
/**
 * SQLite Database Connection and Auto-Initialization
 */

$dbPath = __DIR__ . '/../pos_database.sqlite';

try {
    $pdo = new PDO("sqlite:" . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Enable Foreign Keys in SQLite
    $pdo->exec("PRAGMA foreign_keys = ON;");

    // Initialize Schema
    initDatabase($pdo);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit;
}

function initDatabase($pdo) {
    // Create categories table
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        icon TEXT DEFAULT 'bi-tag',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Create products table
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        barcode TEXT UNIQUE,
        name TEXT NOT NULL,
        category_id INTEGER,
        price REAL NOT NULL DEFAULT 0,
        cost REAL NOT NULL DEFAULT 0,
        stock INTEGER NOT NULL DEFAULT 0,
        image_url TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    )");

    // Create orders table
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_number TEXT UNIQUE NOT NULL,
        subtotal REAL NOT NULL,
        discount REAL DEFAULT 0,
        tax REAL DEFAULT 0,
        final_amount REAL NOT NULL,
        payment_method TEXT NOT NULL,
        paid_amount REAL NOT NULL,
        change_amount REAL DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Create order_items table
    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id INTEGER NOT NULL,
        product_id INTEGER,
        product_name TEXT NOT NULL,
        unit_price REAL NOT NULL,
        quantity INTEGER NOT NULL,
        subtotal REAL NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
    )");

    // Check if categories table is empty and seed
    $stmt = $pdo->query("SELECT COUNT(*) FROM categories");
    if ($stmt->fetchColumn() == 0) {
        seedInitialData($pdo);
    }
}

function seedInitialData($pdo) {
    // Insert initial categories
    $categories = [
        ['name' => 'เครื่องดื่มร้อน & เย็น', 'icon' => 'bi-cup-hot'],
        ['name' => 'เบเกอรี่ & ของหวาน', 'icon' => 'bi-cake2'],
        ['name' => 'อาหารจานหลัก', 'icon' => 'bi-egg-fried'],
        ['name' => 'ของทานเล่น', 'icon' => 'bi-bucket']
    ];

    $catStmt = $pdo->prepare("INSERT INTO categories (name, icon) VALUES (:name, :icon)");
    $catIds = [];
    foreach ($categories as $cat) {
        $catStmt->execute([':name' => $cat['name'], ':icon' => $cat['icon']]);
        $catIds[$cat['name']] = $pdo->lastInsertId();
    }

    // Insert initial mockup products with high quality internet image URLs
    $products = [
        [
            'barcode' => '8850001',
            'name' => 'กาแฟเอสเพรสโซ่ร้อน (Hot Espresso)',
            'category_id' => $catIds['เครื่องดื่มร้อน & เย็น'],
            'price' => 55.00,
            'cost' => 20.00,
            'stock' => 100,
            'image_url' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=400&auto=format&fit=crop&q=80'
        ],
        [
            'barcode' => '8850002',
            'name' => 'ลาเต้เย็น (Iced Latte)',
            'category_id' => $catIds['เครื่องดื่มร้อน & เย็น'],
            'price' => 65.00,
            'cost' => 25.00,
            'stock' => 85,
            'image_url' => 'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=400&auto=format&fit=crop&q=80'
        ],
        [
            'barcode' => '8850003',
            'name' => 'ชาไทยเย็น (Iced Thai Milk Tea)',
            'category_id' => $catIds['เครื่องดื่มร้อน & เย็น'],
            'price' => 50.00,
            'cost' => 18.00,
            'stock' => 120,
            'image_url' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400&auto=format&fit=crop&q=80'
        ],
        [
            'barcode' => '8850004',
            'name' => 'ชาเขียวมัจฉะเย็น (Iced Matcha Green Tea)',
            'category_id' => $catIds['เครื่องดื่มร้อน & เย็น'],
            'price' => 70.00,
            'cost' => 28.00,
            'stock' => 60,
            'image_url' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=400&auto=format&fit=crop&q=80'
        ],
        [
            'barcode' => '8850005',
            'name' => 'น้ำส้มคั้นสด (Fresh Orange Juice)',
            'category_id' => $catIds['เครื่องดื่มร้อน & เย็น'],
            'price' => 45.00,
            'cost' => 20.00,
            'stock' => 50,
            'image_url' => 'https://images.unsplash.com/photo-1613478223719-2ab802602423?w=400&auto=format&fit=crop&q=80'
        ],
        [
            'barcode' => '8850006',
            'name' => 'ครัวซองต์เนยสด (Butter Croissant)',
            'category_id' => $catIds['เบเกอรี่ & ของหวาน'],
            'price' => 60.00,
            'cost' => 22.00,
            'stock' => 35,
            'image_url' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400&auto=format&fit=crop&q=80'
        ],
        [
            'barcode' => '8850007',
            'name' => 'สตรอเบอร์รี่เค้ก (Strawberry Cake)',
            'category_id' => $catIds['เบเกอรี่ & ของหวาน'],
            'price' => 95.00,
            'cost' => 40.00,
            'stock' => 20,
            'image_url' => 'https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=400&auto=format&fit=crop&q=80'
        ],
        [
            'barcode' => '8850008',
            'name' => 'ช็อกโกแลตบราวนี่ (Chocolate Brownie)',
            'category_id' => $catIds['เบเกอรี่ & ของหวาน'],
            'price' => 55.00,
            'cost' => 20.00,
            'stock' => 40,
            'image_url' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=400&auto=format&fit=crop&q=80'
        ],
        [
            'barcode' => '8850009',
            'name' => 'ผัดไทยกุ้งสด (Pad Thai with Shrimp)',
            'category_id' => $catIds['อาหารจานหลัก'],
            'price' => 120.00,
            'cost' => 50.00,
            'stock' => 30,
            'image_url' => 'https://images.unsplash.com/photo-1559847844-5315695dadae?w=400&auto=format&fit=crop&q=80'
        ],
        [
            'barcode' => '8850010',
            'name' => 'สปาเก็ตตี้คาร์โบนาร่า (Spaghetti Carbonara)',
            'category_id' => $catIds['อาหารจานหลัก'],
            'price' => 140.00,
            'cost' => 55.00,
            'stock' => 25,
            'image_url' => 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=400&auto=format&fit=crop&q=80'
        ],
        [
            'barcode' => '8850011',
            'name' => 'ชีสเบอร์เกอร์เนื้อ (Beef Cheeseburger)',
            'category_id' => $catIds['อาหารจานหลัก'],
            'price' => 150.00,
            'cost' => 65.00,
            'stock' => 20,
            'image_url' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400&auto=format&fit=crop&q=80'
        ],
        [
            'barcode' => '8850012',
            'name' => 'เฟรนช์ฟรายส์ทอดกรอบ (French Fries)',
            'category_id' => $catIds['ของทานเล่น'],
            'price' => 69.00,
            'cost' => 25.00,
            'stock' => 50,
            'image_url' => 'https://images.unsplash.com/photo-1576107232684-1279f3908594?w=400&auto=format&fit=crop&q=80'
        ]
    ];

    $prodStmt = $pdo->prepare("INSERT INTO products (barcode, name, category_id, price, cost, stock, image_url)
                               VALUES (:barcode, :name, :category_id, :price, :cost, :stock, :image_url)");
    foreach ($products as $p) {
        $prodStmt->execute([
            ':barcode' => $p['barcode'],
            ':name' => $p['name'],
            ':category_id' => $p['category_id'],
            ':price' => $p['price'],
            ':cost' => $p['cost'],
            ':stock' => $p['stock'],
            ':image_url' => $p['image_url']
        ]);
    }
}
