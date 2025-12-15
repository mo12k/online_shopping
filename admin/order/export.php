<?php
require '../_base.php';
admin_require_login();

/* =========================
   Get Order ID
========================= */
$order_id = req('id');
if (!$order_id) {
    die('Invalid order ID');
}

/* =========================
   Order + Customer + Address
========================= */
$stm = $_db->prepare("
    SELECT 
        o.order_id,
        o.total_amount,
        o.status,
        o.order_date,

        c.username,
        c.email,

        a.address,
        a.postcode,
        a.city,
        a.state
    FROM orders o
    LEFT JOIN customer c ON o.customer_id = c.customer_id
    LEFT JOIN customer_address a ON o.address_id = a.address_id
    WHERE o.order_id = ?
");
$stm->execute([$order_id]);
$order = $stm->fetch();

if (!$order) {
    die('Order not found');
}

/* =========================
   Order Items
========================= */
$stm = $_db->prepare("
    SELECT 
        p.title,
        oi.quantity,
        oi.price_each,
        oi.subtotal
    FROM order_item oi
    LEFT JOIN product p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stm->execute([$order_id]);
$items = $stm->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order #<?= encode($order->order_id) ?></title>

<style>
    body {
        font-family: Arial, sans-serif;
        background: #fff;
        margin: 30px;
        color: #333;
    }

    h2 {
        margin-bottom: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 25px;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
        font-size: 14px;
    }

    th {
        background: #f5f5f5;
        width: 200px;
    }

    .items th {
        text-align: center;
    }

    .items td {
        text-align: center;
    }

    .total {
        font-size: 18px;
        font-weight: bold;
        text-align: right;
    }

    /* Print button */
    .no-print {
        text-align: right;
        margin-bottom: 20px;
    }

    .no-print button {
        padding: 10px 18px;
        border-radius: 20px;
        border: none;
        background: #3498db;
        color: #fff;
        font-weight: 600;
        cursor: pointer;
    }

    /* Hide on print */
    @media print {
        .no-print {
            display: none;
        }
        body {
            margin: 0;
        }
    }
</style>
</head>

<body>

<!-- Print Button -->
<div class="no-print">
    <button onclick="window.print()">🖨 Print</button>
</div>

<h2>Order #<?= encode($order->order_id) ?></h2>

<!-- Order Info -->
<table>
    <tr>
        <th>Customer</th>
        <td>
            <?= $order->username ?: '(guest)' ?><br>
            <small><?= encode($order->email) ?></small>
        </td>
    </tr>
    <tr>
        <th>Status</th>
        <td><?= ucfirst($order->status) ?></td>
    </tr>
    <tr>
        <th>Order Date</th>
        <td><?= $order->order_date ?></td>
    </tr>
    <tr>
        <th>Shipping Address</th>
        <td>
            <?= encode($order->address) ?><br>
            <?= encode($order->postcode) ?>, <?= encode($order->city) ?><br>
            <?= encode($order->state) ?>
        </td>
    </tr>
</table>

<!-- Items -->
<table class="items">
    <tr>
        <th>Product</th>
        <th width="100">Price</th>
        <th width="80">Qty</th>
        <th width="120">Subtotal</th>
    </tr>

    <?php foreach ($items as $i): ?>
        <tr>
            <td style="text-align:left"><?= encode($i->title) ?></td>
            <td>RM <?= number_format($i->price_each, 2) ?></td>
            <td><?= $i->quantity ?></td>
            <td>RM <?= number_format($i->subtotal, 2) ?></td>
        </tr>
    <?php endforeach; ?>

    <tr>
        <td colspan="3" class="total">TOTAL</td>
        <td class="total">RM <?= number_format($order->total_amount, 2) ?></td>
    </tr>
</table>

</body>
</html>
