<?php
require '../_base.php';
include '../../_head.php';
include '../../_header.php';

$current = 'product';
$_title = 'Product Detail';

$hash = get('id');
$id = decode_id($hash);

if ($id <= 0) {
    redirect('../page/product.php');
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

    if (!isset($_SESSION['customer_id'])) {
    temp('error', 'Please login as customer to add items to cart.');
    redirect();
    exit;
}

    $product_id = req('id');
    $quantity   = (int) req('quantity');

    // get customer_id
    $customer_id = $_SESSION['customer_id'] ?? null;

    // validate stock
    $stm = $_db->prepare('SELECT stock, title FROM product WHERE id = ?');
    $stm->execute([$product_id]);
    $product = $stm->fetch();

    if ($product && $quantity > 0 && $quantity <= $product->stock) {

        add_to_cart($product_id, $quantity, $customer_id);

        temp('success', "Added <strong>{$product->title}</strong> (x{$quantity}) to cart!");

    } else {

        temp('error', 'Invalid quantity or insufficient stock!');
    }

    
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
        cursor: pointer;
        font-size: 20px;
        font-weight: bold;
        line-height: 1;              
        display: flex;
        align-items: center;
        justify-content: center;
        height: 20px;
        width: 20px;
        margin-left: 12px;
        margin-top: 2px;
    }

    /* Quantity Selector */
    .quantity-selector {
        margin: 20px 0;
        padding: 20px;
        background: #FAF7F2; 
        border-radius: 12px;
        border: 1px solid #E4DCD3; 
        text-align: center;
    }

    .quantity-label {
        display: block;
        color: #4E342E; 
        font-weight: 600;
        margin-bottom: 10px;
        font-size: 16px;
    }

    .quantity-control {
        display: flex;
        align-items: center;        
        justify-content: center;
        gap: 12px;
    }

    /* + / - button */
    .qty-btn {
        width: 38px;
        height: 38px;
        border: 2px solid #D7CCC8; 
        background: #FFF;
        font-size: 22px;
        cursor: pointer;
        border-radius: 8px; 
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .qty-btn:hover {
        background: #F2EBE5; 
        border-color: #6D4C41; 
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

        display: flex;              
        align-items: center;        
        justify-content: center;    

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

    .product-actions {
        margin-top: 25px;
    }

</style>

    <div class="content">
        
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

            <!-- left picture + summary + .. -->
            <div class="product-image-section">
                <?php if ($s->photo_name && file_exists("../../admin/upload/{$s->photo_name}")): ?>
                    <div class="product-image-frame">
                        <img src="../../admin/upload/<?= encode($s->photo_name) ?>" alt="<?= encode($s->title) ?>" class="product-image">
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

            <!-- right -->
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
                    <span class="info-price">RM <?= number_format($s->price, 2) ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Stock</span>
                    <span class="info-label <?= $s->stock > 0 ? 'in-stock' : 'out-of-stock' ?>">
                        <?= $s->stock > 0 ? $s->stock : 'Out of Stock' ?>
                    </span>
                </div>

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
    
    const $quantityInput = $('#quantity');
    const $minusBtn = $('.qty-btn.minus');
    const $plusBtn = $('.qty-btn.plus');
    const maxStock = <?= $s->stock ?>;
    
    
    function updateButtonState() {
        const currentValue = parseInt($quantityInput.val());
        $minusBtn.prop('disabled', currentValue <= 1);
        $plusBtn.prop('disabled', currentValue >= maxStock);
    }
    
    
    updateButtonState();
    
    
    $minusBtn.on('click', function() {
        let value = parseInt($quantityInput.val());
        if (value > 1) {
            $quantityInput.val(value - 1);
            updateButtonState();
        }
    });
    
    
    $plusBtn.on('click', function() {
        let value = parseInt($quantityInput.val());
        if (value < maxStock) {
            $quantityInput.val(value + 1);
            updateButtonState();
        }
    });
    
    // validate input change
    $quantityInput.on('change', function() {
        let value = parseInt($(this).val());
        if (isNaN(value) || value < 1) {
            $(this).val(1);
        } else if (value > maxStock) {
            $(this).val(maxStock);
        }
        updateButtonState();
    });
    
    // validate before upload form
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