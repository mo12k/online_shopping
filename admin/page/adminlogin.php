<?php
$_title = "Login";
$_body_class = 'login-page';

admin_require_login();
require '../_base.php';
include '../_head.php';

if(is_post()){
    $username = req('username');
    $password = req('password');

    //Validate username
    if(!$username){
        $_err['username'] = "Required";
    }else if(!is_exists($username, 'admin', 'username')){
        $_err['username'] = "Username not found";
    }

    //Validate password
    if(!$password){
        $_err['password'] = "Required";
    }

    //Check credentials
    if(empty($_err)){
        $user = verify_credentials($username, $password);
        if($user){
            //Login success
            $_SESSION['admin_id'] = $user->admin_id;
            $_SESSION['admin_username'] = $user->username;
            $_SESSION['profile_picture'] = $user->photo ?? 'default_pic.jpg';
            redirect('../index.php');

        } else {
            //Login failed
            $_err['general'] = "Invalid username or password";
        }
    }
}
?>
<div class="container-login">
    <div class="wrapper-login">
        <h1> Login </h1>
        <form method="post" action="">
            <div class="field input">
                <label for="username">Username </label>
                <?= html_text('username') ?>
                <i class='bxr bx-user'></i> 
                <?= err('username') ?>
            </div>

            <div class="field input">
                <label for="password">Password </label>
                <?= html_password('password' ,'maxlength="11"') ?>
                <i class='bxr  bx-lock'></i> 
                <?= err('password') ?>
            </div>

            <?php if(!empty($_err['general'])): ?>
                <div class="error-message" style="color:red; margin-bottom:10px;">
                    <?= $_err['general'] ?>
                </div>
            <?php endif; ?>


            <div class="remember-forgot">
                <label class="remember-me">
                    <input type="checkbox" name="remember_me" value="1">
                    Remember me
                </label>

                <a href="forgot-password.php" class="link">Forgot Password?</a>
            </div>

            <button type="submit" name="submit" value='1'> Login </button>
        </form>
    </div>
</div>
<?php

