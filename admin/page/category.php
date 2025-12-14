<?php
require '../_base.php';

admin_require_login();

$current = 'category';
$_title = 'Category List';

             
$category_id = get('category_id');
$name = trim(req('name')?? '');

// searhing all possible 
// use


//header set sort
$fields = [
    
    'category_id'         => 'Id',
    'category_code'       => 'Code',
    'category_name'         => 'Name',
    
];
$q = [];
if ($name !== '')        $q[] = 'name=' .$name;
$qs = implode('&', $q);

$sql = "SELECT *
        FROM category
        WHERE 1=1
       ";



$params = [];

if ($name !== '') {
    $sql .= " AND (category_id LIKE ? OR category_code LIKE ? OR category_name LIKE ?)";
    $params[] = "%$name%"; 
    $params[] = "%$name%"; 
    $params[] = "%$name%";
}





$sort = req('sort');
key_exists($sort, $fields) || $sort = 'category_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';


$sql .= " ORDER BY $sort $dir";


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
            <?= html_search('name','Searching by id , code , name') ?>
            <button style="padding:14px 32px; background:yellow; color:black; border:none; border-radius:16px; ">
                Search</button>

            <a href="category.php" style="padding:14px 32px; background:yellow; color:black; border:none; border-radius:16px; text-decoration: none; ">
                Reset</a>

            </div>
            <!-- add button -->
            <a href="/admin/category/insert.php" class="btn-add">+ Add New Category</a>
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
          <?= $p->item_count ?> record(s)
        </p>

           
    <table class="product-table" >
        <tr>
            <!-- table title can click ,have link to baase php -->
            <?= table_headers($fields, $sort, $dir ,"page=$page&$qs") ?>
            <th>Action</th>
        </tr>

        <?php if (empty($arr)): ?>
            <tr> 
                <td colspan="4" class="no-find">NO PRODUCT FIND</td>
            </tr> 

        <?php else: ?>
            <?php foreach ($arr as $s): ?>
                
            <tr>
                <td><?= encode($s->category_id) ?></td>
                <td><?= encode($s->category_code) ?></td>
                <td >
                    <?= encode($s->category_name)?>
                </td>
                <td style="text-align:center;">
                    <button data-get="../category/update.php?id=<?= encode($s->category_id) ?>" class="btn edit">Edit</button>
                    <button data-post="../category/delete.php?id=<?= encode($s->category_id) ?>" 
                       data-confirm="Confirm delete「<?= encode($s->category_name) ?>」？"
                       class="btn delete">Delete</button>
                </td>
                
            
            </tr>
            <?php endforeach ?>
        <?php endif;  ?>
    </table>    
                <?= $p->html("sort=$sort&dir=$dir&$qs") ?>
                
</div>

           

<?php
include '../_foot.php';