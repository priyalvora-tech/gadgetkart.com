<?php
require_once 'includes/functions.php';

$id   = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare('SELECT * FROM products WHERE id = ? AND is_active = 1 LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Product Not Found | GadgetKart';
    $basePath  = '';
    require 'includes/header.php';
    ?>
    <section class="section">
        <div class="container empty-state">
            <div>📦</div>
            <h1>Product not found</h1>
            <p>The product you requested does not exist or is no longer available.</p>
            <a class="btn" href="products.php">Back to Products</a>
        </div>
    </section>
    <?php
    require 'includes/footer.php';
    exit;
}

$pageTitle = $product['name'] . ' | GadgetKart';
$basePath  = '';

require 'includes/header.php';
?>

<!-- Product Details Section -->
<section class="section">
    <div class="container detail-grid">
        <!-- Product Image -->
        <div class="detail-image">
            <img src="assets/images/<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
        </div>

        <!-- Product Copy & Action -->
        <div class="detail-copy">
            <span class="pill">
                <?= e($product['category']) ?> / <?= e($product['subcategory']) ?>
            </span>

            <h1><?= e($product['name']) ?></h1>

            <div class="big-price">₹<?= number_format($product['price'], 2) ?></div>

            <p class="lead"><?= e($product['description']) ?></p>

            <div class="stock <?= $product['stock'] > 0 ? 'in' : 'out' ?>">
                <?= $product['stock'] > 0 
                    ? '● In stock &mdash; ' . (int)$product['stock'] . ' available' 
                    : '● Out of stock' ?>
            </div>

            <?php if ($product['stock'] > 0): ?>
                <form class="add-form" method="post" action="cart.php">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">

                    <label>
                        Quantity
                        <input 
                            type="number" 
                            name="quantity" 
                            min="1" 
                            max="<?= (int)$product['stock'] ?>" 
                            value="1"
                        >
                    </label>

                    <button class="btn" type="submit">Add to Cart</button>
                </form>
            <?php endif; ?>

            <div class="feature-list">
                <div>✓ Quality-tested accessory</div>
                <div>✓ Simple returns workflow</div>
                <div>✓ Responsive shopping experience</div>
            </div>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
