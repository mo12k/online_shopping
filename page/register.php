<?php

$_body_class = 'register-page';
$_page_title = "Register";

require '../_base.php';
$info = temp('info');

include '../_head.php';
include '../_header.php';

if (is_post()) {

    $username = req('username');
    $email = req('email');
    $password = req('password');
    $confirm_password = req('confirm_password');

    // Validate username
    if (!$username) {
        $_err['username'] = "Required";
    } else if (strlen($username) > 100) {
        $_err['username'] = "Maximum length 100";
    } else if (!is_unique($username, 'customer', 'username')) {
        $_err['username'] = "Duplicate Username";
    }

    // Validate email
    if (!$email) {
        $_err['email'] = "Required";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_err['email'] = "Invalid Email Format";
    } else if (!is_unique($email, 'customer', 'email')) {
        $_err['email'] = "Duplicate Email";
    }

    //Validate password
    if(!$password){
        $_err['password'] = "Required";
    }
    else if(strlen($password) < 8){
        $_err['password'] = "Minimum length 8 characters";
    }
    else if(strlen($password) > 11){
        $_err['password'] = "Maximum length 11";
    }
    elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9])/', $password)) {
        $_err['password'] = "Must contain uppercase, lowercase, number and special character";
    }

    // Validate confirm password
    if (!$confirm_password) {
        $_err['confirm_password'] = "Required";
    } else if ($confirm_password !== $password) {
        $_err['confirm_password'] = "Passwords do not match";
    }

    if (!$_err) {
    
        // GENERATE 6-DIGIT OTP 
        $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);  
        $otp_hash = sha1($otp);

        $_SESSION['pending_registration'] = [
            'username' => $username,
            'email'    => $email,
            'password' => $password,
            'otp_hash' => $otp_hash,
            'expires_at' => time() + (15 * 60) // 15 minutes from now
        ];

        // Send email
        $m = get_mail();
        $m->setFrom('noreply@papernest.com', 'PaperNest');
        $m->addAddress($email);
        $m->isHTML(true);
        $m->Subject = 'Your Verification Code - PaperNest';
        $m->Body = "<h2>Verify Your Email</h2>
                    <p>Hi {$username},</p>
                    <p>Your verification code is:</p>
                    <h1 style='font-size:48px; letter-spacing:10px; text-align:center; color:#1976d2;'>$otp</h1>
                    <p style='text-align:center; color:#555;'>
                        Valid for 15 minutes only
                    </p>";

        try {
            $m->send();
            temp('info', 'Check your email! OTP sent.');
            redirect('verify_otp.php');
        } catch (Exception $e) {
            temp('info', "Email not sent Otp: $otp");
            redirect('verify_otp.php');
        }
    }
}
?>
<?php if ($info): ?>
<div class="alert-success-fixed">
    <div class="alert-content">
        <?= encode($info) ?>
        <span class="alert-close">×</span>
    </div>
</div>
<?php endif; ?>

<div class="container-register">
    <form id="register-form" method="POST" action="register.php">        
        <h2>Account Details</h2>
        <label for="username">Username *</label>
        <?= html_text('username', 'maxlength="100"') ?>
        <?= err('username') ?>

        <label for="email">Email Address *</label>
        <?= html_text('email', 'placeholder="example@example.com"') ?>
        <?= err('email') ?>

        <label for="password">Password *</label>
        <?=  html_password('password', 'maxlength="11"') ?>
        <?= err('password') ?>
        <div class="password-rules">
            <p class="rule" id="rule-length">Minimum 8 characters</p>
            <p class="rule" id="rule-upper">At least one uppercase letter</p>
            <p class="rule" id="rule-lower">At least one lowercase letter</p>
            <p class="rule" id="rule-number">At least one number</p>
            <p class="rule" id="rule-special">At least one special character</p>
        </div>

        <label for="confirm_password">Confirm Password *</label>
        <?=  html_password('confirm_password') ?>
        <?= err('confirm_password') ?>

        <button type="submit" id="create_account" name="create_account">Create Account</button>
        

        <div class="already-account">
            Already have an account?
            <a href="login.php" class="login-link">Log in here</a>
        </div>
    </form>
</div>