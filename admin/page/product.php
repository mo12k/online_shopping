<?php
require '../_base.php';

admin_require_login();

$current = 'product';
$_title = 'Product List';

$category_id = get('category_id');
$name = trim(req('name') ?? '');
$low_stock = req('low_stock');  // 從 dashboard 或手動輸入都會有

/* ===== $fields 定義 ===== */
$fields = [
    'p.photo_name'     => 'Picture',
    'p.id'             => 'Id',
    'p.title'          => 'Title',
    'p.author'         => 'Author',
    'c.category_code'  => 'Category',
    'p.price'          => 'Price(RM)',
    'p.stock'          => 'Stock',
    'p.status'         => 'Status',
];

$q = [];
if ($name !== '')        $q[] = 'name=' . urlencode($name);
if ($category_id !== '') $q[] = 'category_id=' . $category_id;
if ($low_stock)          $q[] = 'low_stock=1';

$qs = implode('&', $q);

$sql = "SELECT p.*, c.*
        FROM product p 
        LEFT JOIN category c ON p.category_id = c.category_id 
        WHERE 1=1";

$params = [];

if ($low_stock) {
    $sql .= " AND p.stock <= 10 AND p.status = 1";
    $_title = 'Low Stock Products (≤ 10)';
}

if ($name !== '') {
    $sql .= " AND (p.title LIKE ? OR p.author LIKE ? OR p.id LIKE ?)";
    $params[] = "%$name%";
    $params[] = "%$name%";
    $params[] = "%$name%";
}
if ($category_id !== '' && $category_id !== null) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
}

$sort = req('sort');
if (!array_key_exists($sort, $fields)) {
    $sort = 'p.id';
}

$dir = req('dir');
if (!in_array($dir, ['asc', 'desc'])) {
    $dir = 'asc';
}

$sql .= " ORDER BY $sort $dir";

$page = req('page', 1);
require_once '../lib/SimplePager.php';
$p = new SimplePager($sql, $params, 10, $page);
$arr = $p->result;

$info = temp('info');

include '../_head.php';
?>


<style>
.low-stock-btn {
    padding: 14px 32px;
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: 16px;
    text-decoration: none;
    display: inline-block;
    font-weight: bold;
    margin-left: 15px;
}
.all-products-btn {
    padding: 14px 32px;
    background: #95a5a6;
    color: white;
    border: none;
    border-radius: 16px;
    text-decoration: none;
    display: inline-block;
    margin-left: 15px;
}
</style>

<div class="content">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; flex-wrap:wrap; gap:20px;">
        
        <div class="search-bar">
            <form method="get" class="search-form">                   
                <?= html_select('category_id', $_category, 'All category', '', true) ?>
                <?= html_search('name','Searching by id , title , author') ?>
                <button style="padding:14px 32px; background:yellow; color:black; border:none; border-radius:16px;">
                    Search
                </button>

             
                <a href="<?= $low_stock ? 'product.php?low_stock=1' : 'product.php' ?>"
                   style="padding:14px 32px; background:yellow; color:black; border:none; border-radius:16px; text-decoration:none;">
                    Reset
                </a>
            </form>
        </div>

    
        <div style="display:flex; align-items:center; flex-wrap:wrap; gap:15px;">
            <a href="/admin/product/insert.php" class="btn-add">+ Add New Book</a>

            <?php
        
            $low_stock_count = $_db->query("SELECT COUNT(*) FROM product WHERE stock <= 10 AND status = 1")->fetchColumn();
            ?>

            <?php 
            
            if ($low_stock || $low_stock_count > 0): 
            ?>
                <?php if ($low_stock): ?>
                   
                    <a href="product.php" class="all-products-btn">
                        <?= $low_stock_count > 0 ? "All Products ($low_stock_count low stock)" : "All Products (No low stock)" ?>
                    </a>
                <?php else: ?>
                   
                    <a href="product.php?low_stock=1" class="low-stock-btn">
                        Low Stock Alert (<?= $low_stock_count ?> items)
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

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
            <th>Action</th>
        </tr>

        <?php if (empty($arr)): ?>
            <tr>
                <td colspan="9" class="no-find">
                    <?= $low_stock ? 'No low stock products found ' : 'NO PRODUCT FOUND' ?>
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($arr as $s): ?>
            <tr>
                <td>
                    <?php if ($s->photo_name): ?>
                        <img src="../upload/<?= encode($s->photo_name) ?>" style="width:60px; height:60px; object-fit:cover; border-radius:6px; border:2px solid #ddd;">
                    <?php else: ?>
                        <img src="/images/no-photo.jpg" style="width:60px; height:60px; opacity:0.5;">
                    <?php endif; ?>
                </td>
                <td><?= encode($s->id) ?></td>
                <td><?= encode(mb_strimwidth($s->title, 0, 20, '...', 'UTF-8')) ?></td>
                <td><?= encode(mb_strimwidth($s->author, 0, 20, '...', 'UTF-8')) ?></td>
                <td><?= encode($s->category_code) ?></td>
                <td><?= encode($s->price) ?></td>
                <td style="<?= $s->stock <= 10 ? 'color:red;font-weight:bold;' : '' ?>"><?= $s->stock ?></td>
                <td>
                    <span style="color:<?= $s->status ? 'green' : 'red' ?>;">
                        <?= $s->status ? 'Published' : 'Draft' ?>
                    </span>
                </td>
                <td style="text-align:center;">
                    <button data-get="../product/detail.php?id=<?= encode($s->id) ?>" class="btn">View</button>
                    <button data-get="../product/update.php?id=<?= encode($s->id) ?>&<?= $qs ?>" class="btn edit">Edit</button>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?= $p->html("sort=$sort&dir=$dir&$qs") ?>
</div>

<?php include '../_foot.php'; ?>