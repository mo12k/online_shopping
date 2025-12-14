<?php
require '../_base.php';

$current = 'payment';
$_title  = 'Payment Checkout';

// Check if user has pending order in session
if (!isset($_SESSION['pending_order'])) {
    redirect('checkout.php');
    exit;
}

$pending_order = $_SESSION['pending_order'];
$payment_method = $pending_order['payment_method'];
$retry = $_SESSION['payment_retry'] ?? 0;
$MAX_RETRY = 3;

/* =====================
   PROCESS PAYMENT
===================== */
if ($payment_method === 'credit_card' || $payment_method === 'debit_card') {

    $card_number = req('card_number');
    $card_holder = req('card_holder');
    $expiry      = req('expiry');
    $cvv         = req('cvv');

    if (
        preg_match('/^[0-9]{16}$/', $card_number) &&
        preg_match('/^[A-Z ]+$/', strtoupper($card_holder)) &&
        preg_match('/^[0-9]{2}\/[0-9]{2}$/', $expiry) &&
        preg_match('/^[0-9]{3}$/', $cvv)
    ) {
        success();
    } else {
        fail('Invalid card details.');
    }

}
elseif ($payment_method === 'online_banking') {

    $bank     = req('bank');
    $username = req('bank_username');
    $password = req('bank_password');
    $approve  = req('approve');

    $valid_banks = ['Maybank', 'CIMB', 'Public Bank'];

    if (!in_array($bank, $valid_banks)) {
        fail('Invalid bank selected.');
    }

    if (!$username || !$password) {
        fail('Invalid bank login.');
    }

    if ($approve === 'A') {
        success();
    } else {
        fail('Payment rejected.');
    }

}
elseif ($payment_method === 'e_wallet') {

    $wallet  = req('wallet');
    $confirm = req('confirm');

    $valid_wallets = ['Touch n Go', 'Boost', 'GrabPay'];

    if (!in_array($wallet, $valid_wallets)) {
        fail('Invalid e-wallet.');
    }

    if ($confirm === 'Y') {
        success();
    } else {
        fail('E-wallet payment not completed.');
    }

}
elseif ($payment_method === 'cash_on_delivery') {
    // Cash on delivery - process immediately on page load
    if (!is_post()) {
        success();
    }
}
else {
    fail('Invalid payment method.');
}

include '../../_head.php';
include '../../_header.php';
?>

<!-- =====================
     PAYMENT FORM UI
===================== -->
<div style="max-width:600px;margin:40px auto;background:#fff;padding:30px;border-radius:10px">
    <h2>Complete Payment</h2>

    <form method="post">

        <input type="hidden" name="payment_method" value="<?= encode($payment_method) ?>">

        <?php if ($payment_method === 'credit_card' || $payment_method === 'debit_card'): ?>

            <label>Card Number</label>
            <input type="text" name="card_number" maxlength="16" required>

            <label>Card Holder</label>
            <input type="text" name="card_holder" required>

            <label>Expiry (MM/YY)</label>
            <input type="text" name="expiry" placeholder="12/27" required>

            <label>CVV</label>
            <input type="password" name="cvv" maxlength="3" required>

        <?php elseif ($payment_method === 'online_banking'): ?>

            <label>Bank</label>
            <select name="bank" required>
                <option value="">-- Select Bank --</option>
                <option>Maybank</option>
                <option>CIMB</option>
                <option>Public Bank</option>
            </select>

            <label>Username</label>
            <input type="text" name="bank_username" required>

            <label>Password</label>
            <input type="password" name="bank_password" required>

            <label>Approve Payment?</label>
            <select name="approve" required>
                <option value="A">Approve</option>
                <option value="R">Reject</option>
            </select>

        <?php elseif ($payment_method === 'e_wallet'): ?>

            <label>E-Wallet</label>
            <select name="wallet" required>
                <option value="">-- Select Wallet --</option>
                <option>Touch n Go</option>
                <option>Boost</option>
                <option>GrabPay</option>
            </select>

            <label>Confirm Payment?</label>
            <select name="confirm" required>
                <option value="Y">Yes</option>
                <option value="N">No</option>
            </select>

        <?php elseif ($payment_method === 'cash_on_delivery'): ?>

            <p>Please prepare the exact amount upon delivery.</p>
            
        <?php endif; ?>

        <br><br>
        <button type="submit" style="padding:12px 20px;background:#6d4c41;color:#fff;border:none;border-radius:6px">
            Pay Now
        </button>

    </form>
</div>

<?php include '../../_footer.php'; ?>
