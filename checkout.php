<?php
/**
 * Menyimpan isi keranjang (session) sebagai pesanan di database.
 * Dipanggil lewat AJAX dari cart.php sebelum WhatsApp dibuka.
 * Harga SELALU dihitung ulang dari database, bukan dari browser.
 */
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isAjax()) {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (!is_array($cart) || !$cart) {
    echo json_encode(['success' => false, 'message' => 'Keranjang kosong']);
    exit;
}

if (isLoggedIn()) {
    $name = $_SESSION['full_name'] ?? 'Pelanggan';
} else {
    $name = trim((string)($_POST['name'] ?? ''));
    $name = mb_substr($name, 0, 100);
    if ($name === '') {
        $name = 'Tamu';
    }
}

try {
    $db  = getDB();
    $ids = array_values(array_unique(array_map('intval', array_keys($cart))));
    $in  = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $db->prepare("SELECT id, name, price FROM products WHERE is_active = 1 AND id IN ($in)");
    $stmt->execute($ids);
    $found = [];
    foreach ($stmt->fetchAll() as $row) {
        $found[(int)$row['id']] = $row;
    }

    $items = [];
    $total = 0;
    foreach ($cart as $pid => $qty) {
        $pid = (int)$pid;
        $qty = max(1, min(99, (int)$qty));
        if (!isset($found[$pid])) {
            continue;
        }
        $price   = (int)$found[$pid]['price'];
        $total  += $price * $qty;
        $items[] = [$pid, $found[$pid]['name'], $price, $qty];
    }

    if (!$items) {
        echo json_encode(['success' => false, 'message' => 'Produk tidak tersedia']);
        exit;
    }

    $orderNumber = generateOrderNumber();
    $db->beginTransaction();

    $db->prepare('INSERT INTO orders (order_number, user_id, customer_name, total) VALUES (?, ?, ?, ?)')
       ->execute([$orderNumber, $_SESSION['user_id'] ?? null, $name, $total]);
    $orderId = (int)$db->lastInsertId();

    $ins = $db->prepare('INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)');
    foreach ($items as [$pid, $pname, $price, $qty]) {
        $ins->execute([$orderId, $pid, $pname, $price, $qty]);
    }
    $db->commit();

    $_SESSION['cart'] = [];
    echo json_encode(['success' => true, 'order_number' => $orderNumber, 'total' => $total]);
} catch (Throwable $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log('checkout.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Pesanan tidak dapat disimpan']);
}
