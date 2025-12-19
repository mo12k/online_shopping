<?php
require '../_base.php';
include '../../_head.php';
include '../../_header.php';
$current = 'cart';
$_title = 'Shopping Cart';

if (!isset($_SESSION['customer_id'])) {
    redirect('../../page/login.php');
    exit;
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


    <style>
    .cart-info-text {
        margin: 20px 0 30px;
        color: #666;
        font-size: 15px;
        text-align: center;
    }

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

    </style>

    <link rel="stylesheet" href="/css/cart.css">

<div class="cart-container">

    <div class="cart-header">
        <h1>Shopping Cart</h1>
        <div class="cart-count">
            <?= count($cart_items) ?> item<?= count($cart_items) !== 1 ? 's' : '' ?>
        </div>
    </div>

    <p class="cart-info-text">
        Showing <?= count($cart_items) ?> item<?= count($cart_items) !== 1 ? 's' : '' ?>
        in your cart
    </p>

<?php if (empty($cart_items)): ?>

    <div class="cart-empty">
        <h2>Your cart is empty</h2>
        <a href="../page/product.php" class="continue-shopping">Continue Shopping</a>
    </div>

<?php else: ?>

<div class="cart-content">

<div class="cart-items">

<?php foreach ($cart_items as $item): ?>
<div class="cart-item" data-stock="<?= $item->stock ?>">

    <div class="cart-item-image">
        <?php if ($item->photo_name && file_exists("../../admin/upload/{$item->photo_name}")): ?>
            <img src="../../admin/upload/<?= encode($item->photo_name) ?>">
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

                <div class="quantity-control-cart">
                    <button type="button" class="qty-dec">−</button>
                    <input type="text" name="quantity" class="qty-input"
                           value="<?= $item->quantity ?>" autocomplete="off">
                    <button type="button" class="qty-inc">+</button>
                </div>

            </form>

            <form method="post" onsubmit="return confirm('Remove this item from cart?')">
                <input type="hidden" name="action" value="remove">
                <input type="hidden" name="id" value="<?= $item->id ?>">
                <button class="remove-btn">Remove</button>
            </form>
        </div>
                <div class="stock-info">
                    <?php if ($item->stock > 0): ?>
                        <span class="in-stock">Max: <?= $item->stock ?> available</span>
                    <?php else: ?>
                        <span class="out-of-stock">Out of stock</span>
                    <?php endif; ?>
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
        <button class="clear-cart-btn" onclick="return confirm('Clear cart?')">
            Clear Cart
        </button>
    </form>
</div>

</div>

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
$(function () {
    $('.cart-item').each(function () {

        const item = $(this);
        const form = item.find('.update-form');
        const input = item.find('.qty-input');
        const dec = item.find('.qty-dec');
        const inc = item.find('.qty-inc');
        const max = parseInt(item.data('stock')) || 1;

        function normalize() {
            let v = parseInt(input.val());
            if (isNaN(v) || v < 1) v = 1;
            if (v > max) v = max;
            input.val(v);
            dec.prop('disabled', v <= 1);
            inc.prop('disabled', v >= max);
            return v;
        }

        normalize();

        dec.on('click', function () {
            let v = normalize();
            if (v > 1) {
                input.val(v - 1);
                form.submit();
            }
        });

        inc.on('click', function () {
            let v = normalize();
            if (v < max) {
                input.val(v + 1);
                form.submit();
            }
        });

        input.on('change', function () {
            normalize();
            form.submit();
        });
    });
});
</script>

<?php include '../../_footer.php'; ?>
