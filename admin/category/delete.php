<?php
include '../_base.php';
$current = 'category';

// ----------------------------------------------------------------------------

if (is_post()) {
    $category_id  = req('id');

     $query = http_build_query([
        'sort' => get('sort', 'id'),     
        'dir'  => get('dir', 'asc'),    
        'page' => get('page') ,           
        'category_id'=> get('category_id')  ,
        'name'=> get('name')  
    ]);

    // Delete photo
    $stm = $_db->prepare('SELECT COUNT(*) FROM product WHERE category_id = ?');
    $stm->execute([$category_id ]);
    
    if($stm->fetchColumn()>0){
        temp('error', 'Has been related product ,Please change product category first');
        redirect('../page/category.php?'. $query);
       exit;
    }
    else {
    temp('success', "Record ID:$category_id deleted");
    $stm = $_db->prepare('DELETE FROM category WHERE category_id = ?');
    $stm->execute([$category_id]);
   
    
    redirect('../page/category.php?'. $query);
    }
  
}



// ----------------------------------------------------------------------------
