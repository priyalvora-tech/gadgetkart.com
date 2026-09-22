<?php
require_once 'includes/functions.php'; if(is_logged_in()) redirect('index.php');
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name=trim($_POST['name']??'');$email=trim($_POST['email']??'');$password=$_POST['password']??'';$confirm=$_POST['confirm_password']??'';
    if(strlen($name)<2 || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($password)<6 || $password!==$confirm){$error='Please enter valid details. Password must be at least 6 characters and match confirmation.';}
    else { $st=$conn->prepare('SELECT id FROM users WHERE email=?');$st->bind_param('s',$email);$st->execute();if($st->get_result()->num_rows){$error='An account with this email already exists.';}else{$hash=password_hash($password,PASSWORD_DEFAULT);$st=$conn->prepare('INSERT INTO users(name,email,password) VALUES(?,?,?)');$st->bind_param('sss',$name,$email,$hash);$st->execute();flash('notice','Registration successful. Please login.');redirect('login.php');}}
}
$pageTitle='Register | GadgetKart';$basePath='';require 'includes/header.php';
?>
<section class="auth-section"><div class="auth-card"><div class="auth-icon">✨</div><h1>Create account</h1><p>Join GadgetKart for a smoother checkout experience.</p><?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><form id="registerForm" method="post" novalidate><label>Full name<input type="text" name="name" required minlength="2" value="<?= e($_POST['name']??'') ?>"></label><label>Email<input type="email" name="email" required value="<?= e($_POST['email']??'') ?>"></label><label>Password<input type="password" name="password" id="regPassword" required minlength="6"></label><label>Confirm password<input type="password" name="confirm_password" required minlength="6"></label><label class="check"><input type="checkbox" id="showPass"> Show password</label><button class="btn full" type="submit">Create Account</button></form><p class="auth-switch">Already registered? <a href="login.php">Login here</a></p></div></section>
<?php require 'includes/footer.php'; ?>
