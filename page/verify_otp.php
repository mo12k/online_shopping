<?php

$_body_class = 'verify-otp-page';
$_page_title = "Verify OTP";

require '../_base.php';
include '../_head.php';

// Must come from registration
$_customer_id = $_SESSION['customer_id'] ?? null;
if(!isset($_POST['create_account'])) {
    if(is_post()){
        $otp = trim(req('otp'));

        //validate otp
        if (strlen($otp) !== 6 || !ctype_digit($otp)) {
            $err = "Please enter a valid 6-digit code";
        }else{
            $otp_hash = sha1($otp);

            $stm = $_db->prepare("
                SELECT * FROM token 
                WHERE customer_id = ? 
                  AND token_hash = ? 
                  AND token_type = 'verify' 
                  AND expires_at > NOW()
            ");
            $stm->execute([$_customer_id, $otp_hash]);
            $token = $stm->fetch();

            if ($token) {
                // OTP correct → NOW update the account
                $stm = $_db->prepare("
                    UPDATE customer 
                    SET email_verified = 1 
                    WHERE customer_id = ?
                ");
                $stm->execute([$_customer_id]);

                // Delete used OTP
                $stm = $_db->prepare("
                    DELETE FROM token 
                    WHERE token_id = ?
                ");
                $stm->execute([$token->token_id]);

            } else {
                $err = "Invalid or expired OTP";
            }
        }
    }
}

?>

<div class="verify-otp-container">
    <h2>Email Verification</h2>
    <p>Please enter the 6-digit code sent to your email</p>

    <form method="POST" style="text-align:center;">
        <?= html_text('otp', 'maxlength="6" placeholder="Enter OTP"') ?>
        <?= err('otp') ?>
        <br><br>
        <button type="submit" style="padding:12px 40px; background:#1976d2; color:white; border:none; border-radius:6px; font-size:16px; cursor:pointer;">Verify</button>
    </form>
