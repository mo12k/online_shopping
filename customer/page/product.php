<?php

require '../_base.php';
include '../../_head.php';
include '../../_header.php';

$current = 'product';
$_title  = 'Product List';

$category_id = get('category_id');
$name        = trim(req('name') ?? '');

$min_price  = req('min_price');
$max_price  = req('max_price');

$min_price  = ($min_price !== '' && $min_price !== null) ? (float)$min_price : null;
$max_price  = ($max_price !== '' && $max_price !== null) ? (float)$max_price : null;

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

if ($min_price !== null) {
    $sql .= " AND p.price >= ?";
    $params[] = $min_price;
}

if ($max_price !== null) {
    $sql .= " AND p.price <= ?";
    $params[] = $max_price;
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

if ($min_price !== null) {
    $q['min_price'] = $min_price;
}

if ($max_price !== null) {
    $q['max_price'] = $max_price;
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
        margin-top: 2px;
    }

    .price-presets button {
        min-width: 100px;
        padding: 3px 6px;
        border-radius: 8px;
        border: 1px solid #D7CCC8;
        background: #fff;
        color: #5D4037;
        cursor: pointer;
        white-space: nowrap;
    }

    .price-presets button:hover {
        background: #8D6E63;
    }

    .price-presets button.active {
        background: #8D6E63;
        color: #fff;
        border-color: #8D6E63;
        box-shadow: inset 0 0 0 4px #3E2723;
    }

    .price-presets .clear-btn {
        background: transparent;
        color: #6D4C41;
        border: 1px dashed #BCAAA4;
        font-weight: 500;
    }

    .price-presets .clear-btn:hover {
        background: #F5F1EE;
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
                <input type="hidden" name="min_price" value="<?= encode($min_price) ?>">
                <input type="hidden" name="max_price" value="<?= encode($max_price) ?>">

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
                
                <div class="price-presets" style="display:flex; gap:10px; margin:10px 0;">

                    <button type="submit"
                            class="<?= ($min_price === 0.0 && $max_price === 50.0) ? 'active' : '' ?>"
                            onclick="this.form.min_price.value=0; this.form.max_price.value=50;">
                            RM 0 – 50
                    </button>

                    <button type="submit"
                            class="<?= ($min_price === 50.0 && $max_price === 100.0) ? 'active' : '' ?>"
                            onclick="this.form.min_price.value=50; this.form.max_price.value=100;">
                            RM 50 – 100
                    </button>

                    <button type="submit"
                            class="<?= ($min_price === 100.0 && $max_price === null) ? 'active' : '' ?>"
                            onclick="this.form.min_price.value=100; this.form.max_price.value='';">
                            RM 100+
                    </button>

                    <a href="product.php"
                       style="
                            height: 16px;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            padding: 6px 14px;
                            border-radius: 8px;
                            border: 1px solid #D7CCC8;
                            background: #F5F5F5;
                            color: #5D4037;
                            font-size: 14px;
                            font-weight: 500;
                            text-decoration: none;
                            white-space: nowrap;
                            transition: background 0.15s ease, color 0.15s ease;
                            margin-top: 10px;
                            "
                        onmouseover="this.style.background='#EFEBE9'"
                        onmouseout="this.style.background='#F5F5F5'">
                        Clear
                    </a>


                </div>
            </form>
        </div>
    </div>

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
