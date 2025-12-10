<?php
$_body_class = 'forgot-password-page';
$_page_title = "Forgot Password";

require '../_base.php';

$info = temp('info');

include '../_head.php';

if (is_post()) {
    $email = trim(req('email'));

    // Validate
    if (!$email) {
        $_err['email'] = "Required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_err['email'] = "Invalid email format";
    } 
    elseif (!is_exists($email, 'customer', 'email')) {
        $_err['email'] = "Email not found";
    }

    // If validation passed
    if (empty($_err)) {
        $stm = $_db->prepare("SELECT * FROM customer WHERE email = ?");
        $stm->execute([$email]);
        $user = $stm->fetch();

        // Check if there's already a VALID (non-expired) token
        $stm = $_db->prepare("
                        SELECT 1 FROM token 
                        WHERE customer_id = ? 
                        AND token_type = 'reset' 
                        AND expires_at > NOW()
                    ");
        $stm->execute([$user->customer_id]);
        $existing_token = $stm->fetch();

        if ($existing_token) {
            // Token still valid don't send new email!
            temp('info', 'A reset link has already been sent to your email. Please check your inbox (valid for 5 minutes).');
            redirect('forgot-password.php');
        } else {
            // No valid token generate new one
            $raw_token  = bin2hex(random_bytes(32));
            $token_hash = sha1($raw_token);

            // Remove any old/expired tokens
            $_db->prepare("DELETE FROM token WHERE customer_id = ? AND token_type = 'reset'")
                        ->execute([$user->customer_id]);
            // Insert new 5-minute token
            $_db->prepare("
                INSERT INTO token (customer_id, token_hash, token_type, type, expires_at)
                VALUES (?,?,'reset','link', DATE_ADD(NOW(), INTERVAL 5 MINUTE))
            ")->execute([$user->customer_id, $token_hash]);

            $reset_url = base('page/token.php') . '?token=' . $raw_token . '&email=' . urlencode($email);

            // Send email
            $m = get_mail();
            $m->setFrom('noreply@papernest.com', 'PaperNest');
            $m->addAddress($email);
            $m->isHTML(true);
            $m->Subject = 'Password Reset - PaperNest';
            $m->Body = "<h2>Hello {$user->username}</h2>
                        <p>Click the button below to reset your password (expires in 5 minutes):</p>
                        <p style='text-align:center'>
                            <a href='$reset_url' 
                            target='_self'
                            style='display:inline-block; padding:16px 36px; background:#007bff; color:white; text-decoration:none; border-radius:8px; font-size:16px; font-weight:600;'>
                                Reset Password
                            </a>
                        </p>";

            try {
                $m->send();
                temp('info', 'Check your email! Reset link sent.');
                redirect('forgot-password.php');
            } catch (Exception $e) {
                temp('info', "Email not sent (localhost). <a href='$reset_url'>Click here to reset password</a>");
                redirect('forgot-password.php');
            }
        }
    }
}
?>

<div class="container-forgot-password">

    <?php if ($info): ?>
    <div class="alert-success-fixed">
        <div class="alert-content">
            <strong>Success!</strong> <?= encode($info) ?>
            <span class="alert-close">×</span>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="wrapper-forgot-password">
        <h1>Forgot Password</h1>
        <form method="post">
            <div class="field input">
                <label for="email">Email Address</label>
                <?= html_text('email') ?>
                <?= err('email') ?>
            </div>

            <div class="field button">
                <button type="submit">Send Reset Link</button>
            </div>

            <div class="login-link">
                <a href="login.php" class="link">Back to Login</a>
            </div>
        </form>
    </div>
</div>