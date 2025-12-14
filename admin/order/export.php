<?php
require '../_base.php';
admin_require_login();


$id = get('id');
if (!$id) {
    die('Invalid order id');
}


$stm = $_db->prepare("
    SELECT
        o.order_id,
        o.total_amount,
        o.status,
        o.order_date,

        c.username,

        a.address,
        a.postcode,
        a.city,
        a.state
    FROM orders o
    LEFT JOIN customer c
        ON o.customer_id = c.customer_id
    LEFT JOIN customer_address a
        ON o.address_id = a.address_id
    WHERE o.order_id = ?
");
$stm->execute([$id]);
$o = $stm->fetch();

if (!$o) {
    die('Order not found');
}


$stm = $_db->prepare("
    SELECT
        p.id AS product_id ,
        oi.quantity,
        oi.price_each  AS price,
        oi.subtotal
    FROM order_item oi
    LEFT JOIN product p
        ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stm->execute([$id]);
$items = $stm->fetchAll();


$filename = "order_{$o->order_id}.txt";

header('Content-Type: text/plain; charset=utf-8');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Pragma: no-cache');
header('Expires: 0');


echo "ORDER DETAIL\n";
echo "=============================\n";
echo "Order ID   : {$o->order_id}\n";
echo "Customer   : " . ($o->username ?: '(no user)') . "\n";
echo "Status     : {$o->status}\n";
echo "Order Date : {$o->order_date}\n\n";

echo "Shipping Address\n";
echo "-----------------------------\n";

if ($o->address) {
    echo "{$o->address}\n";
    echo "{$o->postcode}, {$o->city}, {$o->state}\n";
} else {
    echo "(no address)\n";
}

echo "\nItems\n";
echo "-----------------------------\n";
echo "Product\tQty\tPrice\t\tSubtotal\n";

foreach ($items as $i) {
    $price    = number_format((float)$i->price, 2);
    $subtotal = number_format((float)$i->subtotal, 2);

    echo "{$i->product_id}\t{$i->quantity}\tRM {$price}\tRM {$subtotal}\n";
}

echo "\n-----------------------------\n";
echo "TOTAL: RM " . number_format((float)$o->total_amount, 2) . "\n";

exit;
