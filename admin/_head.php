<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($_title ?? 'Admin Panel') ?></title>
    <link rel="shortcut icon" href="/images/favicon.png">
    <link rel="stylesheet" href="/admin/css/app.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href='https://cdn.boxicons.com/3.0.5/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/admin/js/app.js"></script>
</head>
<body class="<?= htmlspecialchars($_body_class ?? '') ?>">

<?php if (isset($_SESSION['admin_id'])): ?>
    <?php
        $profile_pic = glob('/../admin/images/profile/*.jpg');
        $profile_pic = array_map('basename', $profile_pic);
    ?>  
    <div class="sidebar">
        <ul>
            <li class="<?= $current == 'dashboard' ? 'active' : '' ?>">
                <a href="/admin/page/dashboard.php">Dashboard</a>
            </li>
            <li class="<?= $current == 'customer' ? 'active' : '' ?>">
                <a href="/admin/page/customer.php">Customer List</a>
            </li>
            <li class="<?= $current == 'product' ? 'active' : '' ?>">
                <a href="/admin/page/product.php">Product List</a>
            </li>
            <li class="<?= $current == 'order' ? 'active' : '' ?>">
                <a href="/admin/page/order.php">Order List</a>
            </li>
            <li class="<?= $current == 'staff' ? 'active' : '' ?>">
                <a href="/admin/page/staff.php">Staff List</a>
            </li>
        </ul>
    </div>


    <main>
        <div class="header">
            <h2><?= htmlspecialchars($_title) ?></h2>
            <div class="user-info">
                <img src="/admin/images/profile/<?= htmlspecialchars($_SESSION['profile_picture']) ?>" alt="Profile">
                <span><?= htmlspecialchars($_SESSION['admin_username']) ?></span>
                <a href="/admin/auth/logout.php" class="logout">Logout</a>
            </div>
        </div>
<?php endif; ?>