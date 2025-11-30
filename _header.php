<?php

$arr = glob('/../images/profile/*.jpg');
$arr = array_map('basename', $arr);

$profile_pic = $_SESSION['profile_picture'] ?? 'default_pic.jpg';
?>
<header>
    <div class="header-left">
        <h1>Welcome to the Bookstore</h1>
        <nav>
            <a href="/">Index</a>
            <a href="/page/demo1.php">Demo 1</a>
            <a href="/page/demo2.php">Demo 2</a>
        </nav>
    </div>

    <?php if ($_SESSION['customer_username'] ?? false): ?>
        <div class="header-right">
            <img src="/images/profile/<?= htmlspecialchars($profile_pic) ?>" alt="Profile">
            <span class="username"><?= htmlspecialchars($_SESSION['customer_username']) ?></span>
            |
            <a href="/page/logout.php">Logout</a>
        </div>
    <?php endif; ?>


</header>
