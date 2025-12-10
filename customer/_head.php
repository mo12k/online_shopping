<!DOCTYPE html>
<?php

$arr = glob('/../../images/profile/*.jpg');
$arr = array_map('basename', $arr);

$profile_pic = $_SESSION['profile_picture'] ?? 'default_pic.jpg';
?>

<html>
<head>
<meta charset="UTF-8">
<title><?= $_title ?? 'Test' ?></title>
<link rel="shortcut icon" href="/images/favicon.png">
<link href='https://cdn.boxicons.com/3.0.5/fonts/basic/boxicons.min.css' rel='stylesheet'>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="/admin/js/app.js"></script>
<link rel="stylesheet" href="../../css/app.css">

</head>
<header>
    <div class="header-left">
        <h1>Welcome to the Bookstore</h1>
        <nav class="navbar">
            <a href="/">Index</a>
            <a href="/customer/page/product.php">Product</a>
            <a href="/customer/cart/cart.php">shopping_cart</a>
        </nav>
    </div>
    <nav class="navbar">
    <?php if (!($_SESSION['customer_username'] ?? false)): ?>
        <div class="header-right">
            <ul>
                <li><a href="/admin/index.php">Admin Log in</a> </li>
                <li><a href="/page/register.php">Sign up</a> </li>
                <li><a href="/page/login.php">Log in</a> </li>
            </ul>
        </div>
    </nav>
    <?php endif; ?>

    <?php if ($_SESSION['customer_username'] ?? false): ?>
        <div class="header-right">
            <img src="/images/profile/<?= htmlspecialchars($profile_pic) ?>" alt="Profile">
            <span class="username"><?= htmlspecialchars($_SESSION['customer_username']) ?></span>
            |
            <a href="/page/logout.php">Logout</a>
        </div>
    <?php endif; ?>


</header>




