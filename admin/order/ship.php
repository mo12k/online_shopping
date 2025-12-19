<?php
require '../_base.php';

$id = req('id');

if (!$id) {
    temp('error', 'Invalid order');
    redirect('../page/order.php');
}

$stm = $_db->prepare("SELECT status FROM orders WHERE order_id = ?");
$stm->execute([$id]);
$status = $stm->fetchColumn();

if ($status !== 'pending') {
    temp('error', 'Order is not pending');
    redirect('../page/order.php');
}

$_db->prepare("UPDATE orders SET status = 'shipping' WHERE order_id = ?")
    ->execute([$id]);

temp('info', "Order #$id has been shipped");
redirect('../page/order.php');