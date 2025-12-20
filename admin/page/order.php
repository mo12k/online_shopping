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
$status    = req('status');




/* =========================
   Table headers & SQL
========================= */
$fields = [
    'o.order_id'     => 'Order ID',
    'c.username'     => 'Customer',
    'total_qty'      => 'Total Qty',
    'o.total_amount' => 'Total (RM)',
    'o.status'       => 'Status',
    'o.order_date'   => 'Order Date',
];

$sql = "
    SELECT
        o.order_id,
        o.total_amount,
        o.status,
        o.order_date,
        c.username,
        COALESCE(SUM(oi.quantity), 0) AS total_qty
    FROM orders o
    LEFT JOIN customer c ON o.customer_id = c.customer_id
    LEFT JOIN order_item oi ON o.order_id = oi.order_id
    WHERE 1 = 1
";

$count_sql = "
    SELECT COUNT(DISTINCT o.order_id)
    FROM orders o
    LEFT JOIN customer c ON o.customer_id = c.customer_id
    LEFT JOIN order_item oi ON o.order_id = oi.order_id
    WHERE 1 = 1
";

$params = [];
$q = [];

$status_list = [
    'pending'   => 'Paid',
    'shipping'  => 'Shipping',
    'completed' => 'Completed',
];

/* Filters */
if ($name !== '') {
    $sql .= " AND (CAST(o.order_id AS CHAR) LIKE ? OR c.username LIKE ?)";
    $count_sql .= " AND (CAST(o.order_id AS CHAR) LIKE ? OR c.username LIKE ?)";
    $params[] = "%$name%";
    $params[] = "%$name%";
    $q[] = 'name=' . urlencode($name);
}
if ($date_from !== '') {
    $sql .= " AND o.order_date >= ?";
    $count_sql .= " AND o.order_date >= ?";
    $params[] = $date_from . ' 00:00:00';
    $q[] = 'date_from=' . $date_from;
}
if ($date_to !== '') {
    $sql .= " AND o.order_date <= ?";
    $count_sql .= " AND o.order_date <= ?";
    $params[] = $date_to . ' 23:59:59';
    $q[] = 'date_to=' . $date_to;
}
if ($status !== '' && array_key_exists($status, $status_list)) {
    $sql .= " AND o.status = ?";
    $count_sql .= " AND o.status = ?";
    $params[] = $status;
    $q[] = 'status=' . $status;
}

$sql .= " GROUP BY o.order_id";

$qs = implode('&', $q);

/* Sorting */
$sort = req('sort');
if (!array_key_exists($sort, $fields)) {
    $sort = 'o.order_id';
}
$dir = req('dir');
if (!in_array($dir, ['asc', 'desc'])) {
    $dir = 'desc';
}
$sql .= " ORDER BY $sort $dir";


$page = max(1, (int)req('page', 1));
require_once '../lib/SimplePager.php';
$p = new SimpleOPager(
    $sql,
    $count_sql,
    $params,
    10,
    $page
);
$arr = $p->result;

$info = temp('info');

include '../_head.php';
?>

<div class="content">
    <form method="get" class="search-form" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px; align-items:center;">
        <?= html_search('name', 'Order ID / Username') ?>
        <?= html_select('status', $status_list, 'All status', '', true) ?>
        <input type="date" name="date_from" value="<?= encode($date_from) ?>">
        <input type="date" name="date_to" value="<?= encode($date_to) ?>">
        <button style="padding:14px 32px; background:yellow; color:black; border:none; border-radius:16px;">
            Search
        </button>
        <a href="order.php" style="padding:14px 32px; background:yellow; color:black; border:none; border-radius:16px; text-decoration:none;">
            Reset
        </a>
    </form>

    <?php if ($info): ?>
    <div class="alert-success-fixed">
        <div class="alert-content">
            <strong>Success!</strong> <?= encode($info) ?>
            <span class="alert-close">×</span>
        </div>
    </div>
    <?php endif; ?>

    <p style="margin:20px 0; color:#666; font-size:15px;">
        <?= $p->count ?> of <?= $p->item_count ?> record(s) |
        Page <?= $p->page ?> of <?= $p->page_count ?>
    </p>

    <table class="product-table">
        <tr>
            <?= table_headers($fields, $sort, $dir, "page=$page&$qs") ?>
            <th style="text-align:center;">Action</th>
        </tr>

        <?php if (empty($arr)): ?>
            <tr>
                <td colspan="7" style="text-align:center; padding:40px; color:#999;">No order found</td>
            </tr>
        <?php else: ?>
            <?php foreach ($arr as $s): ?>
                <tr>
                    <td><?= encode($s->order_id) ?></td>
                    <td><?= $s->username ? encode($s->username) : '<em style="color:#999;">(no user)</em>' ?></td>
                    <td><?= (int)$s->total_qty ?></td>
                    <td>RM <?= number_format($s->total_amount, 2) ?></td>
                    <td>
                        <?php
                        $status_class = match ($s->status) {
                            'pending'   => 'background:#fff3cd;color:#856404;',
                            'shipping'  => 'background:#cce5ff;color:#004085;',
                            'completed' => 'background:#d4edda;color:#155724;',
                            
                           
                        };
                        ?>
                        <span style="padding:6px 14px; border-radius:20px; font-size:13px; font-weight:600; <?= $status_class ?>">
                            <?= ucfirst($s->status) ?>
                        </span>
                    </td>
                    <td><?= encode($s->order_date) ?></td>

                    <td style="text-align:center; white-space:nowrap;">
                        
                        <button data-get="../order/detail.php?id=<?= encode($s->order_id) ?>" class="btn action-btn">View</button>

                       
                        <?php if ($s->status === 'completed'): ?>
                            <a href="../order/export.php?id=<?= encode($s->order_id) ?>" target="_blank" class="btn btn-print">Print</a>
                        <?php endif; ?>

                       
                        <?php if ($s->status === 'pending'): ?>
                            <a href="../order/ship.php?id=<?= encode($s->order_id) ?>"
                            class="btn btn-ship"
                            onclick="return confirm('Ship order #<?= $s->order_id ?> and change status to Shipping?')">
                                Ship
                            </a>
                        <?php endif; ?>

                       
                        <?php if ($s->status === 'shipping'): ?>
                            <a href="../order/arrived.php?id=<?= encode($s->order_id) ?>"
                            class="btn btn-arrived "
                            onclick="return confirm('Mark order #<?= $s->order_id ?> as Arrived/Completed?')">
                                Arrived
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?= $p->html0("sort=$sort&dir=$dir&$qs") ?>
</div>

<?php include '../_foot.php'; ?>