    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div>
                <h3>GadgetKart</h3>
                <p>Your mini destination for mobile, computer and gaming accessories.</p>
            </div>
            <div>
                <h4>Categories</h4>
                <a href="<?= e($basePath ?? '') ?>products.php?category=Mobile%20Accessories">Mobile Accessories</a>
                <a href="<?= e($basePath ?? '') ?>products.php?category=Computer%20Accessories">Computer Accessories</a>
                <a href="<?= e($basePath ?? '') ?>products.php?category=Gaming">Gaming</a>
            </div>
            <div>
                <h4>Support</h4>
                <a href="<?= e($basePath ?? '') ?>contact.php">Contact Us</a>
                <a href="<?= e($basePath ?? '') ?>cart.php">Shopping Cart</a>
                <a href="<?= e($basePath ?? '') ?>login.php">My Account</a>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?= date('Y') ?> GadgetKart. Mini E-Commerce Project.
        </div>
    </footer>

    <script src="<?= e($basePath ?? '') ?>assets/js/script.js?v=<?= @filemtime(__DIR__ . '/../assets/js/script.js') ?>"></script>
</body>
</html>
