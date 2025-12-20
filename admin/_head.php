<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel </title>
    <link rel="shortcut icon" href="/images/book.png">
    <link rel="stylesheet" href="/admin/css/app.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href='https://cdn.boxicons.com/3.0.5/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/admin/js/app.js"></script>
</head>
<body class="<?= htmlspecialchars($_body_class ?? '') ?>">

<?php if (isset($_SESSION['admin_id'])): ?>
    <?php 
        $admin_id = $_SESSION['admin_id'];
        $stm = $_db->prepare('SELECT * FROM admin WHERE admin_id = ? ');
        $stm->execute([$admin_id]);
        $admin = $stm->fetch();
        $profile_pic = $_SESSION['profile_pic'] = $admin->photo ?: 'default_pic.jpg';
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

            <li class="<?= $current == 'category' ? 'active' : '' ?>">
                <a href="/admin/page/category.php">Category List</a>
            </li>

            <li class="<?= $current == 'order' ? 'active' : '' ?>">
                <a href="/admin/page/order.php">Order List</a>
            </li>
            <li class="<?= $current == 'report' ? 'active' : '' ?>">
                <a href="/admin/page/report.php">Report List</a>
            </li>
            
        </ul>
    </div>


<main>
        
        <div class="header">
            <h2><?= $_title ?></h2>
            <div class="user-info">
            <img 
            src="/admin/images/profile/<?= $profile_pic ?>" 
            alt="<?= htmlspecialchars($_SESSION['admin_username']) ?>'s Profile"
            class="img"
            onclick="window.location.href='/admin/page/adminprofile.php'"
            style="cursor:pointer;"
            >
            <a href="/admin/page/adminprofile.php">
                <span><?= htmlspecialchars($_SESSION['admin_username']) ?></span>
                <a href="/admin/page/adminlogout.php" class="logout">Logout</a>
            </div>
        </div>
        <?php endif; ?>