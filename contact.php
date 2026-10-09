<?php
require_once 'includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($message) < 10) {
        $error = 'Please fill all fields correctly. Message must be at least 10 characters.';
    } else {
        $st = $conn->prepare('INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)');
        $st->bind_param('sss', $name, $email, $message);
        $st->execute();

        flash('notice', 'Thanks! Your message has been saved.');
        redirect('contact.php');
    }
}

$pageTitle = 'Contact | GadgetKart';
$basePath  = '';

require 'includes/header.php';
?>

<!-- Page Banner -->
<section class="page-banner">
    <div class="container">
        <span class="eyebrow">NEED HELP?</span>
        <h1>Contact GadgetKart</h1>
        <p>Send a question, suggestion or project demo inquiry.</p>
    </div>
</section>

<!-- Contact Form Section -->
<section class="section">
    <div class="container contact-grid">
        <!-- Contact Information -->
        <div>
            <h2>Let’s talk gadgets</h2>
            <p class="lead">This demo contact form stores enquiries in MySQL using PHP.</p>
            <div class="contact-points">
                <div>📧 support@gadgetkart.local</div>
                <div>📞 +91 92270 07307</div>
                <div>🕘 Mon–Sat &middot; 10:00 AM–8:00 PM</div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <h2>Send a message</h2>

            <?php if ($error): ?>
                <div class="alert error"><?= e($error) ?></div>
            <?php endif; ?>

            <form id="contactForm" method="post">
                <label>
                    Name
                    <input 
                        type="text" 
                        name="name" 
                        placeholder="Your name" 
                        required 
                        minlength="2"
                        value="<?= e($_POST['name'] ?? '') ?>"
                    >
                </label>

                <label>
                    Email
                    <input 
                        type="email" 
                        name="email" 
                        placeholder="Your email" 
                        required
                        value="<?= e($_POST['email'] ?? '') ?>"
                    >
                </label>

                <label>
                    Message
                    <textarea 
                        name="message" 
                        rows="6" 
                        placeholder="Write your message here (min. 10 characters)..." 
                        required 
                        minlength="10"
                    ><?= e($_POST['message'] ?? '') ?></textarea>
                </label>

                <button class="btn" type="submit">Send Message</button>
            </form>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
