<?php
require_once 'includes/functions.php'; session_unset(); session_destroy(); session_start(); flash('notice','You have been logged out.'); redirect('index.php');
?>
