<?php
require '../_base.php';
include '../../_head.php';
include '../../_header.php';
$current = 'cart';
$_title = 'Shopping Cart';

// if is not customer
if (!isset($_SESSION['customer_id'])) {
    temp('info', 'Please login to view your cart');
    redirect('../../page/login.php');
}

$customer_id = $_SESSION['customer_id'] ?? null;

if (is_post()) {
    $action = req('action');
    $id = req('id');
    $quantity = req('quantity', 0);
    
    switch ($action) {
        case 'update':
            // ✅ 添加customer_id参数
            update_cart($id, $quantity, $customer_id);
            break;
            
        case 'remove':
            // ✅ 添加customer_id参数
            remove_from_cart($id, $customer_id);
            break;
            
        case 'clear':
            // ✅ 使用新的clear_cart函数
            clear_cart($customer_id);
            break;
    }

    redirect();
}

// get cart item
$cart_items = get_cart_items($_db, $customer_id);
$cart_total = get_cart_total($cart_items);

?>
<link rel="stylesheet" href="../../css/cart.css">

<div class="cart-container">
    <div class="cart-header">
        <h1>Shopping Cart</h1>
        <div class="cart-count">
            <?= count($cart_items) ?> item<?= count($cart_items) !== 1 ? 's' : '' ?> in your cart
        </div>
    </div>
    
    <?php if (empty($cart_items)): ?>
        <div class="cart-empty">
            <div class="cart-empty-icon">🛒</div>
            <h2>Your cart is empty</h2>
            <p style="color: #666; margin-bottom: 30px;">Add some books to get started!</p>
            <a href="../page/product.php" class="continue-shopping">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-content">
            <div class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item" data-id="<?= $item->id ?>">
                        <div class="cart-item-image">
                            <?php if ($item->photo_name && file_exists("../upload/{$item->photo_name}")): ?>
                                <img src="../upload/<?= encode($item->photo_name) ?>" alt="<?= encode($item->title) ?>">
                            <?php else: ?>
                                <div style="color: #999; font-size: 14px;">No Image</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="cart-item-details">
                            <a href="../page/product-detail.php?id=<?= $item->id ?>" class="cart-item-title">
                                <?= encode($item->title) ?>
                            </a>
                            
                            <?php if ($item->author): ?>
                                <div class="cart-item-author">by <?= encode($item->author) ?></div>
                            <?php endif; ?>
                            
                            <div class="cart-item-price">
                                RM <?= number_format($item->price, 2) ?> per 1
                            </div>
                            
                            <div class="cart-item-actions">
                                <form method="post" class="quantity-control-cart">
                                    <input type="hidden" name="id" value="<?= $item->id ?>">
                                    <input type="hidden" name="action" value="update">
                                    <button type="button" class="qty-dec">-</button>
                                    <input type="number" name="quantity" value="<?= $item->quantity ?>" 
                                           min="1" max="<?= $item->stock ?>" class="qty-input">
                                    <button type="button" class="qty-inc">+</button>
                                    <button type="submit" style="display:none;">Update</button>
                                </form>
                                
                                <?php if ($item->quantity > $item->stock): ?>
                                    <div class="stock-warning">
                                        Only <?= $item->stock ?> available
                                    </div>
                                <?php endif; ?>
                                
                                <form method="post" class="remove-form">
                                    <input type="hidden" name="id" value="<?= $item->id ?>">
                                    <input type="hidden" name="action" value="remove">
                                    <button type="submit" class="remove-btn">Remove</button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="cart-item-subtotal">
                            <span class="subtotal-label">Subtotal</span>
                            <div class="subtotal-amount">
                                RM <?= number_format($item->subtotal, 2) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="cart-actions">
                    <a href="../page/product.php" class="continue-shopping">← Continue Shopping</a>
                    
                    <form method="post">
                        <input type="hidden" name="action" value="clear">
                        <button type="submit" class="clear-cart-btn" 
                                onclick="return confirm('Are you sure you want to clear your cart?')">
                            Clear Cart
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="cart-summary">
                <h2 class="summary-title">Order Summary</h2>
                
                <div class="summary-row">
                    <span class="summary-label">Subtotal</span>
                    <span class="summary-amount">RM <?= number_format($cart_total, 2) ?></span>
                </div>
                
                <div class="summary-total">
                    <span class="total-label">Total</span>
                    <span class="total-amount">RM <?= number_format($cart_total, 2) ?></span>
                </div>
                
                <button type="button" class="checkout-btn" onclick="checkout()">
                    Proceed to Checkout
                </button>
                
                <p style="text-align: center; margin-top: 15px; color: #666; font-size: 14px;">
                    Free shipping on all orders
                </p>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    // button + / -
    $('.qty-dec').on('click', function() {
        const $form = $(this).closest('form');
        const $input = $form.find('.qty-input');
        let value = parseInt($input.val());
        if (value > 1) {
            $input.val(value - 1);
            $form.find('button[type="submit"]').click();
        }
    });
    
    $('.qty-inc').on('click', function() {
        const $form = $(this).closest('form');
        const $input = $form.find('.qty-input');
        const maxStock = parseInt($input.attr('max'));
        let value = parseInt($input.val());
        if (value < maxStock) {
            $input.val(value + 1);
            $form.find('button[type="submit"]').click();
        }
    });
    
    // change when type input
    $('.qty-input').on('change', function() {
        const $form = $(this).closest('form');
        const maxStock = parseInt($(this).attr('max'));
        let value = parseInt($(this).val());
        
        if (isNaN(value) || value < 1) {
            $(this).val(1);
        } else if (value > maxStock) {
            $(this).val(maxStock);
        }
        
        $form.find('button[type="submit"]').click();
    });
    
    // remove comfirm
    $('.remove-form').on('submit', function() {
        return confirm('Are you sure you want to remove this item from your cart?');
    });
});

function checkout() {
    //validate stock
    let hasStockIssue = false;
    $('.cart-item').each(function() {
        const $item = $(this);
        const quantity = parseInt($item.find('.qty-input').val());
        const maxStock = parseInt($item.find('.qty-input').attr('max'));
        
        if (quantity > maxStock) {
            hasStockIssue = true;
            $item.addClass('stock-error');
        }
    });
    
    if (hasStockIssue) {
        alert('Some items in your cart have insufficient stock. Please adjust quantities before checkout.');
        return;
    }
    
    window.location.href = '../page/checkout.php';
}
</script>

<?php include '../../_footer.php'; ?>