<?php
require_once __DIR__ . '/config.php';

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function current_user() {
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'name' => $_SESSION['user_name'] ?? null,
        'email' => $_SESSION['user_email'] ?? null,
        'is_admin' => $_SESSION['is_admin'] ?? false,
    ];
}

function require_login() {
    if (!is_logged_in()) {
        redirect('login.php?redirect=' . urlencode($_SERVER['REQUEST_URI'] ?? 'checkout.php'));
    }
}

function require_admin() {
    if (!is_logged_in() || empty($_SESSION['is_admin'])) {
        redirect('../login.php');
    }
}

function cart_count() {
    $count = 0;
    foreach ($_SESSION['cart'] ?? [] as $qty) {
        $count += (int)$qty;
    }
    return $count;
}

function cart_items($conn) {
    $cart = $_SESSION['cart'] ?? [];
    if (!$cart) return [];

    $ids = array_keys($cart);
    $ids = array_map('intval', $ids);
    $idList = implode(',', $ids);
    $result = $conn->query("SELECT * FROM products WHERE id IN ($idList) AND is_active = 1 ORDER BY id DESC");
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $row['quantity'] = (int)($cart[$row['id']] ?? 0);
        if ($row['quantity'] > 0) {
            $row['line_total'] = $row['price'] * $row['quantity'];
            $items[] = $row;
        }
    }
    return $items;
}

function cart_total($conn) {
    $total = 0;
    foreach (cart_items($conn) as $item) {
        $total += $item['line_total'];
    }
    return $total;
}

function flash($key, $message = null, $type = 'success') {
    if ($message !== null) {
        $_SESSION['flash'][$key] = ['message' => $message, 'type' => $type];
        return;
    }
    if (!empty($_SESSION['flash'][$key])) {
        $value = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $value;
    }
    return null;
}
?>
