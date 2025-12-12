<?php
require '../_base.php';

$current = 'product';
$_title = 'Add New Product';



if (is_post()) {
    $title       = trim(req('title'));
    $author      = trim(req('author') ?? '');
    $category_id = req('category_id');
    $price       = (float)req('price');
    $stock       = (int)req('stock');
    $status      = req('status') ? 1 : 0;
    $description = trim(req('description') ?? '');
    $f       = get_file('photo');

    // Validation
    if ($title === '') {
        $_err['title'] = 'Title is required';
    } elseif (mb_strlen($title) > 50) {
        $_err['title'] = 'Title must not exceed 50 characters';
    }

    if (mb_strlen($author)> 50) {
        $_err['author'] = 'Author name too long (max 50)';
    }elseif ($title === '') {
         $_err['author'] = 'Author is required';
    }

    if ($category_id === '' || !array_key_exists($category_id, $_category)) {
        $_err['category_id'] = 'Please select a category';
    }

     if ($price == '') {
        $_err['price'] = 'Required';
    }
    else if (!is_money($price)) {
        $_err['price'] = 'Must be money';
    }
    else if ($price < 0.01 || $price > 99.99) {
        $_err['price'] = 'Must between 0.01 - 99.99';
    }

    if ($stock == '') {
        $_err['stock'] = 'Required';
    }
    else if ($stock < 0) {
        $_err['stock'] = 'Stock cannot be negative';
    }

    if (!$f) {
        $_err['photo'] = 'Required';
    }
    else if (!str_starts_with($f->type, 'image/')) {
        $_err['photo'] = 'Must be image';
    }
    else if ($f->size > 5 * 1024 * 1024) {
        $_err['photo'] = 'Maximum 5MB';
    }

    $required = ['title', 'category_id', 'price', 'stock'];
    foreach ($required as $field) {
        if (req($field) === '' || req($field) === null) {
            $_err[$field] ??= 'This field is required';
        }
    }

    if (!$_err) {
        $photo_name = save_photo($f,"../upload");

        //檢查 database 有沒有 autoincrement 在合并的時候
       

        $_db->prepare("
             INSERT INTO product (id, title, author, category_id, price, stock, status, description, photo_name)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ")->execute([$id, $title, $author, $category_id, $price, $stock, $status, $description, $photo_name]);

        temp('info', "Product $title added successfully!  ");
        redirect('../page/product.php');
    }
}

include '../_head.php';
?>

<div class="content">
   

    <div class="form-container">
        <form method="post" enctype="multipart/form-data">

            <div class="form-grid">

                            <!-- Photo Upload Section -->
                             
                <div class="photo-section">
                <label class="upload-label">
                    <?= html_file('photo', 'image/*') ?>
                    <div class="preview-wrapper" id="preview-wrapper">
                      <img src="<?= ($f = get_file('photo')) ? $f->temp_url : '/images/no-photo.jpg' ?>" id="preview">
                      
                    </div>
                </label>

                
                <?= err('photo') ? '<div class="err-photo">' . err('photo') . '</div>' : '' ?>
                
                <small style="color:#888; display:block; margin-top:8px;">
                    Recommended: 800×1000px or larger • Max 5MB
                </small>
            </div>

                <!-- Form Fields -->
                <div class="fields-section">
                    <div class="auto-id">
                        Product ID will be generated automatically
                    </div>

                    <label><span class="req">*</span> Title</label>
                    <?= html_text('title', 'placeholder="Enter product title" required') ?>
                    <?= err('title') ?>

                    <label>Author `</label>
                    <?= html_text('author', 'placeholder="Enter author name"') ?>

                    <label><span class="req">*</span> Category</label>
                    <?= html_select('category_id', $_category, "Selcet Category" , 'required') ?>
                    <?= err('category_id') ?>

                    <div class="row">
                        <div>
                            <label><span class="req">*</span> Price(RM)</label>
                            <?= html_number('price', 1.00, 999999, '0.01', 'step="0.01" min="1.00" placeholder="1.00" required') ?>
                            <?= err('price') ?>
                        </div>
                        <div>
                            <label><span class="req">*</span> Stock</label>
                            <?= html_number('stock', 0, 99999, 1, 'min="0" required') ?>
                            <?= err('stock') ?>
                        </div>
                    </div>

                    <label>Status</label>
                    <?= html_status_toggle('status', $_status, false) ?>

                    <label>Description <small>(optional)</small></label>
                    <?= html_textarea('description', 'rows="6" placeholder="Enter product description, features ,......."') ?>

                    <div class="actions">
                        <button  class="btn-primary">Add Product</button>
                        <a href="/admin/page/product.php" class="btn-cancel">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>





<?php include '../_foot.php'; ?>