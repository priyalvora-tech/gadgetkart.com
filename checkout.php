<?php
require_once 'includes/functions.php'; require_login(); $items=cart_items($conn); if(!$items) redirect('cart.php');
$error=''; $total=cart_total($conn);
if($_SERVER['REQUEST_METHOD']==='POST'){
    $address=trim($_POST['address']??'');$city=trim($_POST['city']??'');$phone=trim($_POST['phone']??'');
    if(strlen($address)<10 || strlen($city)<2 || strlen($phone)<10){$error='Please provide a complete delivery address and valid phone number.';}
    else{
        $conn->begin_transaction();
        try{
            $st=$conn->prepare('INSERT INTO orders(user_id,total_amount,address,city,phone,status) VALUES(?,?,?,?,?,\'Placed\')');$uid=$_SESSION['user_id'];$st->bind_param('idsss',$uid,$total,$address,$city,$phone);$st->execute();$orderId=$conn->insert_id;
            $stItem=$conn->prepare('INSERT INTO order_items(order_id,product_id,quantity,price) VALUES(?,?,?,?)');
            foreach($items as $item){$pid=$item['id'];$qty=$item['quantity'];$price=$item['price'];$stItem->bind_param('iiid',$orderId,$pid,$qty,$price);$stItem->execute();$up=$conn->prepare('UPDATE products SET stock=GREATEST(stock-?,0) WHERE id=?');$up->bind_param('ii',$qty,$pid);$up->execute();}
            $conn->commit(); $_SESSION['cart']=[]; redirect('order_success.php?id='.$orderId);
        }catch(Throwable $e){$conn->rollback();$error='We could not place the order. Please try again.';}
    }
}
$pageTitle='Checkout | GadgetKart';$basePath='';require 'includes/header.php';
?>
<section class="page-banner"><div class="container"><span class="eyebrow">CHECKOUT</span><h1>Complete your order</h1><p>Enter delivery details to place your GadgetKart order.</p></div></section>
<section class="section"><div class="container checkout-grid"><div class="form-card"><h2>Delivery Details</h2><?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><form method="post"><label>Phone number<input type="tel" name="phone" pattern="[0-9]{10}" required value="<?= e($_POST['phone']??'') ?>"></label><label>Address<textarea name="address" rows="5" required><?= e($_POST['address']??'') ?></textarea></label><label>City<input type="text" name="city" required value="<?= e($_POST['city']??'') ?>"></label><button class="btn full" type="submit">Place Order · ₹<?= number_format($total,2) ?></button></form></div><aside class="summary-card"><span class="eyebrow">YOUR ITEMS</span><?php foreach($items as $item): ?><div class="mini-item"><span><?= e($item['name']) ?> × <?= (int)$item['quantity'] ?></span><strong>₹<?= number_format($item['line_total'],2) ?></strong></div><?php endforeach; ?><hr><div class="summary-total"><span>Total</span><strong>₹<?= number_format($total,2) ?></strong></div></aside></div></section>
<?php require 'includes/footer.php'; ?>
