<?php
include '../_base.php';
$_body_class = 'token-page';

// ------------------------------------------------------------------
// 1. Get token and email from URL
$raw_token = trim(req('token') ?? '');
$email     = trim(req('email') ?? '');

$info = temp('info');

if ($raw_token === '' || $email === '') {
    temp('info', 'Invalid reset link');
    redirect('/login.php');
}

// 2. Hash the token that came from the email link
$token_hash = sha1($raw_token);

$stm = $_db->prepare("
    SELECT c.*
    FROM customer c
    JOIN token t ON c.customer_id = t.customer_id
    WHERE t.token_hash = ?
      AND c.email = ?
      AND t.token_type = 'reset'
      AND t.expires_at > NOW()
");
$stm->execute([$token_hash, $email]);
$user = $stm->fetch();

if (!$user) {
    temp('info', 'Invalid or expired reset link. Please request a new one.');
    redirect('login.php');
}

// ------------------------------------------------------------------
// If we reach here → token is valid and not expired

if (is_post()) {
    $password = req('password');
    $confirm  = req('confirm');

    // ---- Validation ----
    if (!$password) {
        $_err['password'] = 'Required';
    } elseif (strlen($password) < 8 || strlen($password) > 11) {
        $_err['password'] = 'Between 8–11 characters';
    }elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9])/', $password)) {
        $_err['password'] = "Must contain uppercase, lowercase, number and special character";
    }

    if (!$confirm) {
        $_err['confirm'] = 'Required';
    } elseif ($confirm !== $password) {
        $_err['confirm'] = 'Passwords do not match';
    }

    // ---- If no errors → update password ----
    if (empty($_err)) {
        $new_hash = sha1($password);

        // Update password
        $_db->prepare("UPDATE customer SET password = ? WHERE customer_id = ?")
            ->execute([$new_hash, $user->customer_id]);

        // Delete the used token (important!)
        $_db->prepare("DELETE FROM token WHERE customer_id = ?")
            ->execute([$user->customer_id]);

        temp('info', 'Your password has been changed successfully!');
        redirect('login.php');
    }
}

// ------------------------------------------------------------------
$_body_class = 'token-page';
$_page_title = 'Reset Password';
require '../_head.php';   
?>
<style>
/* ===== Reset Password Page (Embedded Only) ===== */
body.token-page {
    margin: 0;
    padding: 0;
    font-family: "Poppins", sans-serif;
    background: linear-gradient(135deg, #ede7e3, #d7ccc8);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.container-token {
    width: 100%;
    max-width: 420px;
    padding: 20px;
}

.wrapper-token {
    background: #fff;
    padding: 32px 36px;
    border-radius: 22px;
    box-shadow: 0 14px 45px rgba(0,0,0,.15);
}

.wrapper-token h1 {
    text-align: center;
    margin-bottom: 26px;
    font-size: 28px;
    font-weight: 600;
    color: #333;
}

/* Form layout */
.wrapper-token form {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.wrapper-token label {
    font-weight: 500;
    margin-bottom: 6px;
    display: block;
}

/* Inputs */
.wrapper-token input {
    width: 100%;
    padding: 12px 14px;
    border-radius: 14px;
    border: 1px solid #ccc;
    font-size: 14px;
    box-sizing: border-box;
}

.wrapper-token .field.input {
    width: 100%;
    box-sizing: border-box;
}

/* Password rules */
.password-rules {
    margin-top: 10px;
    width: 100%;
    padding-right: 8px;
    box-sizing: border-box;
}

.password-rules .rule {
    font-size: 13px;
    color: red;
    margin: 4px 0;
    word-wrap: break-word;
}

.password-rules .rule.valid {
    color: #2e7d32;
    font-weight: 600;
}

/* Button */
.wrapper-token button {
    margin-top: 10px;
    width: 100%;
    padding: 13px;
    border-radius: 14px;
    border: none;
    background: #5b7cfa;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
}

.wrapper-token button:hover {
    background: #4868e8;
}

.wrapper-token .alert-message {
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

<div class="container-token">
    <div class="wrapper-token">
        <?php if ($info): ?>
        <div class="alert-message">
            <?= $info ?>
        </div>
        <?php endif; ?>
        
        <h1>Reset Your Password</h1>
    
        <form method="post" class="form">
            <div class="field input">
                <label>New Password</label>
                <?=  html_password('password', 'maxlength="11"') ?>                
                <?= err('password') ?>
                <div class="password-rules">
                    <p class="rule" id="rule-length">Minimum 8 characters</p>
                    <p class="rule" id="rule-upper">At least one uppercase letter</p>
                    <p class="rule" id="rule-lower">At least one lowercase letter</p>
                    <p class="rule" id="rule-number">At least one number</p>
                    <p class="rule" id="rule-special">At least one special character</p>
                </div>

                <label>Confirm New Password</label>
                <?=  html_password('confirm', 'maxlength="11"') ?>
                <?= err('confirm') ?>
            </div>
            <div class="field button">
                <button type="submit">Update Password</button>
            </div>
        </div>
    </form>
</div>