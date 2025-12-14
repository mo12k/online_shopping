<?php

require '../_base.php';
include '../../_head.php';
include '../../_header.php';

$current = 'product';
$_title  = 'Product List';

$category_id = get('category_id');
$name        = trim(req('name') ?? '');

$sql = "SELECT p.*, c.*
        FROM product p
        LEFT JOIN category c ON p.category_id = c.category_id
        WHERE p.status = 1
          AND p.stock > 0";

$params = [];

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

//pageing
$page = req('page', 1);

require_once '../lib/SimplePager.php';
$p   = new SimplePager($sql, $params, 15, $page);
$arr = $p->result;

$q = [];

if ($category_id !== '' && $category_id !== null) {
    $q['category_id'] = $category_id;
}

if ($name !== '') {
    $q['name'] = $name;
}

$info = temp('info');
?>

<style>
.search-input-wrapper {
    position: relative;
    width: 600px;
    max-width: 100%;
}

.search-input-wrapper input.key-in {
    box-sizing: border-box;
    display: block;
    width: 100%;
    height: 36px;
    padding: 0 36px 0 12px;
    border: 1px solid #D7CCC8;
    border-radius: 8px;
    background-color: #ffffff;
    color: #5D4037;
    font-size: 15px;
}

.search-input-wrapper .search-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #8D6E63;
}
</style>

<div class="content">

    <div style="display:flex;
                justify-content:space-between;
                align-items:center;
                margin-bottom:25px;
                flex-wrap:wrap;
                gap:20px;">

        <div class="search-bar">
            <form method="get" class="search-form">

                <?= html_select('category_id', $_category, 'All category', $category_id, true) ?>

                <div class="search-input-wrapper">
                    <input class="key-in"
                           type="search"
                           name="name"
                           value="<?= encode($name) ?>"
                           placeholder="Searching by id, title, author">
                    <i class='bx bx-search search-icon'
                       onclick="this.closest('form').submit();"></i>
                </div>

            </form>
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

    <div class="product-grid">
        <?php foreach ($arr as $s): ?>
            <div class="product-card">

                <a href="detail.php?id=<?= encode_id($s->id) ?>">
                    <?php if ($s->photo_name): ?>
                        <img src="../../admin/upload/<?= $s->photo_name ?>">
                    <?php else: ?>
                        <img src="/images/no-photo.jpg" style="opacity:0.5;">
                    <?php endif; ?>
                </a>

                <div class="product-title">
                    <?= encode(mb_strimwidth($s->title, 0, 25, '...', 'UTF-8')) ?>
                </div>

                <div class="product-author">
                    <?= encode($s->author) ?>
                </div>

                <div class="product-price">
                    RM <?= encode($s->price) ?>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

    <?= $p->html(http_build_query($q)) ?>

</div>

<?php include '../../_footer.php'; ?>
