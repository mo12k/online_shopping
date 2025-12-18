<?php

$arr = glob('/../images/profile/*.jpg');
$arr = array_map('basename', $arr);

$profile_pic = $_SESSION['profile_picture'] ?? 'default_pic.jpg';
?>
<link rel="stylesheet" href="/css/app.css">
<header>
    <div class="header-left">
        <h1>Welcome to PaperNest Bookstore</h1>
        <nav class="navbar">
            <a href="/">Home</a>
            <a href="/customer/page/product.php">Product</a>
            <a href="/customer/page/cart.php">Cart</a>
            <a href="/customer/page/order_history.php">History</a>
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
