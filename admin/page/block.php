<?php
require '../_base.php';

admin_require_login();

if (!is_post()) {
    redirect('/admin/page/customer.php');
}

$id = (int)req('id');
if ($id <= 0) {
    redirect('/admin/page/customer.php');
}

try {
    $stm = $_db->prepare('UPDATE customer SET is_blocked = 1 WHERE customer_id = ?');
    $stm->execute([$id]);
    temp('info', "Customer ID:$id blocked");
} catch (Throwable $e) {
    temp('info', 'Unable to block customer. Please ensure the database has an is_blocked column.');
}

$query = http_build_query([
    'sort' => get('sort', 'customer_id'),
    'dir' => get('dir', 'asc'),
    'page' => get('page', 1),
    'keyword' => get('keyword', ''),
]);

redirect('/admin/page/customer.php?' . $query);
