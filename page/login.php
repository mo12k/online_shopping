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
        $user_stmt = $_db->prepare("SELECT * FROM customer WHERE username = ? LIMIT 1");
        $user_stmt->execute([$username]);
        $user = $user_stmt->fetch(PDO::FETCH_OBJ);

        if($user){
            $current_Time = new DateTime();
            $lastFailedLogin = $user->last_failed_login ? new DateTime($user->last_failed_login) : null;


            //Check account blocked
            if($user->failed_attempt >= 3 && $lastFailedLogin){
                $diff = $current_Time->getTimestamp() - $lastFailedLogin->getTimestamp();
                if($diff <  60){//Block duration 1 minute
                    $_err['general'] = "Account is temporarily blocked due to multiple failed login attempts. Please try again in " . (60 - $diff) . " seconds remaining)";
                }else{
                    //Reset failed attempts after block duration
                    $_db->prepare("UPDATE customer SET failed_attempt = 0, last_failed_login = NULL WHERE customer_id = ?")
                        ->execute([$user->customer_id]);
                    $user->failed_attempt = 0;
                }
            }

            if(empty($_err)){
                // Verify credentials
                $valid_user = verify_credentials($username, $password);
                if($valid_user){
                    //Reset failed attempts on successful login
                    $_db->prepare("UPDATE customer SET failed_attempt = 0, last_failed_login = NULL WHERE customer_id = ?")
                        ->execute([$user->customer_id]);

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
                } else {
                    //Increment failed attempts
                    $failed_attempt = $user->failed_attempt + 1;
                    $_db->prepare("UPDATE customer SET failed_attempt = ?, last_failed_login = ? WHERE customer_id = ?")
                        ->execute([$failed_attempt, $current_Time->format('Y-m-d H:i:s'), $user->customer_id]);

                    if($failed_attempt >= 3){
                        $_err['general'] = "Account is temporarily blocked due to multiple failed login attempts. Please try again in 60 seconds.";
                    } else {
                        $_err['general'] = "Invalid username or password";
                    }
                }
            }

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
// include '../_foot.php';