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
$status = req('status');

/* =========================
   Table headers (for sort)
========================= */
$fields = [
    'order_id'     => 'Order ID',
    'username'     => 'Customer',
    'total_qty'      => 'Total Qty',
    'total_amount' => 'Total (RM)',
    'status'       => 'Status',
    'order_date'   => 'Order Date',
];


/* =========================
   SQL
========================= */
$sql = "
    SELECT *
    FROM (
        SELECT
            o.order_id,
            o.total_amount,
            o.status,
            o.order_date,
            c.username,
            COALESCE(SUM(oi.quantity), 0) AS total_qty
        FROM orders o
        LEFT JOIN customer c
            ON o.customer_id = c.customer_id
        LEFT JOIN order_item oi
            ON o.order_id = oi.order_id
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
    'pending'   => 'Pending',
    'delivery'  => 'Delivery',
    'shipping'  => 'Shipping',
    'completed' => 'Completed',
];

$params = [];
$q = [];

/* =========================
   Search: Order ID / Username
========================= */
if ($name !== '') {
    $sql .= " AND (CAST(o.order_id AS CHAR) LIKE ? OR c.username LIKE ?)";
    $count_sql .= " AND (CAST(o.order_id AS CHAR) LIKE ? OR c.username LIKE ?)";
    $params[] = "%$name%";
    $params[] = "%$name%";
    $q[] = 'name=' . $name;
}

/* =========================
   Date range
========================= */
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

/* =========================
   Status filter
========================= */
if ($status !== '' && in_array($status, ['pending','shipping','completed'])) {
    $sql .= " AND o.status = ?";
    $count_sql .= " AND o.status = ?";
    $params[] = $status;
    $q[] = 'status=' . $status;
}

/* =========================
   Final query string (for paging/sort)
========================= */
$qs = implode('&', $q);



$sort = req('sort');
key_exists($sort, $fields) || $sort = 't.order_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'desc';

$sql .= " GROUP BY o.order_id
) t
ORDER BY $sort $dir";


$page = req('page', 1);
require_once '../lib/SimplePager.php';
$p = new SimpleOPager(
    $sql,
    $count_sql,
    $params,
    10,
    $page
);

$arr = $p->result;


include '../_head.php';
?>

<div class="content">

    <!-- Search bar -->
    <form method="get" class="search-form" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px;">
        <?= html_search('name', 'Order ID / Username') ?>
         <?= html_select('status', $status_list, 'All status' , '', true) ?>
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
                        <?php
                            $status_class = match ($s->status) {
                                'pending'   => 'background:#fff3cd;color:#856404;',
                                'shipping'  => 'background:#cce5ff;color:#004085;',
                                'completed' => 'background:#d4edda;color:#155724;',
                                default     => 'background:#eee;color:#333;',
                            };
                        ?>
                        <span style="
                            padding:6px 14px;
                            border-radius:20px;
                            font-size:13px;
                            font-weight:600;
                            <?= $status_class ?>
                        ">
                            <?= $s->status ?>
                        </span>
                    </td>

                    <td><?= encode($s->order_date) ?></td>

                  

                    <td>
                        <button data-get="../order/detail.php?id=<?= encode($s->order_id) ?>" class="btn">
                            View
                        </button>
                        <a href="../order/export.php?id=<?= encode($s->order_id) ?>" target="_blank" class="btn">
                            Print
                        </a>

                        <?php if ($s->status === 'shipping'): ?>
                            
                            <form method="post" action="../order/arrived.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?= encode($s->order_id) ?>">
                                <button
                                    class="btn"
                                    style="background:#28a745;color:#fff;"
                                    onclick="return confirm('Mark this order as completed?')">
                                    Arrived
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?= $p->html0("sort=$sort&dir=$dir&$qs") ?>

</div>

<?php include '../_foot.php'; ?>
