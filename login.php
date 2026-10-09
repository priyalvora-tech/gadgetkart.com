<?php
require_once 'includes/functions.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error      = '';
$redirectTo = $_GET['redirect'] ?? ($_POST['redirect'] ?? 'index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $st = $conn->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $st->bind_param('s', $email);
    $st->execute();
    $user = $st->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['is_admin']  = (bool)$user['is_admin'];

        redirect($redirectTo);
    }

    $error = 'Invalid email or password.';
}

$pageTitle = 'Login | GadgetKart';
$basePath  = '';

require 'includes/header.php';
?>

<!-- Authentication Section -->
<section class="auth-section">
    <div class="auth-card">
        <div class="auth-icon">🔐</div>
        <h1>Welcome back</h1>
        <p>Login to continue shopping and place orders.</p>

        <?php if ($error): ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="redirect" value="<?= e($redirectTo) ?>">

            <label>
                Email
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Enter your email" 
                    required 
                    autocomplete="email"
                    value="<?= e($_POST['email'] ?? '') ?>"
                >
            </label>

            <label>
                Password
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Enter your password" 
                    required 
                    autocomplete="current-password"
                >
            </label>

            <button class="btn full" type="submit">Login</button>
        </form>

        <p class="auth-switch">
            New here? <a href="register.php">Create an account</a>
        </p>

        <div class="demo-box">
            <strong>Demo Credentials</strong>
            <span>Admin: admin@gadgetkart.local / Admin@123</span>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>