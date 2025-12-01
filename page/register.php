<?php

$_body_class = 'register-page';
$_page_title = "Register";

require_once '../_base.php';
include_once '../_head.php';

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

    // Validate password
    if (!$password) {
        $_err['password'] = "Required";
    } else if (strlen($password) < 8) {
        $_err['password'] = "Minimum length 8 characters";
    } else if (strlen($password) > 11) {
        $_err['password'] = "Maximum length 11";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9])/', $password)) {
        $_err['password'] = "Must contain uppercase, lowercase, number and special character";
    }

    // Validate confirm password
    if (!$confirm_password) {
        $_err['confirm_password'] = "Required";
    } else if ($confirm_password !== $password) {
        $_err['confirm_password'] = "Passwords do not match";
    }

    if (!$_err) {
        // Optional fields not collected now
        $phone = null;
        $birthdate = null;
        $gender = null;

        $_db->beginTransaction();

        $sql = "INSERT INTO customer 
                (username, email, password, phone, birthdate, gender, created_at, photo) 
                VALUES 
                (?, ?, SHA1(?), ?, ?, ?, NOW(), 'default_pic.jpg')";

        $stm = $_db->prepare($sql);
        $stm->execute([$username, $email, $password, $phone, $birthdate, $gender]);

        $customer_id = $_db->lastInsertId();
        $_db->commit();

        $_SESSION['customer_id'] = $customer_id;
        $_SESSION['customer_username'] = $username;

        redirect('/');
    }
}
?>

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
        <input type="password" name="password" id="password" maxlength="11">
        <?= err('password') ?>
        <div class="password-rules">
            <p class="rule" id="rule-length">Minimum 8 characters</p>
            <p class="rule" id="rule-upper">At least one uppercase letter</p>
            <p class="rule" id="rule-lower">At least one lowercase letter</p>
            <p class="rule" id="rule-number">At least one number</p>
            <p class="rule" id="rule-special">At least one special character</p>
        </div>

        <label for="confirm_password">Confirm Password *</label>
        <input type="password" name="confirm_password" id="confirm_password">
        <?= err('confirm_password') ?>

        <button type="submit">Create Account</button>

        <div class="already-account">
            Already have an account?
            <a href="login.php" class="login-link">Log in here</a>
        </div>
    </form>
</div>