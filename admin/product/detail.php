<?php
require '../_base.php';
$current = 'product';
$_title = 'Product Detail';

$id = get('id');
if (!$id) redirect('../page/product.php');

$stm = $_db->prepare('SELECT p.*, c.category_name 
                      FROM product p 
                      LEFT JOIN category c 
                      ON p.category_id = c.category_id 
                      WHERE p.id = ?');
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

            <!-- 左邊：圖片 + 標題 + 描述 -->
            <div class="product-image-section">
                <?php if ($s->photo_name && file_exists("../upload/{$s->photo_name}")): ?>
                    <div class="product-image-frame">
                        <img src="../upload/<?= encode($s->photo_name) ?>" alt="<?= encode($s->title) ?>" class="product-image">
                    </div>
                <?php else: ?>
                    <div class="product-no-image">No Image</div>
                <?php endif; ?>

                <h1 class="product-title"><?= encode($s->title) ?></h1>

                <div style="margin:40px 0; 
                            padding-top:30px; 
                            border-top:2px dashed #eee;">

                    <strong style="color:#333; 
                                   font-size:19px; 
                                   display:block; 
                                   margin-bottom:15px">Description</strong>
                    <div style="background:#f8f9fa; 
                                padding:22px 28px; 
                                border-radius:14px; 
                                line-height:1.8; 
                                min-height:120px; 
                                font-size:16px; color:#444;">
                        <?= $s->description ? nl2br(encode($s->description)) : '<span style="color:#aaa;">No description provided.</span>' ?>
                    </div>
                </div>
            </div>

            <!-- 右邊：所有資訊 -->
            <div class="product-info-section">

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
                    <span class="info-price">RM <?= number_format($s->price, 0) ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Stock</span>
                    <span class="info-label"><?= encode($s->stock) ?></span>
                </div>

                <!-- 按鈕 -->
                <!-- 數量選擇器和購物車按鈕 -->
                <?php if ($s->stock > 0): ?>
                    <div style="margin: 30px 0; padding: 25px; background: #f9f9f9; border-radius: 12px; border: 1px solid #eaeaea;">
                        <div style="margin-bottom: 20px;">
                            <div style="color: #333; font-weight: 600; margin-bottom: 10px; font-size: 16px;">Quantity</div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <button type="button" class="qty-btn minus" 
                                        style="width: 40px; height: 40px; border: 2px solid #ddd; background: white; font-size: 18px; cursor: pointer; border-radius: 6px;">-</button>
                                <input type="number" id="quantity" value="1" min="1" max="<?= $s->stock ?>"
                                       style="width: 70px; height: 40px; text-align: center; border: 2px solid #ddd; border-radius: 6px; font-size: 16px; font-weight: 600;">
                                <button type="button" class="qty-btn plus" 
                                        style="width: 40px; height: 40px; border: 2px solid #ddd; background: white; font-size: 18px; cursor: pointer; border-radius: 6px;">+</button>
                                <span style="color: #666; font-size: 14px; margin-left: 10px;">Max: <?= $s->stock ?></span>
                            </div>
                        </div>

                        <?php if (isset($_SESSION['customer_id'])): ?>
                            <a href="../cart/add.php?id=<?= encode($s->id) ?>&return=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
                               class="btn-add-to-cart"
                               style="width: 100%; padding: 14px 20px; background: #3498db; color: white; text-align: center; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; display: block; transition: background 0.3s;">
                                🛒 Add to Cart
                            </a>
                        <?php else: ?>
                            <div style="text-align: center;">
                                <div style="color: #666; margin-bottom: 15px; font-size: 15px;">Please login to add to cart</div>
                                <div style="display: flex; gap: 10px;">
                                    <a href="/customer/login.php?return=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
                                       style="flex: 1; padding: 12px; background: #3498db; color: white; text-align: center; text-decoration: none; border-radius: 6px; font-weight: 600;">
                                        Login
                                    </a>
                                    <a href="/customer/register.php" 
                                       style="flex: 1; padding: 12px; background: #95a5a6; color: white; text-align: center; text-decoration: none; border-radius: 6px; font-weight: 600;">
                                        Register
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div style="margin: 30px 0; padding: 25px; background: #f9f9f9; border-radius: 12px; border: 1px solid #eaeaea; text-align: center;">
                        <div style="color: #e74c3c; font-weight: 600; margin-bottom: 15px; font-size: 16px;">Out of Stock</div>
                        <p style="color: #666;">This product is currently unavailable.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../_foot.php'; ?>