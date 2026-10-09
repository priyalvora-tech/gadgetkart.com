<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Add item to cart
    if ($action === 'add') {
        $id  = (int)($_POST['product_id'] ?? 0);
        $qty = max(1, (int)($_POST['quantity'] ?? 1));

        $st = $conn->prepare('SELECT id, stock, is_active FROM products WHERE id = ? LIMIT 1');
        $st->bind_param('i', $id);
        $st->execute();
        $p = $st->get_result()->fetch_assoc();

        if ($p && $p['is_active'] && $p['stock'] > 0) {
            $current = (int)($_SESSION['cart'][$id] ?? 0);
            $_SESSION['cart'][$id] = min($current + $qty, (int)$p['stock']);
            flash('notice', 'Product added to your cart.');
        } else {
            flash('notice', 'This product is unavailable.', 'error');
        }

        redirect($_SERVER['HTTP_REFERER'] ?? 'products.php');
    }

    // Remove item from cart
    if ($action === 'remove') {
        $id = (int)$_POST['product_id'];
        unset($_SESSION['cart'][$id]);
        flash('notice', 'Product removed from your cart.');
        redirect('cart.php');
    }

    // Update cart item quantities
    if ($action === 'update') {
        foreach ($_POST['qty'] ?? [] as $id => $qty) {
            $id  = (int)$id;
            $qty = (int)$qty;

            if ($qty <= 0) {
                unset($_SESSION['cart'][$id]);
                continue;
            }

            $st = $conn->prepare('SELECT stock FROM products WHERE id = ?');
            $st->bind_param('i', $id);
            $st->execute();
            $r = $st->get_result()->fetch_assoc();

            if ($r) {
                $_SESSION['cart'][$id] = min($qty, (int)$r['stock']);
            }
        }

        flash('notice', 'Cart updated.');
        redirect('cart.php');
    }
}

$items     = cart_items($conn);
$total     = cart_total($conn);
$pageTitle = 'Shopping Cart | GadgetKart';
$basePath  = '';

require 'includes/header.php';
?>

<!-- Page Banner -->
<section class="page-banner">
    <div class="container">
        <span class="eyebrow">YOUR CART</span>
        <h1>Shopping cart</h1>
        <p>Review your selected gadgets before checkout.</p>
    </div>
</section>

<!-- Cart Section -->
<section class="section">
    <div class="container cart-layout">
        <!-- Main Cart Content -->
        <div class="cart-main">
            <?php if (!$items): ?>
                <div class="empty-state">
                    <div>🛒</div>
                    <h2>Your cart is empty</h2>
                    <p>Add a few gadgets and come back here.</p>
                    <a class="btn" href="products.php">Start Shopping</a>
                </div>
            <?php else: ?>
                <form method="post">
                    <input type="hidden" name="action" value="update">

                    <div class="cart-table">
                        <div class="cart-head">
                            <span>Product</span>
                            <span>Price</span>
                            <span>Quantity</span>
                            <span>Total</span>
                            <span></span>
                        </div>

                        <?php foreach ($items as $item): ?>
                            <div class="cart-row">
                                <div class="cart-product">
                                    <img src="assets/images/<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>">
                                    <div>
                                        <a href="product.php?id=<?= (int)$item['id'] ?>">
                                            <strong><?= e($item['name']) ?></strong>
                                        </a>
                                        <small><?= e($item['subcategory']) ?></small>
                                    </div>
                                </div>

                                <span>₹<?= number_format($item['price'], 2) ?></span>

                                <input 
                                    class="qty-input" 
                                    type="number" 
                                    name="qty[<?= (int)$item['id'] ?>]" 
                                    min="0" 
                                    max="<?= (int)$item['stock'] ?>" 
                                    value="<?= (int)$item['quantity'] ?>"
                                >

                                <strong>₹<?= number_format($item['line_total'], 2) ?></strong>

                                <button 
                                    class="remove-btn" 
                                    type="submit" 
                                    form="remove-<?= (int)$item['id'] ?>"
                                    title="Remove item"
                                >&times;</button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button class="btn btn-light" type="submit">Update Cart</button>
                </form>

                <!-- Hidden removal forms -->
                <?php foreach ($items as $item): ?>
                    <form id="remove-<?= (int)$item['id'] ?>" method="post" style="display: none;">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>">
                    </form>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Order Summary Sidebar -->
        <?php if ($items): ?>
            <aside class="summary-card">
                <span class="eyebrow">ORDER SUMMARY</span>
                <h2>Checkout</h2>

                <div class="summary-line">
                    <span>Subtotal</span>
                    <strong>₹<?= number_format($total, 2) ?></strong>
                </div>

                <div class="summary-line">
                    <span>Delivery</span>
                    <strong>Free</strong>
                </div>

                <hr>

                <div class="summary-total">
                    <span>Total</span>
                    <strong>₹<?= number_format($total, 2) ?></strong>
                </div>

                <a class="btn full" href="checkout.php">Proceed to Checkout</a>
                <p class="mini-note">Login is required before placing an order.</p>
            </aside>
        <?php endif; ?>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
