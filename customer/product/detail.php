<?php
require '../_base.php';
include '../../_head.php';
include '../../_header.php';
$current = 'product';
$_title = 'Product Detail';

$id = get('id');
if (!$id){ redirect('../page/product.php');
exit;
}
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
    exit;
}

if (is_post()) {

    $product_id = req('id');
    $quantity   = (int) req('quantity');

    // get customer_id
    $customer_id = $_SESSION['customer_id'] ?? null;

    // validate stock
    $stm = $_db->prepare('SELECT stock, title FROM product WHERE id = ?');
    $stm->execute([$product_id]);
    $product = $stm->fetch();

    if ($product && $quantity > 0 && $quantity <= $product->stock) {

        update_cart($product_id, $quantity, $customer_id);

        // ✅ 页面级成功信息（不会进 head）
        temp('success', "Added <strong>{$product->title}</strong> (x{$quantity}) to cart!");

    } else {

        // ✅ 页面级错误信息（不会进 head）
        temp('error', 'Invalid quantity or insufficient stock!');
    }

    // PRG 模式，防止重复提交
    redirect();
    exit;
}


$arr = $_db->query('SELECT * FROM product');
?>

<style>

.message {
    display: flex;
    align-items: center;
}

.message button {
    all: unset;                 /* 清掉浏览器默认样式 */
    cursor: pointer;
    font-size: 20px;
    font-weight: bold;
    line-height: 1;              /* 关键：防止文字下沉 */
    display: flex;
    align-items: center;
    justify-content: center;
    height: 20px;
    width: 20px;
    margin-left: 12px;
}

/* Quantity Selector — Warm Bookstore Style */
.quantity-selector {
    margin: 20px 0;
    padding: 20px;
    background: #FAF7F2; /* 柔和米白底 */
    border-radius: 12px;
    border: 1px solid #E4DCD3; /* 暖灰边框 */
    text-align: center;
}

.quantity-label {
    display: block;
    color: #4E342E; /* 深咖啡色，书店风 */
    font-weight: 600;
    margin-bottom: 10px;
    font-size: 16px;
}

.quantity-control {
    display: flex;
    align-items: center;         /* 强制同一中线 */
    justify-content: center;
    gap: 12px;
}

/* + / - 按钮 */
.qty-btn {
    width: 42px;
    height: 42px;
    border: 2px solid #D7CCC8; /* 柔和暖灰边框 */
    background: #FFF;
    font-size: 22px;
    cursor: pointer;
    border-radius: 8px; /* 比较圆，有手工感 */
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.qty-btn:hover {
    background: #F2EBE5; /* 暖色 hover */
    border-color: #6D4C41; /* 木质深棕 hover 边框 */
}

.qty-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

/* Quantity input */
#quantity {
    width: 72px;
    height: 42px;

    border: 2px solid #D7CCC8;
    border-radius: 8px;
    background: #FFF;

    font-size: 16px;
    font-weight: 600;
    color: #4E342E;

    padding: 0;
    margin: 0;

    text-align: center;

    display: flex;              /* 🔥 关键 */
    align-items: center;        /* 🔥 关键 */
    justify-content: center;    /* 🔥 关键 */

    box-sizing: border-box;
}


/* Stock text */
.stock-info {
    font-size: 15px;
    margin-top: 10px;
}

.in-stock {
    color: #2E7D32;
    font-weight: 600;
}

.out-of-stock {
    color: #D32F2F;
    font-weight: 600;
}

.add-to-cart-btn {
    padding: 16px 70px;
    background: #6D4C41; 
    color: #FFF; 
    border: none;
    border-radius: 50px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    box-shadow: 0 10px 25px rgba(109, 76, 65, 0.25);
    text-decoration: none;
    display: inline-block;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.add-to-cart-btn:hover {
    background: #5A3E34; 
    transform: translateY(-2px); 
}

.add-to-cart-btn:disabled {
    background: #BCAAA4; 
    cursor: not-allowed;
}

/* Product actions spacing */
.product-actions {
    margin-top: 25px;
}

</style>
<link rel="stylesheet" href="../../css/app.css">
<link rel="stylesheet" href="../../css/customer.css">

    <div class="content">
        
        <!-- 消息提示区域 -->
        <?php if ($msg = temp('success')): ?>
                    <div class="message success" style="
                        max-width: 800px;
                        margin: 20px auto;
                        background: #d4edda;
                        color: #155724;
                        padding: 15px 20px;
                        border-radius: 8px;
                        border: 1px solid #c3e6cb;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span><?= $msg ?></span>
                        </div>

                        <button onclick="this.parentElement.remove()" style="
                            background: none;
                            border: none;
                            font-size: 20px;
                            color: #155724;
                            cursor: pointer;
                            padding: 0 5px;
                        ">x</button>
                </div>
            <?php endif; ?>
            
            <?php if ($msg = temp('error')): ?>
                    <div class="message error" style="
                        max-width: 800px;
                        margin: 20px auto;
                        background: #f8d7da;
                        color: #721c24;
                        padding: 15px 20px;
                        border-radius: 8px;
                        border: 1px solid #f5c6cb;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span><?= $msg ?></span>
                    </div>

                    <button onclick="this.parentElement.remove()">x</button>
            </div>
        <?php endif; ?>
        
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

                <div style="margin:40px 0; padding-top:30px; border-top:2px dashed #eee;">
                    <strong style="color:#333; font-size:19px; display:block; margin-bottom:15px">Description</strong>
                    <div style="background:#f8f9fa; padding:22px 28px; border-radius:14px; line-height:1.8; min-height:120px; font-size:16px; color:#444;">
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
                    <span class="info-label <?= $s->stock > 0 ? 'in-stock' : 'out-of-stock' ?>">
                        <?= $s->stock > 0 ? $s->stock : 'Out of Stock' ?>
                    </span>
                </div>

            <!-- 数量选择器 -->
            <div class="product-action">     
                <div class="quantity-selector">
                    <?php if ($s->stock > 0): ?>
                        <form method="post" id="add-to-cart-form">
                            <input type="hidden" name="id" value="<?= $s->id ?>">
                            
                            <span class="quantity-label">Quantity</span>
                            
                            <div class="quantity-control">
                                <div class="qty-btn minus" role="button" aria-label="Decrease quantity">−</div>

                                <input
                                    type="text"
                                    id="quantity"
                                    name="quantity"
                                    value="1"
                                    autocomplete="off"
                                >


                                <div class="qty-btn plus" role="button" aria-label="Increase quantity">+</div>
                            </div>

                            
                            <div class="stock-info">
                                <span class="in-stock">Max: <?= $s->stock ?> available</span>
                            </div>
                            
                            <button type="submit" class="add-to-cart-btn">
                                 Add to Cart
                            </button>
                        </form>
                    <?php else: ?>
                        <div style="text-align: center; padding: 20px;">
                            <div class="out-of-stock" style="margin-bottom: 15px; font-size: 16px;">Out of Stock</div>
                            <p style="color: #666;">This product is currently unavailable.</p>
                        </div>
                    <?php endif; ?>
                </div>
                </div>
                <div class="product-actions">
                    <a href="/customer/page/product.php" class="btn-back">Back to List</a>
                </div>
                
            
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // 获取元素
    const $quantityInput = $('#quantity');
    const $minusBtn = $('.qty-btn.minus');
    const $plusBtn = $('.qty-btn.plus');
    const maxStock = <?= $s->stock ?>;
    
    // 更新按钮状态
    function updateButtonState() {
        const currentValue = parseInt($quantityInput.val());
        $minusBtn.prop('disabled', currentValue <= 1);
        $plusBtn.prop('disabled', currentValue >= maxStock);
    }
    
    // 初始化按钮状态
    updateButtonState();
    
    // 减少数量
    $minusBtn.on('click', function() {
        let value = parseInt($quantityInput.val());
        if (value > 1) {
            $quantityInput.val(value - 1);
            updateButtonState();
        }
    });
    
    // 增加数量
    $plusBtn.on('click', function() {
        let value = parseInt($quantityInput.val());
        if (value < maxStock) {
            $quantityInput.val(value + 1);
            updateButtonState();
        }
    });
    
    // 输入框变化时验证
    $quantityInput.on('change', function() {
        let value = parseInt($(this).val());
        if (isNaN(value) || value < 1) {
            $(this).val(1);
        } else if (value > maxStock) {
            $(this).val(maxStock);
        }
        updateButtonState();
    });
    
    // 表单提交前的验证
    $('#add-to-cart-form').on('submit', function(e) {
        let quantity = parseInt($quantityInput.val());
        
        if (isNaN(quantity) || quantity < 1 || quantity > maxStock) {
            e.preventDefault();
            alert('Please select a valid quantity (1-' + maxStock + ')');
            $quantityInput.val(1);
            updateButtonState();
        }
        
    });
});
</script>

<?php include '../../_footer.php'; ?>