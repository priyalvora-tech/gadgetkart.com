<?php
/**
 * GadgetKart - Helper Functions & Session Utilities
 */

require_once __DIR__ . '/config.php';

/**
 * Escapes HTML entities for safe output.
 */
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirects the user to a given URL and halts execution.
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Checks whether the current user is authenticated.
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Retrieves the current authenticated user's details.
 */
function current_user() {
    return [
        'id'       => $_SESSION['user_id'] ?? null,
        'name'     => $_SESSION['user_name'] ?? null,
        'email'    => $_SESSION['user_email'] ?? null,
        'is_admin' => $_SESSION['is_admin'] ?? false,
    ];
}

/**
 * Enforces authentication; redirects to login page if unauthenticated.
 */
function require_login() {
    if (!is_logged_in()) {
        redirect('login.php?redirect=' . urlencode($_SERVER['REQUEST_URI'] ?? 'checkout.php'));
    }
}

/**
 * Enforces admin authorization; redirects unprivileged users.
 */
function require_admin() {
    if (!is_logged_in() || empty($_SESSION['is_admin'])) {
        redirect('../login.php');
    }
}

/**
 * Returns the total count of items across all products in the cart.
 */
function cart_count() {
    $count = 0;
    foreach ($_SESSION['cart'] ?? [] as $qty) {
        $count += (int)$qty;
    }
    return $count;
}

/**
 * Retrieves full product records and quantities for items in the cart.
 */
function cart_items($conn) {
    $cart = $_SESSION['cart'] ?? [];
    if (!$cart) {
        return [];
    }

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

/**
 * Computes the grand total amount for items in the cart.
 */
function cart_total($conn) {
    $total = 0;
    foreach (cart_items($conn) as $item) {
        $total += $item['line_total'];
    }
    return $total;
}

/**
 * Sets or retrieves flash notifications stored in the session.
 */
function flash($key, $message = null, $type = 'success') {
    if ($message !== null) {
        $_SESSION['flash'][$key] = [
            'message' => $message,
            'type'    => $type,
        ];
        return;
    }

    if (!empty($_SESSION['flash'][$key])) {
        $value = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $value;
    }

    return null;
}

/**
 * Lists existing product image asset filenames from assets/images directory.
 */
function get_available_asset_images() {
    $dir = realpath(__DIR__ . '/../assets/images');
    if (!$dir || !is_dir($dir)) {
        return [];
    }
    $files = scandir($dir);
    $images = [];
    $validExts = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, $validExts, true)) {
            $images[] = $file;
        }
    }
    sort($images);
    return $images;
}

/**
 * Safely handles product image upload by an authorized administrator.
 * Validates file type, size, MIME type, generates a persistent filename,
 * and saves the file directly into assets/images/.
 *
 * @param array|null $file The $_FILES['image_file'] array
 * @param string|null &$error Receives any error message
 * @return string|null The saved filename or null on failure / no file
 */
function handle_product_image_upload($file, &$error = null) {
    // 1. Strict admin-only access check
    if (!is_logged_in() || empty($_SESSION['is_admin'])) {
        $error = 'Access denied: Only administrators are authorized to upload product images.';
        return null;
    }

    // 2. Check if a file was actually submitted
    if (!isset($file) || !is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    // 3. Handle upload error codes
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = match ($file['error']) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'The uploaded image exceeds the maximum permitted file size (8MB).',
            UPLOAD_ERR_PARTIAL   => 'The image was only partially uploaded. Please try again.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server error: Temporary upload directory is missing.',
            UPLOAD_ERR_CANT_WRITE => 'Server error: Failed to write uploaded file to disk.',
            default              => 'Upload failed with error code ' . $file['error'] . '.',
        };
        return null;
    }

    // 4. File size limit (8 MB)
    $maxBytes = 8 * 1024 * 1024;
    if ($file['size'] > $maxBytes) {
        $error = 'The image file is too large. Maximum allowed size is 8MB.';
        return null;
    }

    // 5. Allowed image extensions
    $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
    $originalName = (string)($file['name'] ?? '');
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExts, true)) {
        $error = 'Invalid file extension. Please upload a PNG, JPG, JPEG, WEBP, GIF, or SVG image.';
        return null;
    }

    // 6. Validate MIME type
    $tmpPath = $file['tmp_name'];
    if (!is_uploaded_file($tmpPath)) {
        $error = 'Invalid upload request detected.';
        return null;
    }

    $mime = '';
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mime = (string)finfo_file($finfo, $tmpPath);
            finfo_close($finfo);
        }
    } elseif (function_exists('mime_content_type')) {
        $mime = (string)mime_content_type($tmpPath);
    } else {
        $mime = (string)($file['type'] ?? '');
    }

    $allowedMimes = [
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/x-png',
        'image/webp',
        'image/gif',
        'image/svg+xml',
        'image/svg',
        'text/xml',
        'text/plain', // some systems report SVG as text/xml or text/plain
    ];

    if (!in_array($mime, $allowedMimes, true)) {
        $error = 'File verification failed. Please select a valid image file.';
        return null;
    }

    // 7. Target directory (assets/images/)
    $targetDir = realpath(__DIR__ . '/../assets/images');
    if (!$targetDir || !is_dir($targetDir)) {
        $targetDir = __DIR__ . '/../assets/images';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
    }

    // 8. Generate safe, persistent unique filename
    $rawBase = pathinfo($originalName, PATHINFO_FILENAME);
    $cleanBase = preg_replace('/[^a-zA-Z0-9_-]/', '-', strtolower($rawBase));
    $cleanBase = trim(preg_replace('/-+/', '-', (string)$cleanBase), '-');
    if ($cleanBase === '') {
        $cleanBase = 'product';
    }

    $uniqueSuffix = date('Ymd-His') . '-' . substr(bin2hex(random_bytes(3)), 0, 6);
    $finalFilename = $cleanBase . '-' . $uniqueSuffix . '.' . $ext;
    $destination = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $finalFilename;

    // 9. Persist file to storage
    if (!move_uploaded_file($tmpPath, $destination)) {
        $error = 'Could not save the image to the assets directory. Please check file permissions.';
        return null;
    }

    return $finalFilename;
}

