<?php
$_body_class = 'login-page';
$_page_title = "Login";

require '../_base.php';
require '../_head.php';

if(is_post()) {
    $username = trim(req('username'));
    $password = req('password');
    
    //Validate username
    if(!$username){
        $_err['username'] = "Required";
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
            $_SESSION['customer_id'] = $user->customer_id;
            $_SESSION['customer_name'] = $user->username;
            redirect('/');

        } else {
            //Login failed
            $_err['general'] = "Invalid username or password";
        }
    }
}
?>

<div class="container-login">
    <div class="wrapper">
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
                <input type="password" name="password" id="password" />
                <i class='bxr  bx-lock'></i> 
                <?= err('password') ?>
            </div>

            <div class="remember-forgot">
                <a href="forgot_password.php" class="link"> Forgot Password? </a>
            </div>

            <button type="submit" name="submit" value="login"> Login </button>

            <div class="register-link">
                <p> Don't have an account? <a href="register.php" class="link"> Register Here </a> </p>
            </div>

        </form>
    </div>
</div>
<?php
// require '../_foot.php';