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
                <li><a href="/admin/page/dashboard.php">Admin Log in</a> </li>
                <li><a href="/page/register.php">Sign up</a> </li>
                <li><a href="/page/login.php">Log in</a> </li>
            </ul>
        </div>
    </nav>
    <?php endif; ?>


    <?php if ($_SESSION['customer_username'] ?? false): ?>
        <div class="header-right">
            <img 
            src="/images/profile/<?= $profile_pic ?>" 
            alt="<?= htmlspecialchars($_SESSION['customer_username']) ?>'s Profile"
            class="img"
            onclick="window.location.href='/customer/page/profile.php'"
            style="cursor:pointer;"
            >
            
            <a href="/customer/page/profile.php">
                <span class="username"><?= htmlspecialchars($_SESSION['customer_username']) ?></span>
            </a>
            |
            <a href="/page/logout.php">Logout</a>
        </div>
    </nav>
    <?php endif; ?>

</header>