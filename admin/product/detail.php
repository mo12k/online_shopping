<?php
require '../_base.php';
$current = 'product';
$_title = 'Product Detail';

admin_require_login();

$id = get('id');
if (!$id) redirect('../page/product.php');

$stm = $_db->prepare('SELECT p.*, c.category_name FROM product p LEFT JOIN category c ON p.category_id = c.category_id WHERE p.id = ?');
$stm->execute([$id]);
$s = $stm->fetch();

if (!$s) {
    temp('info', 'Product not found');
    redirect('../page/product.php');
}

include '../_head.php';
?>

<link rel="stylesheet" href="/css/product-detail.css">

<div class="content">
    <div class="product-detail-container">
        <div class="product-detail-wrapper">

         
          
<div class="product-image-section">
    <?php if ($s->photo_name && file_exists("../upload/{$s->photo_name}")): ?>
        <div class="product-image-frame">
            <img src="../upload/<?= $s->photo_name ?>" 
                 alt="<?= $s->title ?>" 
                 class="product-image">
        </div>
    <?php else: ?>
        <div class="product-no-image">No Image</div>
    <?php endif; ?>

    <h1 class="product-title"><?= $s->title ?></h1>

    
    <div style="margin:40px 0; padding-top:30px; border-top:2px dashed #eee;">
        <strong style="color:#333; font-size:19px; display:block; margin-bottom:15px;">Description</strong>
        <div style="background:#f8f9fa; padding:22px 28px; border-radius:14px; line-height:1.8; min-height:120px; font-size:16px; color:#444;">
            <?= $s->description ? nl2br($s->description) : '<span style="color:#aaa;">No description provided.</span>' ?>
        </div>
    </div>
</div>

            <!-- 右邊：所有資訊 -->
            <div class="product-info-section">
                <div class="product-id-label">Product ID</div>
                <div class="product-id-value"><?= encode($s->id) ?></div>

                <div class="info-row">
                    <span class="info-label">Author</span>
                    <span><?= encode($s->author) ?: '<em style="color:#aaa;">Not specified</em>' ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Category</span>
                    <span class="info-category"><?= encode($s->category_name) ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Price</span>
                    <span class="info-price">RM <?= number_format($s->price, 2) ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Stock</span>
                    <span class="<?= $s->stock <= 10 ? 'info-stock-low' : 'info-stock-normal' ?>">
                        <?= $s->stock ?> unit<?= $s->stock <= 10 ? ' <strong>(Low stock!)</strong>' : '' ?>
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="info-status <?= $s->status ? 'status-published' : 'status-draft' ?>">
                        <?= $s->status ? 'Published' : 'Draft' ?>
                    </span>
                </div>

                <!-- Created & Updated -->
                <div class="datetime-grid">
                    <div class="datetime-item">
                        <div class="datetime-title">Created Date</div>
                        <div class="datetime-value created-value">
                            <?= date('Y-m-d H:i:s', strtotime($s->created_date)) ?>
                        </div>
                    </div>
                    <div class="datetime-item">
                        <div class="datetime-title">Last Update</div>
                        <div class="datetime-value updated-value">
                            <?= $s->updated_date ? date('Y-m-d H:i:s', strtotime($s->updated_date)) : '<em style="color:#999;">Never updated</em>' ?>
                        </div>
                    </div>
                </div>

                <!-- 按鈕 -->
                <div class="product-actions">
                    <button data-get="../product/update.php?id=<?= encode($s->id) ?>" class="btn-edit">Edit Product</button>
                    <a href="/admin/page/product.php" class="btn-back">Back to List</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../_foot.php'; ?>