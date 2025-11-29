<?php
$_body_class = 'forgot-password-page';
$_page_title = "Forgot Password";

require '../_base.php';
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
              AND reset_token_expire_at > NOW()
        ");
        $stm->execute([$user->customer_id]);
        $existing_token = $stm->fetch();

        if ($existing_token) {
            // Token still valid → don't send new email!
            temp('info', 'A reset link has already been sent to your email. Please check your inbox (valid for 5 minutes).');
            redirect('forgot-password.php');
        } else {
            // No valid token → safe to generate new one
            $raw_token  = bin2hex(random_bytes(32));
            $token_hash = hash('sha256', $raw_token);

            // Remove any old/expired tokens
            $_db->prepare("DELETE FROM token WHERE customer_id = ?")
                ->execute([$user->customer_id]);

            // Insert new 5-minute token
            $_db->prepare("
                INSERT INTO token (reset_token_hash, reset_token_expire_at, customer_id)
                VALUES (?, DATE_ADD(NOW(), INTERVAL 5 MINUTE), ?)
            ")->execute([$token_hash, $user->customer_id]);

            $reset_url = base('page/token.php') . '?token=' . $raw_token . '&email=' . urlencode($email);

            // Send email
            $m = get_mail();
            $m->setFrom('mokcb-wm24@student.tarc.edu.my', 'PaperNest');
            $m->addAddress($email);
            $m->isHTML(true);
            $m->Subject = 'Password Reset - PaperNest';
            $m->Body = "<h2>Hello {$user->username}</h2>
                        <p>Click the button below to reset your password (expires in 5 minutes):</p>
                        <p style='text-align:center'>
                            <a href='$reset_url' style='padding:15px 30px;background:#007bff;color:white;text-decoration:none;border-radius:5px;'>
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