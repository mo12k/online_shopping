<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= $_title ?? 'Test' ?></title>
<link rel="shortcut icon" href="/images/favicon.png">
<link rel="stylesheet" href="/admin/css/app.css">
<link href='https://cdn.boxicons.com/3.0.5/fonts/basic/boxicons.min.css' rel='stylesheet'>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="/admin/js/app.js"></script>
</head>
<body>

<div class="sidebar">
    

    <ul>
        <li class="<?= $current == 'user' ? 'active' : '' ?>">
        <a href="/admin/page/customer.php">User</a>
        </li> 
        
        <li class="<?= $current == 'dashboard' ? 'active' : '' ?>">
        <a href="/admin/page/dashboard.php">Dashboard</a>
        </li>

        <li class="<?= $current == 'customer' ? 'active' : '' ?>">
        <a href="/admin/page/customer.php" >Customer List</a>
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
            <h2><?= $_title ?? 'Untitled' ?></h2>
            <div class="logout">
                <a href="/index.php">Logout</a>
            </div>
</div>

