<?php
require_once 'includes/functions.php'; $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
$name=trim($_POST['name']??'');$email=trim($_POST['email']??'');$message=trim($_POST['message']??'');
if(strlen($name)<2 || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($message)<10){$error='Please fill all fields correctly.';}else{$st=$conn->prepare('INSERT INTO contacts(name,email,message) VALUES(?,?,?)');$st->bind_param('sss',$name,$email,$message);$st->execute();flash('notice','Thanks! Your message has been saved.');redirect('contact.php');}}
$pageTitle='Contact | GadgetKart';$basePath='';require 'includes/header.php';
?>
<section class="page-banner"><div class="container"><span class="eyebrow">NEED HELP?</span><h1>Contact GadgetKart</h1><p>Send a question, suggestion or project demo inquiry.</p></div></section>
<section class="section"><div class="container contact-grid"><div><h2>Let’s talk gadgets</h2><p class="lead">This demo contact form stores enquiries in MySQL using PHP.</p><div class="contact-points"><div>📧 support@gadgetkart.local</div><div>📞 +91 98765 43210</div><div>🕘 Mon–Sat · 10:00 AM–8:00 PM</div></div></div><div class="form-card"><h2>Send a message</h2><?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><form id="contactForm" method="post"><label>Name<input type="text" name="name" required minlength="2"></label><label>Email<input type="email" name="email" required></label><label>Message<textarea name="message" rows="6" required minlength="10"></textarea></label><button class="btn" type="submit">Send Message</button></form></div></div></section>
<?php require 'includes/footer.php'; ?>
