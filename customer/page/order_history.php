<?php
require '../_base.php';

if (!isset($_SESSION['customer_id'])) {
    temp('info', 'Please login to view order history');
    redirect('../../page/login.php');
}

$customer_id = $_SESSION['customer_id'];
$search = trim(req('search') ?? '');

$sql = "
    SELECT 
        o.order_id,
        o.total_amount,
        o.status,
        o.order_date,
        (SELECT COUNT(*) FROM order_item oi WHERE oi.order_id = o.order_id) as item_count
    FROM orders o
    WHERE o.customer_id = ?
";

$params = [$customer_id];

if ($search !== '') {
    $sql .= " AND (
        o.order_id LIKE ?
        OR o.status LIKE ?
        OR DATE(o.order_date) LIKE ?
    )";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// collect number of page
$stm = $_db->prepare("SELECT COUNT(*) FROM orders WHERE customer_id = ?" . ($search !== '' ? " AND (order_id LIKE ? OR status LIKE ? OR DATE(order_date) LIKE ?)" : ""));
$count_params = [$customer_id];
if ($search !== '') {
    $count_params = array_merge($count_params, ["%$search%", "%$search%", "%$search%"]);
}
$stm->execute($count_params);
$total_items = $stm->fetchColumn();

// calculate page
$per_page = 5;
$page = req('page', 1);
$offset = ($page - 1) * $per_page;
$total_pages = ceil($total_items / $per_page);

// order by date, only show intval(per_page), skip the intval($offset) page
$sql .= " ORDER BY o.order_date DESC LIMIT " . intval($per_page) . " OFFSET " . intval($offset);

$stm = $_db->prepare($sql);
$stm->execute($params);
$orders = $stm->fetchAll();
include '../../_head.php';
include '../../_header.php';

?>
<link rel="stylesheet" href="../../css/app.css">
<link rel="stylesheet" href="../../css/customer.css">
<style>
    .container {
        width: 900px;
        margin: 80px auto 40px;
        padding: 0 20px;
    }

    .search-bar {
        margin-bottom: 30px ;
    }

    .search-form {
        position: relative ;
        width: 100% ;
        max-width: 400px ;
    }

    .search-form input.key-in{
        display: block ;
        width: 100% ;
        padding: 12px 50px 12px 18px;
        border-radius: 30px ;
        border: 1px solid #ddd ;
        font-size: 14px ;
        box-sizing: border-box ;
    }

    .search-icon {
        position: absolute;
        right: 15px;
        top: 0;
        height: 100%;
        display: flex;
        align-items: center;
        cursor: pointer;
        color: #666;
        font-size: 18px;
        margin-top: 3px;
    }


    .order-card {
        background: #fff;
        border-radius: 14px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 20px;
        align-items: center;
        transition: none !important;
    }

    .order-info {
        display: grid;
        gap: 8px;
    }

    .order-id {
        font-size: 18px;
        font-weight: 700;
        color: #34495e;
    }

    .order-meta {
        font-size: 14px;
        color: #7f8c8d;
    }

    .order-total {
        font-size: 20px;
        font-weight: 700;
        color: #e74c3c;
    }

    .status {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-paid {
        background: #d4edda;
        color: #155724;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-shipped {
        background: #cce5ff;
        color: #004085;
    }

    .status-completed {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-cancelled {
        background: #f8d7da;
        color: #721c24;
    }

    .actions {
        text-align: right;
    }

    .btn {
        display: inline-block;
        padding: 10px 22px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
    }

    .btn-view {
        background: #3498db;
        color: #fff;
    }

    .btn-view:hover {
        background: #2980b9;
    }

    .empty {
        background: #fff;
        padding: 60px 30px;
        border-radius: 14px;
        text-align: center;
        color: #7f8c8d;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .page-info {
        margin: 20px 0;
        color: #666;
        font-size: 15px;
    }

    .btn-clear {
    padding: 10px 18px;
    border-radius: 30px;
    background: #ecf0f1;
    color: #555;
    font-weight: 600;
    text-decoration: none;
    font-size: 14px;
    }

    .btn-clear:hover {
    background: #dcdde1;
    }

</style>

<div class="container">
    <h1>My Orders</h1>
    
    <div class="search-bar" style="display:flex; gap:12px; align-items:center;">
    <form method="get" class="search-form">
        <input
            class="key-in"
            type="search"
            name="search"
            value="<?= encode($search) ?>"
            placeholder="Search by Order ID / Status / Date">
        <i class='bx bx-search search-icon' onclick="this.closest('form').submit();"></i>
    </form>

    <?php if ($search !== ''): ?>
        <a href="order_history.php" class="btn btn-clear">
            Clear
        </a>
    <?php endif; ?>
</div>


    <?php if ($total_items == 0): ?>
        <div class="empty">
            <h2>No orders found</h2>
            <?php if ($search !== ''): ?>
                <p>No orders match your search criteria.</p>
                <a href="order_history.php" class="btn btn-view" style="margin-top: 15px;">
                    Clear Search
                </a>
            <?php else: ?>
                <p>You haven’t placed any orders yet.</p>
                <a href="product.php" class="btn btn-view" style="margin-top: 15px;">
                    Start Shopping
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="page-info">
            Showing <?= count($orders) ?> of <?= $total_items ?> order(s) |
            Page <?= $page ?> of <?= $total_pages ?>
        </div>
        
        <?php foreach ($orders as $o): ?>
            <div class="order-card">
                <div class="order-info">
                    <div class="order-id">
                        Order #<?= $o->order_id ?>
                    </div>

                    <div class="order-meta">
                        <?= date('F j, Y, g:i a', strtotime($o->order_date)) ?>
                        · <?= $o->item_count ?> item(s)
                    </div>

                    <div>
                        <span class="status status-<?= $o->status ?>">
                            <?= ucfirst($o->status) ?>
                        </span>
                    </div>
                </div>

                <div class="actions">
                    <div class="order-total">
                        RM <?= number_format($o->total_amount, 2) ?>
                    </div>
                    <br>
                    <a href="order_confirm.php?id=<?= $o->order_id ?>" class="btn btn-view">
                        View Order
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
        
        <!-- 分页导航 -->
        <div class="pager-container">
            <div class="pager">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="pager-btn">
                        &laquo;
                    </a>
                <?php else: ?>
                    <span class="pager-btn disabled">&laquo;</span>
                <?php endif; ?>
                
                <?php
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);
                
                if ($start > 1) {
                    echo '<a href="?page=1&search=' . urlencode($search) . '" class="pager-btn">1</a>';
                    if ($start > 2) echo '<span class="pager-dots">...</span>';
                }
                
                for ($i = $start; $i <= $end; $i++) {
                    if ($i == $page) {
                        echo '<span class="pager-btn active">' . $i . '</span>';
                    } else {
                        echo '<a href="?page=' . $i . '&search=' . urlencode($search) . '" class="pager-btn">' . $i . '</a>';
                    }
                }
                
                if ($end < $total_pages) {
                    if ($end < $total_pages - 1) echo '<span class="pager-dots">...</span>';
                    echo '<a href="?page=' . $total_pages . '&search=' . urlencode($search) . '" class="pager-btn">' . $total_pages . '</a>';
                }
                ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="pager-btn">
                        &raquo;
                    </a>
                <?php else: ?>
                    <span class="pager-btn disabled">&raquo;</span>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../../_footer.php'; ?>