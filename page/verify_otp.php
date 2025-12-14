<?php

$_body_class = 'verify-otp-page';
$_page_title = "Verify OTP";

require '../_base.php';

$info = temp('info');

include '../_head.php';
?>

<style>
    body.verify-otp-page {
        background-image: url("../images/register_background.jpg");
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-position: center;
        background-size: cover;
    }
    .verify-otp-container {
        max-width: 450px;
        margin: 80px auto;
        padding: 40px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .verify-otp-container h2 {
        color: #1976d2;
        margin-bottom: 16px;
        font-size: 28px;
    }

    .verify-otp-container p {
        color: #666;
        margin-bottom: 30px;
        font-size: 15px;
    }

    .verify-otp-container input[type="text"] {
        width: 100%;
        padding: 16px;
        font-size: 24px;
        letter-spacing: 8px;
        text-align: center;
        border: 2px solid #ddd;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: border-color 0.3s;
    }

    .verify-otp-container input[type="text"]:focus {
        outline: none;
        border-color: #1976d2;
    }

    .verify-otp-container button {
        width: 100%;
        padding: 14px 40px;
        background: linear-gradient(135deg, #1976d2, #1565c0);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        margin-top: 20px;
    }

    .verify-otp-container button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(25, 118, 210, 0.4);
    }

    .verify-otp-container button:active {
        transform: translateY(0);
    }

    .error {
        color: #d32f2f;
        font-size: 14px;
        margin-top: 8px;
    }

    .verify-otp-container .alert-message {
        background: #d4edda;
        color: #155724;
        padding: 12px 16px;
        border-radius: 8px;
        border: 1px solid #c3e6cb;
        margin-bottom: 20px;
        font-size: 14px;
        text-align: center;
    }
</style>

<?php

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

<div class="verify-otp-container">
    <?php if ($info): ?>
    <div class="alert-message">
        <?= $info ?>
    </div>
    <?php endif; ?>
    
    <h2>Email Verification</h2>
    <p>Please enter the 6-digit code sent to your email</p>

    <form method="POST" style="text-align:center;">
        <?= html_text('otp', 'maxlength="6" placeholder="Enter OTP"') ?>
        <?= err('otp') ?>
        <br><br>
        <button type="submit" style="padding:12px 40px; background:#1976d2; color:white; border:none; border-radius:6px; font-size:16px; cursor:pointer;">Verify</button>
    </form>
