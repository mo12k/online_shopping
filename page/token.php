<?php
$_body_class = 'verify-otp-page';
$_page_title = "Verify Email";

require '../_base.php';
include '../_head.php';



if (is_post()) {
    $otp = trim(req('otp'));

    if (strlen($otp) !== 6 || !ctype_digit($otp)) {
        $err = "Please enter a valid 6-digit code";
    } else {
        $otp_hash = sha1($otp);
        $token_id = $_SESSION['pending_otp_token_id'] ?? 0;

        $stm = $_db->prepare("
            SELECT * FROM token 
            WHERE token_id = ? 
              AND token_hash = ? 
              AND token_type = 'verify' 
              AND expires_at > NOW()
        ");
        $stm->execute([$token_id, $otp_hash]);
        $token = $stm->fetch();

        if ($token) {
            // OTP correct → NOW create the account
            $data = $_SESSION['pending_register'];

            $_db->beginTransaction();

            $stm = $_db->prepare("
                INSERT INTO customer 
                (username, email, password, email_verified, created_at, photo)
                VALUES (?, ?, SHA1(?), 1, NOW(), 'default_pic.jpg')
            ");
            $stm->execute([$data['username'], $data['email'], $data['password']]);

            $customer_id = $_db->lastInsertId();

            // Delete used OTP
            $_db->prepare("DELETE FROM token WHERE token_id = ?")->execute([$token_id]);

            $_db->commit();

            // Login user
            $_SESSION['customer_id'] = $customer_id;
            $_SESSION['customer_username'] = $data['username'];
            unset($_SESSION['pending_register'], $_SESSION['pending_otp_token_id']);

            temp('info', 'Account created successfully! Welcome!');
            redirect('/'); // or profile.php
        } else {
            $error = "Invalid or expired OTP";
        }
    }
}
?>

<div class="box">
    <h2>Email Verification</h2>
    <p>We sent a 6-digit code to <strong><?= htmlspecialchars($_SESSION['pending_register']['email']) ?></strong></p>

    <?php if ($error): ?>
        <div style="color:red; margin:15px 0; text-align:center;"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" style="text-align:center;">
        <!-- 6 separate boxes version (beautiful) -->
        <div style="display:flex; justify-content:center; gap:10px; margin:20px 0;">
            <?php for ($i = 0; $i < 6; $i++): ?>
                <input type="text" name="otp[]" maxlength="1" required 
                       style="width:50px; height:60px; font-size:28px; text-align:center; border:2px solid #ddd; border-radius:8px;"
                       oninput="this.value=this.value.replace(/[^0-9]/g,''); if(this.value) this.nextElementSibling?.focus();"
                       <?= $i===0 ? 'autofocus' : '' ?>>
            <?php endfor; ?>
        </div>

        <button type="submit" style="padding:12px 40px; background:#1976d2; color:white; border:none; border-radius:6px; font-size:16px;">Verify & Create Account</button>
    </form>

    <script>
        // Allow paste full 6-digit code
        document.querySelectorAll('input[name="otp[]"]').forEach((input, idx) => {
            input.addEventListener('paste', e => {
                const paste = e.clipboardData.getData('text').replace(/\D/g,'').slice(0,6);
                if (paste.length === 6) {
                    paste.split('').forEach((char, i) => {
                        const box = document.querySelectorAll('input[name="otp[]"]')[i];
                        if (box) box.value = char;
                    });
                    e.preventDefault();
                }
            });
        });
    </script>
</div>