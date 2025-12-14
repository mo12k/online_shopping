<?php
require '../_base.php';

admin_require_login();

$current = 'order';
$_title  = 'Order List';

/* =========================
   Inputs
========================= */
$name      = trim(req('name') ?? '');
$date_from = req('date_from');
$date_to   = req('date_to');

/* =========================
   Table headers (for sort)
========================= */
$fields = [
    'o.order_id'     => 'Order ID',
    'c.username'     => 'Customer',
    'total_qty'      => 'Total Qty',
    'o.total_amount' => 'Total (RM)',
    'o.status'       => 'Status',
    'o.order_date'   => 'Order Date',
];

/* =========================
   SQL
========================= */
$sql = "
SELECT
    o.order_id,
    o.total_amount,
    o.status,
    o.order_date,

    c.username,

    SUM(oi.quantity)        AS total_qty,
    COUNT(oi.order_item_id) AS line_count

FROM orders o
LEFT JOIN customer c
    ON o.customer_id = c.customer_id
LEFT JOIN order_item oi
    ON o.order_id = oi.order_id

WHERE 1 = 1
";

$params = [];

$q = [];

/* Search */
if ($name !== '') {
    $q[] = 'name=' .$name;
}

/* Date range */
if ($date_from !== '') {
    $q[] = 'date_from=' . $date_from;
}

if ($date_to !== '') {
    $q[] = 'date_to=' .$date_to;
}

/* Final query string */
$qs = implode('&', $q);


if ($name !== '') {
    $sql .= " AND (CAST(o.order_id AS CHAR) LIKE ? OR c.username LIKE ?)";
    $params[] = "%$name%";
    $params[] = "%$name%";
}


if ($date_from) {
    $sql .= " AND o.order_date >= ?";
    $params[] = $date_from . ' 00:00:00';
}

if ($date_to) {
    $sql .= " AND o.order_date <= ?";
    $params[] = $date_to . ' 23:59:59';
}


$sort = req('sort');
key_exists($sort, $fields) || $sort = 'o.order_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'desc';

$sql .= " GROUP BY o.order_id ORDER BY $sort $dir";


$page = req('page', 1);
require_once '../lib/SimplePager.php';
$p = new SimplePager($sql, $params, 10, $page);
$arr = $p->result;

include '../_head.php';
?>

<div class="content">

    <!-- 🔍 Search bar -->
    <form method="get" class="search-form" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px;">
        <?= html_search('name', 'Order ID / Username') ?>

        <input type="date" name="date_from" value="<?= encode($date_from) ?>">
        <input type="date" name="date_to" value="<?= encode($date_to) ?>">

            <button style="padding:14px 32px; background:yellow; color:black; border:none; border-radius:16px; ">
             Search</button>
        <a href="order.php" style="padding:14px 32px; background:yellow; color:black; border:none; border-radius:16px; text-decoration: none; ">
            Reset</a>
    </form>

    <p>
        <?= $p->count ?> of <?= $p->item_count ?> record(s) |
        Page <?= $p->page ?> of <?= $p->page_count ?>
    </p>

    <!-- 📦 Order table -->
    <table class="product-table">
        <tr>
            <?= table_headers($fields, $sort, $dir, "page=$page&$qs") ?>
            <th>Action</th>
        </tr>

        <?php if (empty($arr)): ?>
            <tr><td colspan="8">No order found</td></tr>
        <?php else: ?>
            <?php foreach ($arr as $s): ?>
                <tr>
                    <td><?= encode($s->order_id) ?></td>

                    <td>
                        <?= $s->username
                            ? encode($s->username)
                            : '<em style="color:#999;">(no user)</em>' ?>
                    </td>

                    <td><?= (int)$s->total_qty ?></td>

                    <td>RM <?= number_format($s->total_amount, 2) ?></td>

                    <td>
                        <span style="color:<?= $s->status === 'completed' ? 'green' : 'orange' ?>">
                            <?= ucfirst($s->status) ?>
                        </span>
                    </td>

                    <td><?= encode($s->order_date) ?></td>

                  

                    <td>
                        <button data-get="../order/detail.php?id=<?= encode($s->order_id) ?>" class="btn">
                            View
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?= $p->html("sort=$sort&dir=$dir&$qs") ?>

</div>

<?php include '../_foot.php'; ?>
