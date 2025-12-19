<?php
require '../_base.php';

admin_require_login();

$current = 'product';
$_title = 'Edit Product';

$id = req('id');


$stm = $_db->prepare('SELECT * FROM product WHERE id = ?');
$stm->execute([$id]);
$p = $stm->fetch();

if (!$p) {
    temp('info', 'Product not found');
    redirect('../page/product.php');
}


if (is_post()) {

    $title       = trim(req('title'));
    $author      = trim(req('author') ?? '');
    $category_id = req('category_id');
    $price       = (float)req('price');
    $stock       = (int)req('stock');
    $status      = req('status') ? 1 : 0;
    $description = trim(req('description') ?? '');
    $photo_name  = req('old_photo');

   
    if ($title === '') {
        $_err['title'] = 'Title is required';
    } elseif (mb_strlen($title) > 50) {
        $_err['title'] = 'Title must not exceed 50 characters';
    }

    if (mb_strlen($author) > 50) {
        $_err['author'] = 'Author name too long (max 50)';
    }

    if ($category_id === '' || !array_key_exists($category_id, $_category)) {
        $_err['category_id'] = 'Please select a category';
    }

    if ($price === '') {
        $_err['price'] = 'Required';
    } elseif (!is_money($price)) {
        $_err['price'] = 'Must be money';
    } elseif ($price < 0.01 || $price > 9999.99) {
        $_err['price'] = 'Must between 0.01 - 99.99';
    }

    if ($stock === '') {
        $_err['stock'] = 'Required';
    } elseif ($stock < 0) {
        $_err['stock'] = 'Stock cannot be negative';
    }

    
    $f = get_file('photo');

    if ($f && isset($f->tmp_name) && $f->error === 0) {

        if ($f->size > 5 * 1024 * 1024) {
            $_err['photo'] = 'Maximum 5MB';
        }
        elseif (!str_starts_with($f->type, 'image/')) {
            $_err['photo'] = 'Must be an image';
        }
        else {
            $new_photo = save_photo($f, '../upload');

            if ($new_photo) {
                if ($photo_name && file_exists("../upload/$photo_name")) {
                    @unlink("../upload/$photo_name");
                }
                $photo_name = $new_photo;
            } else {
                $_err['photo'] = 'Upload failed';
            }
        }
    }

   
    $isIdentityChanged =
        $title !== $p->title ||
        $author !== $p->author ||
        $category_id != $p->category_id;

    if (!$_err && $isIdentityChanged) {
        $stm = $_db->prepare(
            "SELECT COUNT(*) 
             FROM product 
             WHERE title = ? 
               AND author = ? 
               AND category_id = ?
               AND id <> ?"
        );
        $stm->execute([$title, $author, $category_id, $id]);

        if ($stm->fetchColumn()) {
            $_err['title'] = 'Another product with the same title already exists';
        }
    }

    // ---------- Update ----------
    if (!$_err) {
        $_db->prepare(
            "UPDATE product 
             SET title=?, author=?, category_id=?, price=?, stock=?, status=?, description=?, photo_name=? 
             WHERE id=?"
        )->execute([
            $title, $author, $category_id,
            $price, $stock, $status,
            $description, $photo_name, $id
        ]);

        temp('info', "Product ID $id updated successfully!");
        redirect('../page/product.php');
    }

}

else {
    $title       = $p->title;
    $author      = $p->author;
    $category_id = $p->category_id;
    $price       = $p->price;
    $stock       = $p->stock;
    $status      = $p->status;
    $description = $p->description;
    $photo_name  = $p->photo_name;
}

include '../_head.php';
?>

<div class="content">
    <div class="form-container">
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= encode($id) ?>">
            <input type="hidden" name="old_photo" value="<?= encode($photo_name) ?>">

            <div class="form-grid">


                <div class="photo-section">

                   
                    <div style="margin-bottom: 40px; text-align: center;">
                        <div style="color:#666; font-weight:600; font-size:18px; margin-bottom:15px;">
                            Currently Using
                        </div>
                        <div class="current-photo-wrapper">
                            <img src="<?= $photo_name ? '../upload/'.$photo_name : '/images/no-photo.jpg' ?>" 
                                 style="width:100%; max-width:420px; height:auto; border-radius:18px; box-shadow:0 12px 35px rgba(0,0,0,0.2);">
                        </div>
                        <small style="display:block; margin-top:10px; color:#27ae60; font-weight:500;">
                            <?= $photo_name ?: 'No image uploaded' ?>
                        </small>
                    </div>

                   
                    <div style="text-align: center;">
                        <div style="color:#d4380d; font-weight:600; font-size:19px; margin-bottom:15px;">
                            Update Picture
                        </div>

                        
                        <div style="display:inline-block; position:relative;">

                            
                            <label class="upload-label" style="cursor:pointer; display:block;">
                                <?= html_file('photo', 'image/*') ?>

                                <div class="preview-wrapper" id="preview-wrapper" style="pointer-events:none; position:relative;">
                                    <img src="/images/no-photo.jpg" id="preview" style="pointer-events:none;">
                                    <div class="upload-text" id="upload-text" style="pointer-events:none;">
                                        Click or drop image here<br>
                                        <small>Support JPG, PNG, WebP • Max 5MB</small>
                                    </div>
                                </div>
                            </label>


                            <span id="cancel-photo" style="display:none; position:absolute; top:8px; right:8px; 
                                  width:34px; height:34px; background:#d4380d; color:white; border-radius:50%; 
                                  font-size:20px; line-height:34px; text-align:center; cursor:pointer; 
                                  font-weight:bold; z-index:999; box-shadow:0 3px 10px rgba(0,0,0,0.4);">
                                ×
                            </span>

                        </div>

                        <?php if (err('photo')): ?>
                            <div class="err-photo"><strong>Warning</strong> <?= encode(err('photo')) ?></div>
                        <?php endif; ?>

                        <div id="new-photo-hint" style="margin-top:15px; font-size:15px; color:#888; font-style:italic;">
                            using current picture
                        </div>
                    </div>

                    <small style="color:#888; display:block; margin-top:25px; text-align:center;">
                        Recommended: 800×1000px or larger • Max 5MB
                    </small>
                </div>

               
                <div class="fields-section">
                    <div class="auto-id">Product ID: <strong><?= encode($id) ?></strong></div>

                    <label><span class="req">*</span> Title</label>
                    <?= html_text('title', "value='" . encode($title) . "' required") ?>
                    <?= err('title') ?>

                    <label>Author</label>
                    <?= html_text('author', "value='" . encode($author) . "'") ?>

                    <label><span class="req">*</span> Category</label>
                    <?= html_select('category_id', $_category, null, 'required') ?>
                    <?= err('category_id') ?>

                    <div class="row">
                        <div>
                            <label><span class="req">*</span> Price</label>
                            <?= html_number('price', 1.00, 99999, '0.01', "value='$price' step='0.01' min='1.00' required") ?>
                            <?= err('price') ?>
                        </div>
                        <div>
                            <label><span class="req">*</span> Stock</label>
                            <?= html_number('stock', 0, 99999, 1, "value='$stock' min='0' required") ?>
                            <?= err('stock') ?>
                        </div>
                    </div>

                    <label>Status</label>
                    <?= html_status_toggle('status', $_status, $status) ?>

                    <label>Description <small>(optional)</small></label>
                    <?= html_textarea('description', "rows='6'") ?>

                    <div class="actions">
                        <button  class="btn-primary">Update Product</button>
                        <a href="/admin/page/product.php" class="btn-cancel">Cancel</a>
                        
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


<?php include '../_foot.php'; ?>