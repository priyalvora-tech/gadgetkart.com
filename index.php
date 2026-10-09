<?php
$pageTitle = 'GadgetKart | Smart Accessories Store';
$basePath  = '';

require_once 'includes/functions.php';

$featured = $conn->query(
    "SELECT * FROM products WHERE is_featured = 1 AND is_active = 1 ORDER BY id DESC LIMIT 8"
);

require 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero" id="heroSection">
    <div class="container hero-grid">
        <div class="hero-copy">
            <span class="eyebrow fade-in-down">TECH MADE SIMPLE</span>
            <h1 class="fade-in-up delay-1">Power up your everyday with <span>GadgetKart.</span></h1>
            <p class="fade-in-up delay-2">
                Shop useful mobile, computer and gaming accessories with simple browsing, quick filtering and an easy cart experience.
            </p>
            <div class="hero-cta fade-in-up delay-3">
                <a class="btn" href="products.php">Shop Products</a>
                <a class="btn btn-light" href="#categories">Explore Categories</a>
            </div>
            <div class="trust-row fade-in-up delay-4">
                <span>✓ Easy shopping</span>
                <span>✓ Responsive design</span>
                <span>✓ Secure login</span>
            </div>
        </div>

        <div class="hero-image-wrap" id="heroImageWrap">
            <img 
                src="assets/images/hero-keyboard.png" 
                alt="Gaming Keyboard" 
                class="hero-keyboard-img idle-float" 
                id="heroKeyboardImg"
            >
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="section" id="categories">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">SHOP BY CATEGORY</span>
                <h2>Everything you need</h2>
            </div>
            <a href="products.php">View all &rarr;</a>
        </div>

        <div class="category-grid">
            <a class="category-card mobile" href="products.php?category=Mobile%20Accessories">
                <span>📱</span>
                <h3>Mobile Accessories</h3>
                <p>Chargers, cables, power banks &amp; cases</p>
            </a>
            <a class="category-card computer" href="products.php?category=Computer%20Accessories">
                <span>💻</span>
                <h3>Computer Accessories</h3>
                <p>Mouse, keyboards, webcams &amp; USB hubs</p>
            </a>
            <a class="category-card gaming" href="products.php?category=Gaming">
                <span>🎮</span>
                <h3>Gaming</h3>
                <p>Headsets, controllers &amp; gaming gear</p>
            </a>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="section soft">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">FEATURED PRODUCTS</span>
                <h2>Popular picks</h2>
            </div>
            <a href="products.php">See catalogue &rarr;</a>
        </div>

        <div class="product-grid">
            <?php while ($p = $featured->fetch_assoc()): ?>
                <article class="product-card">
                    <a href="product.php?id=<?= (int)$p['id'] ?>" class="product-image">
                        <img src="assets/images/<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>">
                        <span><?= e($p['category']) ?></span>
                    </a>
                    <div class="product-body">
                        <h3>
                            <a href="product.php?id=<?= (int)$p['id'] ?>"><?= e($p['name']) ?></a>
                        </h3>
                        <p><?= e($p['short_description']) ?></p>
                        <div class="price-row">
                            <strong>₹<?= number_format($p['price'], 2) ?></strong>
                            <form method="post" action="cart.php">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                <button class="icon-btn" title="Add to cart" type="submit">+</button>
                            </form>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Highlights / Stats Band -->
<section class="section">
    <div class="container stats-band">
        <div>
            <strong>24+</strong>
            <span>Products</span>
        </div>
        <div>
            <strong>3</strong>
            <span>Categories</span>
        </div>
        <div>
            <strong>100%</strong>
            <span>Responsive UI</span>
        </div>
        <div>
            <strong>PHP + MySQL</strong>
            <span>Backend</span>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
