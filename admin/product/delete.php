<?php
include '../_base.php';
$current = 'product';

// ----------------------------------------------------------------------------

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


    $stm = $_db->prepare('DELETE FROM product WHERE id = ?');
    $stm->execute([$id]);
    temp('info', 'Record deleted');
    redirect('../page/product.php?'. $query);

}



// ----------------------------------------------------------------------------
