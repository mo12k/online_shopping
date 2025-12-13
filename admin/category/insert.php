<?php
require '../_base.php';

admin_require_login();

$current = 'category';
$_title = 'Add New Category';

if (is_post()) {
    // big and nospace
    $category_code = strtoupper(trim(req('category_code')));
    $category_name = trim(req('category_name'));

    // Validation
    if ($category_code === '') {
        $_err['category_code'] = 'Category code is required';
    }
    else if (strlen($category_code) > 10) {
        $_err['category_code'] = 'Max 10 characters';
    }

    if ($category_name === '') {
        $_err['category_name'] = 'Category name is required';
    }
    else if (mb_strlen($category_name) > 50) {
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


    // Insert
    if (!$_err) {
        $_db->prepare(
            "INSERT INTO category (category_code, category_name)
             VALUES (?, ?)"
        )->execute([$category_code, $category_name]);

        temp('info', 'Category added successfully');
        redirect('/admin/page/category.php');
    }
}

include '../_head.php';
?>

<div class="content">
    <div class="form-container">
        <form method="post">

            <div class="form-grid">
                <div class="fields-section">

                    <div class="auto-id">
                        Category ID will be generated automatically
                    </div>

                    <label><span class="req">*</span> Category Code</label>
                    <?= html_text('category_code', 'required maxlength="10" placeholder="e.g. ART"') ?>
                    <?= err('category_code') ?>

                    <label><span class="req">*</span> Category Name</label>
                    <?= html_text('category_name', 'required maxlength="50" placeholder="e.g. Art Books"') ?>
                    <?= err('category_name') ?>

                    <div class="actions">
                        <button class="btn-primary">Add Category</button>
                        <a href="/admin/page/category.php" class="btn-cancel">Cancel</a>
                    </div>

                </div>
            </div>

        </form>
    </div>
</div>

<?php include '../_foot.php'; ?>
