<?php
require '../_base.php';

admin_require_login();

$current = 'category';
$_title = 'Update Category';

$id = req('id');



$stm = $_db->prepare("SELECT * FROM category WHERE category_id = ?");
$stm->execute([$id]);
$c = $stm->fetch();



if (is_post()) {

    
    $category_code = strtoupper(trim(req('category_code')));
    $category_name = trim(req('category_name'));

   
    if ($category_code === '') {
        $_err['category_code'] = 'Category code is required';
    } elseif (strlen($category_code) > 10) {
        $_err['category_code'] = 'Max 10 characters';
    }

    if ($category_name === '') {
        $_err['category_name'] = 'Category name is required';
    } elseif (mb_strlen($category_name) > 50) {
        $_err['category_name'] = 'Max 50 characters';
    }

    if (!$_err) {
        $stm = $_db->prepare(
            "SELECT COUNT(*) FROM category WHERE category_code = ?"
        );
        $stm->execute([$category_code]);
        $exists = $stm->fetchColumn();

        if ($exists) {
            $_err['category_code'] = 'Category code already exists';
        }
    }

    if (!$_err) {
    $stm = $_db->prepare(
        "SELECT COUNT(*) FROM category WHERE category_name = ?"
    );
    $stm->execute([$category_name]);
    $exists = $stm->fetchColumn();

    if ($exists) {
        $_err['category_name'] = 'Category name already exists';
    }
    }
    
    // database check code  type 是不是10
    
    if (!$_err) {
        try {
            $_db->prepare(
                "UPDATE category 
                 SET category_code = ?, category_name = ? 
                 WHERE category_id = ?"
            )->execute([$category_code, $category_name, $id]);

            temp('info', 'Category updated successfully');
            redirect('/admin/page/category.php');

        } catch (PDOException $e) {
           
            if ($e->getCode() == 23000) {
                $_err['category_code'] = 'Category code already exists';
            } else {
                throw $e;
            }
        }
    }
}
else{

    $category_code       =  $c->category_code;
    $category_name       =  $c->category_name;
}

include '../_head.php';
?>

<div class="content">
    <div class="form-container">
        <form method="post">

            <div class="form-grid">
                <div class="fields-section">

                    <div class="auto-id">
                        Category ID: <?= encode($c->category_id) ?>
                    </div>

                    <label><span class="req">*</span> Category Code</label>
                    <?= html_text('category_code', 'required maxlength="10"', $c->category_code) ?>
                    <?= err('category_code') ?>

                    <label><span class="req">*</span> Category Name</label>
                    <?= html_text('category_name', 'required maxlength="50"', $c->category_name) ?>
                    <?= err('category_name') ?>

                    <div class="actions">
                        <button class="btn-primary">Update Category</button>
                        <a href="/admin/page/category.php" class="btn-cancel">Cancel</a>
                    </div>

                </div>
            </div>

        </form>
    </div>
</div>

<?php include '../_foot.php'; ?>
