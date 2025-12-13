<?php
require '../_base.php';
include '../../_head.php';
include '../../_header.php';
$current = 'cart';
$_title = 'Shopping Cart';

if (!isset($_SESSION['customer_id'])) {
    temp('info', 'Please login to view your cart');
    redirect('../../page/login.php');
}

$customer_id = $_SESSION['customer_id'];

if (is_post()) {
    $action   = req('action');
    $id       = req('id');
    $quantity = req('quantity', 0);

    if ($action === 'update') {
    update_cart($id, $quantity, $customer_id, 'update');
    } elseif ($action === 'remove') {
        remove_from_cart($id, $customer_id);
    } elseif ($action === 'clear') {
        clear_cart($customer_id);
    }

    redirect();
}

$cart_items = get_cart_items($_db, $customer_id);
$cart_total = get_cart_total($cart_items);
?>

<link rel="stylesheet" href="../../css/cart.css">

<style>
/* quantity selector */
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
    width: 42px;
    height: 42px;
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
.quantity {
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

.remove-btn {
    height: 36px;
    padding: 0 14px;
    display: flex;
    align-items: center; 
    justify-content: center;
    margin-top: -2px;    
    white-space: nowrap;
}
</style>

<link rel="stylesheet" href="../../css/qty.css">
<link rel="stylesheet" href="../../css/customer.css">

<div class="cart-container">
    <div class="cart-header">
        <h1>Shopping Cart</h1>
        <div class="cart-count">
            <?= count($cart_items) ?> item<?= count($cart_items) !== 1 ? 's' : '' ?>
        </div>
    </div>

<?php if (empty($cart_items)): ?>

    <div class="cart-empty">
        <h2>Your cart is empty</h2>
        <a href="../page/product.php" class="continue-shopping">Continue Shopping</a>
    </div>

<?php else: ?>

<div class="cart-content">

<!-- left -->
<div class="cart-items">

<?php foreach ($cart_items as $item): ?>
<div class="cart-item" data-stock="<?= $item->stock ?>">

    <div class="cart-item-image">
        <?php if ($item->photo_name && file_exists("../upload/{$item->photo_name}")): ?>
            <img src="../upload/<?= encode($item->photo_name) ?>">
        <?php endif; ?>
    </div>

    <div class="cart-item-details">
        <a class="cart-item-title"><?= encode($item->title) ?></a>
        <div class="cart-item-author">by <?= encode($item->author) ?></div>
        <div class="cart-item-price">RM <?= number_format($item->price,2) ?> per 1</div>

        <div class="cart-item-actions">
        <form method="post" class="update-form">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?= $item->id ?>">

    <div class="quantity-control">
        <div class="qty-btn minus" role="button">−</div>

        <input
            type="text"
            class="quantity"
            name="quantity"
            value="<?= $item->quantity ?>"
            autocomplete="off"
        >

        <div class="qty-btn plus" role="button">+</div>
    </div>
</form>

            <form method="post"
            onsubmit="return confirm('Remove this item from cart?')">
                <input type="hidden" name="action" value="remove">
                <input type="hidden" name="id" value="<?= $item->id ?>">
                <button class="remove-btn">Remove</button>
            </form>

        </div>
    </div>

    <div class="cart-item-subtotal">
        <span class="subtotal-label">Subtotal</span>
        <div class="subtotal-amount">
            RM <?= number_format($item->subtotal,2) ?>
        </div>
    </div>

</div>
<?php endforeach; ?>


<div class="cart-actions">
    <a href="../page/product.php" class="continue-shopping">← Continue Shopping</a>

    <form method="post">
        <input type="hidden" name="action" value="clear">
        <button class="clear-cart-btn"
            onclick="return confirm('Clear cart?')">
            Clear Cart
        </button>
    </form>
</div>

</div>

<!-- right -->
<div class="cart-summary">
    <h2 class="summary-title">Order Summary</h2>

    <div class="summary-row">
        <span>Subtotal</span>
        <span>RM <?= number_format($cart_total,2) ?></span>
    </div>

    <div class="summary-total">
        <strong>Total</strong>
        <strong>RM <?= number_format($cart_total,2) ?></strong>
    </div>

    <form action="../page/checkout.php" method="post">
    <button type="submit" class="checkout-btn">
        Proceed to Checkout
    </button>
</form>

</div>

</div>
<?php endif; ?>
</div>

<script>
$(document).ready(function () {

    $('.cart-item').each(function () {

        const $item     = $(this);
        const $form     = $item.find('form.update-form');
        const $qtyInput = $item.find('.quantity');
        const $minusBtn = $item.find('.qty-btn.minus');
        const $plusBtn  = $item.find('.qty-btn.plus');

        const maxStock = parseInt($item.data('stock')) || 1;

        function normalizeQty() {
            let qty = parseInt($qtyInput.val());

            if (isNaN(qty) || qty < 1) qty = 1;
            if (qty > maxStock) qty = maxStock;

            $qtyInput.val(qty);

            $minusBtn.prop('disabled', qty <= 1);
            $plusBtn.prop('disabled', qty >= maxStock);

            return qty;
        }

        normalizeQty();

        $minusBtn.on('click', function () {
            let qty = normalizeQty();
            if (qty > 1) {
                $qtyInput.val(qty - 1);
                $form.submit();
            }
        });

        $plusBtn.on('click', function () {
            let qty = normalizeQty();
            if (qty < maxStock) {
                $qtyInput.val(qty + 1);
                $form.submit();
            }
        });

        $qtyInput.on('change', function () {
            normalizeQty();
            $form.submit();
        });
    });

});
</script>

<?php include '../../_footer.php'; ?>
