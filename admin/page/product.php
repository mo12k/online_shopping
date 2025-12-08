<?php
require '../_base.php';

admin_require_login();

$current = 'product';
$_title = 'Product List';

             
$category_id = get('category_id');
$name = trim(req('name')?? '');

// searhing all possible 
// use


//header set sort
$fields = [
    
    'p.photo_name'         => 'Picture',
    'p.id'       => 'Id',
    'p.title'         => 'Title',
    'p.author'       => 'Author',
    'c.category_code'     => 'Category',
    'p.price' => 'Price',
    'p.stock'     => 'Stock',
    'p.status' => 'Status',
];



$sql = "SELECT p.*, c.*
        FROM product p 
        LEFT JOIN category c ON p.category_id = c.category_id 
        WHERE 1=1";

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

include '../_head.php';

?>
      
      
<div class="content">
    
          
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; flex-wrap:wrap; gap:20px;">
            
            <div class = "search-bar">
            <form method ="get" class="search-form">                   
            <?= html_select('category_id', $_category, 'All category' , '', true) ?>
            <?= html_search('name','Searching by id , title , author') ?>
            <button>Search</button>

            </div>
            <!-- add button -->
            <a href="/admin/product/insert.php" class="btn-add">+ Add New Book</a>
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

           
    <table >
        <tr>
            <!-- table title can click ,have link to baase php -->
            <?= table_headers($fields, $sort, $dir ,"page=$page") ?>
            <td>Action</td>
        </tr>

        <?php if (empty($arr)): ?>
            <tr> 
                <td colspan="8" class="no-find">NO PRODUCT FIND</td>
            </tr> 

        <?php else: ?>
            <?php foreach ($arr as $s): ?>
                
            <tr>
                 <td><?php if ($s->photo_name): ?>
                    <a href="../upload/<?= $s->photo_name ?>" >
                        <img src="../upload/<?= $s->photo_name ?>" 
                            style="width:60px; height:60px; object-fit:cover; border-radius:6px; border:2px solid #ddd;">
                    </a>
                <?php else: ?></td>
                    <img src="/images/no-photo.jpg" style="width:60px; height:60px; opacity:0.5;">
                <?php endif; ?>
                <td><?= encode($s->id) ?></td>
                <td >
                    <?= encode(mb_strimwidth($s->title , 0 ,20 ,'...' , 'UTF-8'))?>
                </td>
                <td><?= encode($s->author)?></td>
                <td><?= encode($s->category_code) ?></td>
                <td><?= encode($s->price ) ?></td>
                <td style="<?= $s->stock <= 10 ? 'color:red;font-weight:bold;' : '' ?>">
                    <?= $s->stock ?>
                </td>

                <td>
                    <span style ="color:<?= $s->status ?'green ':'red' ?>;">
                    <?= $s->status? 'Published':'Draft'?></td>
                <td style="text-align:center;">
                    <button data-get="../product/detail.php?id=<?= encode($s->id)?>" class="btn">View</button>
                    <button data-get="../product/update.php?id=<?= encode($s->id) ?>" class="btn edit">Edit</button>
                    <button data-post="../product/delete.php?id=<?= encode($s->id) ?>" 
                       data-confirm="Confirm delete「<?= encode($s->title) ?>」？"
                       class="btn delete">Delete</button>
                </td>
                
            
            </tr>
            <?php endforeach ?>
        <?php endif;  ?>
    </table>    
                <?= $p->html("sort=$sort&dir=$dir") ?>
                
</div>

           

<?php
include '../_foot.php';