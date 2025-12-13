<?php

require '../_base.php';
include '../../_head.php'; 
include '../../_header.php';

$current = 'product';
$_title = 'Product List';

             
$category_id = get('category_id');
$name = trim(req('name')?? '');

// searhing all possible 
// use
//header set sort
$fields = [
    
    'p.photo_name'          => 'Picture',
    'p.id'                  => 'Id',
    'p.title'               => 'Title',
    'p.author'              => 'Author',
    'c.category_code'       => 'Category',
    'p.price'               => 'Price',
    'p.stock'               => 'Stock',
    'p.status'              => 'Status',
];



$sql = "SELECT p.*, c.*
        FROM product p 
        LEFT JOIN category c ON p.category_id = c.category_id 
        WHERE 1=1 
          AND p.status = 1 
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


$sort = req('sort');
key_exists($sort, $fields) || $sort = 'id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';


$sql .= " ORDER BY $sort $dir";

// (2) Paging
$page = req('page', 1);

require_once '../lib/SimplePager.php';
$p = new SimplePager($sql, $params, 10, $page);
$arr = $p->result;

$info = temp('info');
?>
<link rel="stylesheet" href="../../css/app.css">
<link rel="stylesheet" href="../../css/customer.css">

<div class="content">
    
        <div style="display:flex; 
                    justify-content:space-between; 
                    align-items:center; 
                    margin-bottom:25px; 
                    flex-wrap:wrap; 
                    gap:20px;">

            <div class = "search-bar">
                <form method ="get" class="search-form">                   
                    <?= html_select('category_id', $_category, 'All category' , '', true) ?>

                <div class="search-input-wrapper">
                    <input type="search" id="name" name="name" value="" placeholder="Searching by id, title, author">
                        <i class='bx bx-search search-icon' onclick="this.closest('form').submit();"></i>
                </div>
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

                <a href="detail.php?id=<?= encode($s->id) ?>">
                    <?php if ($s->photo_name): ?>
                <img src="../upload/<?= $s->photo_name ?>">
                    <?php else: ?>
                <img src="/images/no-photo.jpg" style="opacity:0.5;">
                    <?php endif; ?>
                </a>

            <div class="product-title">
                <?= encode(mb_strimwidth($s->title , 0 , 25 , '...', 'UTF-8')) ?>
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

    
                <?= $p->html("sort=$sort&dir=$dir") ?>
                
</div>

           

<?php
include '../../_footer.php';?>