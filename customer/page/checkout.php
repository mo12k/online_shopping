<?php

require '../_base.php';
$current = 'checkout';
$_title = 'Checkout';

if (!isset($_SESSION['customer_id'])) {
    temp('info', 'Please login to checkout');
    redirect('../../page/login.php');
    exit;
}

$customer_id = $_SESSION['customer_id'];

$cart_items = get_cart_items($_db, $customer_id);

if (empty($cart_items) && !isset($_SESSION['pending_order'])) {
    redirect('cart.php');
    exit;
}

$addresses = get_customer_addresses($customer_id);


if (is_post()) {
    
    $address_id = req('address_id');
    $payment_method = req('payment_method');

    if (!$address_id || !$payment_method) {
        redirect('checkout.php');
        exit;
    }

    $_SESSION['pending_order'] = [
        'customer_id'    => $customer_id,
        'address_id'     => $address_id,
        'payment_method' => $payment_method
    ];
    unset($_SESSION['payment_retry']);

    redirect('payment_checkout.php');
    exit;
}

foreach ($cart_items as $item) {
    if ($item->quantity > $item->stock) {
        redirect('cart.php');
        exit;
    }
}

include '../../_head.php';
include '../../_header.php';

?>

    <style>

    .checkout-container {
        min-width: 500px;
        max-width: 1000px;
        margin: 40px auto;
        padding: 0px 20px;
    }

    .checkout-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin-top: 30px;
    }

    .order-summary, .checkout-form {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .form-section {
        margin-bottom: 30px;
    }

    .form-section h3 {
        margin-bottom: 20px;
        color: #333;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 10px;
    }

    .address-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .address-option input[type="radio"] {
        margin: 0;
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .address-option {
        display: flex;
        gap: 15px;
        padding: 10px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .address-option:hover {
        border-color: #6d4c41;
    }

    .address-details {
        display: flex;
        flex: 1;              
        flex-wrap: wrap; 
    }

    .payment-methods {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .payment-option {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        cursor: pointer;
    }

    .payment-option:hover {
        border-color: #6d4c41;
    }

    .payment-option input[type="radio"] {
        margin: 0;
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        margin-top: 2px;
        
    }

    .order-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f0f0f0;
    }

    .order-total {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px solid #e0e0e0;
        font-size: 18px;
        text-align: right;
    }

    .btn-checkout {
        width: 100%;
        padding: 16px;
        background: #6d4c41;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-checkout:hover {
        background: #5a3e34;
        transform: translateY(-2px);
    }
    </style>

        <?php if ($msg = temp('error')): ?>
            <div style="
                max-width: 800px;
                max-height: 100px;
                margin: 20px auto;
                background: #f8d7da;
                color: #721c24;
                padding: 15px 20px;
                border-radius: 8px;
                border: 1px solid #f5c6cb;
                display: flex;
                align-items: center;
                justify-content: space-between;">
                
                <span><?= $msg ?></span>

                <button onclick="this.parentElement.remove()" style="
                    background: none;
                    border: none;
                    font-size: 20px;
                    color: #721c24;
                    cursor: pointer;
                    margin-top: 0px;">x</button>
            </div>
        <?php endif; ?>

<div class="checkout-container">
    <h1>Checkout</h1>
    
    <div class="checkout-content">
        
        <div class="order-summary">
            <h2>Order Summary</h2>
            <?php foreach ($cart_items as $item): ?>
                <div class="order-item">
                    <span class="item-name"><?= encode($item->title) ?> × <?= $item->quantity ?></span>
                    <span class="item-price">RM <?= number_format($item->subtotal, 2) ?></span>
                </div>
            <?php endforeach; ?>
            <div class="order-total">
                <strong>Total: RM <?= number_format(get_cart_total($cart_items), 2) ?></strong>
            </div>
        </div>
        
        
        <div class="checkout-form">
            <form method="post" id="checkout-form">
                <!-- choose adress -->
                <div class="form-section">
                    <h3>Delivery Address</h3>
                    <?php if (empty($addresses)): ?>
                        <div class="alert alert-warning">
                                No address found.
                                <a href="add_address.php?return=checkout"
                                style="
                                    display: inline-block;
                                    margin-left: 8px;
                                    padding: 6px 12px;
                                    background: #6d4c41;
                                    color: #fff;
                                    text-decoration: none;
                                    border-radius: 6px;
                                    font-size: 14px;
                                    font-weight: 500;
                                ">
                                    Add a new address
                                </a>
                            </div>
                    <?php else: ?>
                        <div class="address-list">
                            <?php foreach ($addresses as $address): ?>
                                <label class="address-option">
                                    <input type="radio" name="address_id" value="<?= $address->address_id ?>" required>
                                    <div class="address-details">
                                        <strong><?= encode($address->address) ?></strong>, <?= encode($address->city) ?>, <?= encode($address->state) ?>, <?= encode($address->postcode) ?>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- payment method -->
                <div class="form-section">
                    <h3>Payment Method</h3>
                    <div class="payment-methods">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="credit_card" required>
                            <span>Credit Card</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="debit_card">
                            <span>Debit Card</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="online_banking">
                            <span>Online Banking</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="cash_on_delivery">
                            <span>Cash on Delivery</span>
                        </label>
                    </div>
                    
                </div>
                
                <button type="submit" class="btn-checkout" id="submit-btn">
                    Place Order
                </button>
            </form>
        </div>
    </div>
</div>

<?php include '../../_footer.php'; ?>