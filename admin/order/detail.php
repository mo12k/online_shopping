<?php
require '../_base.php';

admin_require_login();

$current = 'order';
$_title  = 'Order Detail';

$order_id = req('id');
if (!$order_id) {
    redirect('order.php');
}


$stm = $_db->prepare("
    SELECT 
        o.*,
        c.username,
        c.email,
        o.shipping_address,
        o.shipping_postcode,
        o.shipping_city,
        o.shipping_state
    FROM orders o
    LEFT JOIN customer c ON o.customer_id = c.customer_id
    WHERE o.order_id = ?
");
$stm->execute([$order_id]);
$order = $stm->fetch();

if (!$order) {
    temp('info', 'Order not found');
    redirect('order.php');
}


$stm = $_db->prepare("
    SELECT 
        oi.*,
        p.title,
        p.photo_name,
        p.price AS price_each  
    FROM order_item oi
    LEFT JOIN product p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stm->execute([$order_id]);
$items = $stm->fetchAll();


$stm = $_db->prepare("
    SELECT method
    FROM payment
    WHERE order_id = ?
    ORDER BY payment_id 
");
$stm->execute([$order_id]);
$payments = $stm->fetchAll();

include '../_head.php';
?>

<div class="content">
    <div style="margin-top:30px;">
        <a href="../page/order.php" class="btn">← Back to Order List</a>
    </div>

    <h2>Order #<?= encode($order->order_id) ?></h2>

    <table class="product-table" style="margin-bottom:30px;">
        <tr>
            <th width="200">Order ID</th>
            <td><?= encode($order->order_id) ?></td>
        </tr>
        <tr>
            <th>Customer</th>
            <td>
                <?= encode($order->username) ?: '<em style="color:#999;">(guest)</em>' ?>
                <br>
                <small><?= encode($order->email) ?></small>
            </td>
        </tr>

        <tr>
            <th>Status</th>
            <td>
                <?= encode($order->status) ?: '<em style="color:#999;">(guest)</em>' ?>
            </td>
        </tr>

        <tr>
            <th>Payment Method</th>
            <td>
                    <?php foreach ($payments as $p): ?>
                        <strong><?= encode($p->method) ?></strong><br>
                    <?php endforeach; ?> 
            </td>
        </tr>
        <tr>
            <th>Order Date</th>
            <td><?= date('Y-m-d H:i:s', strtotime($order->order_date)) ?></td>
        </tr>
        <tr>
            <th>Total Amount</th>
            <td><strong>RM <?= number_format($order->total_amount, 2) ?></strong></td>
        </tr>
        <tr>
            <th>Shipping Address</th>
            <td>
                <?= encode($order->shipping_address) ?><br>
                <?= encode($order->shipping_postcode) ?>, <?= encode($order->shipping_city) ?><br>
                <?= encode($order->shipping_state) ?>
            </td>
        </tr>
    </table>

    <h3>Order Items</h3>

    <table class="product-table">
        <thead>
            <tr>
                <th width="80">Image</th>
                <th>Product</th>
                <th width="100">Price</th>
                <th width="100">Qty</th>
                <th width="120">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $i): ?>
                    <tr>
                        <td>
                            <?php if ($i->photo_name && file_exists("../upload/{$i->photo_name}")): ?>
                                <img src="../upload/<?= encode($i->photo_name) ?>"
                                     style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
                            <?php else: ?>
                                <span style="color:#aaa;">No image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= encode($i->title) ?></td>
                        <td>RM <?= number_format($i->price_each, 2) ?></td>
                        <td><?= $i->quantity ?></td>
                        <td>RM <?= number_format($i->price_each * $i->quantity, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; color:#999; padding:20px;">
                        No items in this order.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../_foot.php'; ?>