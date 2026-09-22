<?php require_once __DIR__ . '/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'GadgetKart') ?></title>
    <meta name="description" content="GadgetKart - Mobile, computer and gaming accessories in one place.">
    <link rel="stylesheet" href="<?= e($basePath ?? '') ?>assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="<?= e($basePath ?? '') ?>index.php">
            <span class="brand-mark">G</span>
            <span>Gadget<span class="brand-accent">Kart</span></span>
        </a>
        <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">☰</button>
        <nav class="main-nav" id="mainNav">
            <a href="<?= e($basePath ?? '') ?>index.php">Home</a>
            <a href="<?= e($basePath ?? '') ?>products.php">Products</a>
            <a href="<?= e($basePath ?? '') ?>products.php?category=Mobile%20Accessories">Mobiles</a>
            <a href="<?= e($basePath ?? '') ?>products.php?category=Computer%20Accessories">Computers</a>
            <a href="<?= e($basePath ?? '') ?>products.php?category=Gaming">Gaming</a>
            <a href="<?= e($basePath ?? '') ?>contact.php">Contact</a>
        </nav>
        <div class="nav-actions">
            <a class="cart-link" href="<?= e($basePath ?? '') ?>cart.php">🛒 <span>Cart</span><b class="cart-badge"><?= cart_count() ?></b></a>
            <?php if (is_logged_in()): ?>
                <div class="user-menu"><span>Hi, <?= e(current_user()['name']) ?></span><a href="<?= e($basePath ?? '') ?>logout.php">Logout</a></div>
                <?php if (!empty($_SESSION['is_admin'])): ?><a class="admin-link" href="<?= e($basePath ?? '') ?>admin/dashboard.php">Admin</a><?php endif; ?>
            <?php else: ?>
                <a class="btn btn-small btn-outline" href="<?= e($basePath ?? '') ?>login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<main>
<?php if ($msg = flash('notice')): ?>
<div class="container"><div class="alert <?= e($msg['type']) ?>"><?= e($msg['message']) ?></div></div>
<?php endif; ?>
