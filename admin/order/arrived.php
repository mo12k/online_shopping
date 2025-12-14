<?php
require '../_base.php';


$id = req('id');

if (!$id) {
    temp('error', 'Invalid order');
    redirect('order.php');
}

$stm = $_db->prepare("
    SELECT status
    FROM orders
    WHERE order_id = ?
");
$stm->execute([$id]);
$status = $stm->fetchColumn();

if ($status !== 'shipping') {
    temp('error', 'Order cannot be completed');
    redirect("order.php");
}

$_db->prepare("
    UPDATE orders
    SET status = 'completed'
    WHERE order_id = ?
")->execute([$id]);

temp('info', "Order #$id marked as completed");
redirect('../page/order.php');
