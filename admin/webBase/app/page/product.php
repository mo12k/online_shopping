<?php
require '../_base.php';

$current = 'product';
$_title = 'Product List';

//set database
$_category = $_db->query('SELECT category_id, category_name FROM category ORDER BY sort_order')
                 ->fetchAll(PDO::FETCH_KEY_PAIR);
$_category = []+$_category;
             
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
    'c.category_name'     => 'Category',
    'p.price' => 'Price',
    'p.stock'     => 'Stock',
    'p.status' => 'Status',
];

$sort = req('sort');
key_exists($sort , $fields) || $sort = 'p.id';//reset back to sort by id


$dir = req('dir');
in_array($dir,['asc','desc']) || $dir ='asc';//reset back to sort by asc

$page = req('page', 1);

$sql = "SELECT p.*, c.category_name 
        FROM product p 
        LEFT JOIN category c ON p.category_id = c.category_id 
        WHERE 1=1";  // 1=1 讓後面好加條件

$params = [];

//searching by combine
if ($name !== '') {
    $sql .= " AND (p.title LIKE ? OR p.author LIKE ? OR p.id LIKE ?)";
    $params[] = "%$name%";
    $params[] = "%$name%";
    $params[] = "%$name%";
};

if ($category_id !== '' && $category_id !== null) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
};

$sql .= " ORDER BY $sort $dir";


$page = max(1, (int)req('page', 1));
$per_page = 10;
require_once '../lib/SimplePager.php';
$pager = new SimplePager($sql, $params, $per_page, $page);
$arr   = $pager->result;       
$total_items = $pager->item_count;       // 總筆數
$total_pages = $pager->page_count;       // 總頁數
$current_page = $pager->page;



include '../_head.php';
?>
      
      
<div class="content">
    
            <!-- 頂部工具列：分類 + 搜尋 + 新增按鈕（緊湊又漂亮）-->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; flex-wrap:wrap; gap:20px;">
            
            <div class = "search-bar">
            <form method ="get" class="search-form">                   
            <?= html_select('category_id', $_category, 'All category' , '', true) ?>
            <?= html_search('name','Searching by id/title/author') ?>
            <button>Search</button>

            </div>
            <!-- add button -->
            <a href="product_add.php" class="btn-add">+ Add New Book</a>
        </div>
         
        <p style="margin:20px 0; color:#666; font-size:15px;">
            Showing <?= count($arr) ?> of <strong><?= $pager->item_count ?></strong> record(s) |
            Page <strong><?= $pager->page ?></strong> of <strong><?= $pager->page_count ?></strong>
        </p>

           
    <table>
        <tr>
            <!-- table title can click ,have link to baase php -->
            <?= table_headers($fields, $sort, $dir) ?>
            <td>Action</td>
        </tr>

        <?php if (empty($arr)): ?>
            <tr> 
                <td colspan="8" class="no-find">NO PRODUCT FIND</td>
            </tr> 

        <?php else: ?>
            <?php foreach ($arr as $s): ?>
                
            <tr>
                <td>sad</td>
                <td><?= encode($s->id) ?></td>
                <td><?= encode($s->title)?></td>
                <td><?= encode($s->author)?></td>
                <td><?= encode($s->category_name) ?></td>
                <td><?= encode($s->price ) ?></td>
                <td style="<?= $s->stock <= 10 ? 'color:red;font-weight:bold;' : '' ?>">
                    <?= $s->stock ?>
                </td>
                <td>
                    <span style ="color:<?= $s->status ?'green ':'red' ?>;">
                    <?= $s->status? 'active':'unactive'?></td>
                <td style="text-align:center;">
                    <a href="product_detail.php?id=<?= encode($s->id)?>" class="btn">View</a>
                    <a href="product_edit.php?id=<?= encode($s->id) ?>" class="btn edit">Edit</a>
                    <a href="?delete=<?= encode($s->id) ?>" 
                       onclick="return confirm('Confirm delete「<?= encode($s->title) ?>」？')"
                       class="btn delete">Delete</a>
                </td>
                
            
            </tr>
            <?php endforeach ?>
        <?php endif;  ?>
    </table>

</div>

            <<?php if ($pager->page_count > 1): ?>
            <div style="margin:40px 0; text-align:center;">
                <?php $pager->html(http_build_query([
                    'category_id' => $category_id,
                    'name'        => $name,
                    'sort'        => $sort,
                    'dir'         => $dir
                ])); ?>
            </div>
            <?php endif; ?>

<?php
include '../_foot.php';