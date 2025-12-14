<?php
require '../_base.php';

admin_require_login();

$current = 'order';
$_title  = 'Order Detail';

$order_id = req('id');
if (!$order_id) {
    redirect('order.php');
}

/* =========================
   Order + Customer + Address
========================= */
$stm = $_db->prepare("
    SELECT 
        o.*,
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
    temp('info', 'Order not found');
    redirect('order.php');
}

/* =========================
   Order Items
========================= */
$stm = $_db->prepare("
    SELECT 
        oi.*,
        p.title,
        p.photo_name
    FROM order_item oi
    LEFT JOIN product p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stm->execute([$order_id]);
$items = $stm->fetchAll();

include '../_head.php';
?>

<div class="content">

    <h2>Order #<?= encode($order->order_id) ?></h2>

    <!-- ================= Order Info ================= -->
    <table class="product-table" style="margin-bottom:30px;">
        <tr>
            <th width="200">Order ID</th>
            <td><?= encode($order->order_id) ?></td>
        </tr>
        <tr>
            <th>Customer</th>
            <td>
                <?= $order->username ?: '<em style="color:#999;">(guest)</em>' ?>
                <br>
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
            <th>Total Amount</th>
            <td><strong>RM <?= number_format($order->total_amount, 2) ?></strong></td>
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

    <!-- ================= Items ================= -->
    <h3>Order Items</h3>

    <table class="product-table">
        <tr>
            <th width="80">Image</th>
            <th>Product</th>
            <th width="100">Price</th>
            <th width="100">Qty</th>
            <th width="120">Subtotal</th>
        </tr>

        <?php foreach ($items as $i): ?>
            <tr>
                <td>
                    <?php if ($i->photo_name): ?>
                        <img src="../upload/<?= encode($i->photo_name) ?>"
                             style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
                    <?php else: ?>
                        <span style="color:#aaa;">No image</span>
                    <?php endif; ?>
                </td>
                <td><?= encode($i->title) ?></td>
                <td>RM <?= number_format($i->price, 2) ?></td>
                <td><?= $i->quantity ?></td>
                <td>
                    RM <?= number_format($i->price * $i->quantity, 2) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div style="margin-top:30px;">
        <a href="../page/order.php" class="btn">← Back to Order List</a>
    </div>

</div>

<?php include '../_foot.php'; ?>
