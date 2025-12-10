<?php
$_body_class = 'login-page';
$_page_title = "Login";

require '../_base.php';
include '../_head.php';

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
    }elseif(strlen($password) > 11){
        $_err['password'] = "Maximum length 11  characters";
    }

    //Check credentials
    if(empty($_err)){
        // Fetch user to check failed attempts and last failed login time
        $stm = $_db->prepare("SELECT * FROM customer WHERE username = ?");
        $stm->execute([$username]);
        $user = $stm->fetch();

        if($user){
            $current_Time = new DateTime();
            $lastFailedLogin = $user->last_failed_at ? new DateTime($user->last_failed_at) : null;
            $failed_atttempt = $user->failed_attempt ?? 0;

            // Check if account is locked
            if($failed_atttempt >= 3 && $lastFailedLogin){
                $diff = $current_Time->getTimestamp() - $lastFailedLogin->getTimestamp();
                if($diff < 60){
                    $_err['general'] = "Account locked due to multiple failed login attempts. Please try again after 1 minute.";
                }else{
                    //Reset failed attempts after lockout period
                    $_db->prepare("UPDATE customer SET failed_attempt = 0, last_failed_at = NULL WHERE customer_id = ?")
                        ->execute([$user->customer_id]);
                    $failed_atttempt = 0;
                }
            }

            // Only proceed if account is not locked
            if(empty($_err['general'])){
                //Verify credentials
                $valid_user = verify_credentials($username, $password);

                if($valid_user){
                    //Reset failed attempts on successful login
                    $_db->prepare("UPDATE customer SET failed_attempt = 0, last_failed_at = NULL WHERE customer_id = ?")
                        ->execute([$user->customer_id]);

                    //Login success
                    $_SESSION['customer_id'] = $user->customer_id;
                    $_SESSION['customer_username'] = $user->username;
                    $_SESSION['profile_picture'] = $user->photo ?? 'default_pic.jpg';
                    
                    //Remember me
                    if (!empty($_POST['remember_me'])) {   
                        $remember_token = bin2hex(random_bytes(32));
                        $remember_token_hash = sha1($remember_token);
                        $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));

                        //Delete existing tokens
                        $_db->prepare("DELETE FROM token WHERE customer_id = ? AND token_type = 'remember'")
                            ->execute([$user->customer_id]);

                        //Insert new token
                        $_db->prepare("INSERT INTO token (customer_id, token_type, token_hash, expires_at) 
                                        VALUES (?, 'remember', ?, ?)")
                            ->execute([$user->customer_id, $remember_token_hash, $expires_at]);

                        //Set cookie
                        setcookie('remember_me', $remember_token, time() + (30 * 24 * 60 * 60), "/");
                        setcookie('remember_me_user', $user->customer_id, time() + (30 * 24 * 60 * 60), "/");
                    }
                    redirect('../index.php');
                    exit;
                } else {
                    //Login failed - increment failed attempts
                    $_db->prepare("UPDATE customer SET failed_attempt = failed_attempt + 1, last_failed_at = NOW() WHERE customer_id = ?")
                        ->execute([$user->customer_id]);
                    $_err['general'] = "Invalid username or password";
                }
            }
        } else {
            //User not found
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

            <button type="submit" name="submit" value="login"> Login </button>

            <div class="register-link">
                <p> Don't have an account? <a href="register.php" class="link"> Register Here </a> </p>
            </div>

        </form>
    </div>
</div>
<?php
// require '../_foot.php';