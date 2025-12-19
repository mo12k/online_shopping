<?php
include '../_base.php';
$current = 'product';

// ----------------------------------------------------------------------------
admin_require_login();
if (is_post()) {
    $id = req('id');

    // Delete photo
    $stm = $_db->prepare('SELECT photo_name FROM product WHERE id = ?');
    $stm->execute([$id]);
    $photo = $stm->fetchColumn();
    
    unlink("../upload/$photo");

    $query = http_build_query([
        'sort' => get('sort', 'id'),     
        'dir'  => get('dir', 'desc'),    
        'page' => get('page') ,           
        'category_id'=> get('category_id')  ,
        'name'=> get('name')  
    ]);

    temp('info', "Record ID:$id deleted");
    $stm = $_db->prepare('DELETE FROM product WHERE id = ?');
    $stm->execute([$id]);
   
    redirect('../page/product.php?'. $query);

}



// ----------------------------------------------------------------------------
