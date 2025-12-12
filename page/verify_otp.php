<?php

$_body_class = 'verify-otp-page';
$_page_title = "Verify OTP";

require '../_base.php';

$info = temp('info');

include '../_head.php';

// Must come from registration
$pending = $_SESSION['pending_registration'] ?? null;

if (!$pending) {
    temp('error', 'No pending registration found. Please register first.');
    redirect('register.php');
    exit;
}

if (time() > ($pending['expires_at'] ?? 0)) {
    unset($_SESSION['pending_registration']);
    temp('error', 'OTP has expired. Please register again.');
    redirect('register.php');
    exit;
}

if (is_post()) {
    $otp_input = trim(req('otp'));

    if (strlen($otp_input) !== 6 || !ctype_digit($otp_input)) {
        $_err['otp'] = 'Please enter a valid 6-digit code';
    }
    // Compare hashed OTP
    elseif (sha1($otp_input) !== $pending['otp_hash']) {
        $_err['otp'] = 'Invalid or expired verification code';
    }
    else {
        // SUCCESS: OTP IS CORRECT → NOW CREATE ACCOUNT
        try {
            $_db->beginTransaction();

            $password_hash = sha1($pending['password']); // Hash password before storing

            $stm = $_db->prepare("
                INSERT INTO customer 
                (username, email, password, is_verified, created_at, photo)
                VALUES (?, ?, ?, 1, NOW(), 'default_pic.jpg')
            ");
            $stm->execute([
                $pending['username'],
                $pending['email'],
                $password_hash
            ]);

            $customer_id = $_db->lastInsertId();

            // Auto login
            $_SESSION['customer_id']       = $customer_id;
            $_SESSION['customer_username'] = $pending['username'];

            // Clear pending data
            unset($_SESSION['pending_registration']);

            $_db->commit();

            temp('info', 'Account created and verified successfully!');
            redirect('../index.php');
            exit;

        } catch (Exception $e) {
            $_db->rollBack();
            error_log("Account creation failed: " . $e->getMessage());
            temp('error', 'Something went wrong. Please try again.');
        }
    }
}

?>
<?php if ($info): ?>
<div class="alert-success-fixed">
    <div class="alert-content">
        <strong>Success!</strong> <?= encode($info) ?>
        <span class="alert-close">×</span>
    </div>
</div>
<?php endif; ?>

<div class="verify-otp-container">
    <h2>Email Verification</h2>
    <p>Please enter the 6-digit code sent to your email</p>

    <form method="POST" style="text-align:center;">
        <?= html_text('otp', 'maxlength="6" placeholder="Enter OTP"') ?>
        <?= err('otp') ?>
        <br><br>
        <button type="submit" style="padding:12px 40px; background:#1976d2; color:white; border:none; border-radius:6px; font-size:16px; cursor:pointer;">Verify</button>
    </form>
