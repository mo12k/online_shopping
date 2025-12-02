<?php
include '../_base.php';


// ------------------------------------------------------------------
// 1. Get token and email from URL
$raw_token = trim(req('token') ?? '');
$email     = trim(req('email') ?? '');

temp('info', null);  // clears old messages

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
        $new_hash = password_hash($password, PASSWORD_DEFAULT);

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

<div class="container-token">
    <div class="wrapper-token">
        <h1>Reset Your Password</h1>
    
        <form method="post" class="form">
            <div class="field input">
                <label>New Password</label>
                <input type="password" id="password" name="password" required>
                <?= err('password') ?>
                <div class="password-rules">
                    <p class="rule" id="rule-length">Minimum 8 characters</p>
                    <p class="rule" id="rule-upper">At least one uppercase letter</p>
                    <p class="rule" id="rule-lower">At least one lowercase letter</p>
                    <p class="rule" id="rule-number">At least one number</p>
                    <p class="rule" id="rule-special">At least one special character</p>
                </div>

                <label>Confirm New Password</label>
                <input type="password" name="confirm" required>
                <?= err('confirm') ?>
            </div>
            <div class="field button">
                <button type="submit">Update Password</button>
            </div>
        </div>
    </form>
</div>