<?php

$_body_class = 'change-password-page';
$_page_title = "Change Password";
$_title = $_page_title;

require '../_base.php'; 

$customer_id = $_SESSION['customer_id'];

include '../../_head.php';
include '../../_header.php';

$info = temp('info'); 
$error = temp('error');

$stm = $_db->prepare('SELECT customer_id FROM customer WHERE customer_id = ?');
$stm->execute([$customer_id]);
$customer = $stm->fetch();

if (!$customer) {
    redirect('/page/login.php', 'error', 'User account not found.');
}

if (is_post()) {
    
    $_err = []; 
    
    $current_password = req('current_password');
    $new_password     = req('new_password');
    $confirm_password = req('confirm_password');

    if ($current_password == '') {
        $_err['current_password'] = 'Current password is required.';
    }

    if ($new_password == '') {
        $_err['new_password'] = 'New password is required.';
    } elseif (mb_strlen($new_password) < 6) {
        $_err['new_password'] = 'Password must be at least 6 characters.';
    }

    if ($confirm_password == '') {
        $_err['confirm_password'] = 'Confirm password is required.';
    } elseif ($new_password !== $confirm_password) {
        $_err['confirm_password'] = 'New password and confirmation do not match.';
    }

    
    if (!isset($_err['current_password'])) {
        $stm_verify = $_db->prepare('
            SELECT COUNT(*) FROM customer
            WHERE password = SHA(?) AND customer_id = ?
        ');
        $stm_verify->execute([$current_password, $customer_id]);

        if ($stm_verify->fetchColumn() == 0) {
            $_err['current_password'] = 'The current password you entered is incorrect.';
        }
    }

   
    if (!$_err) {
        
        $_db->prepare(
            "UPDATE customer 
             SET password = SHA1(?) 
             WHERE customer_id=?"
        )->execute([
            $new_password, 
            $customer_id
        ]);

        temp('info', "Your password has been changed successfully!");
        redirect('profile.php');
    }
}

$error = temp('error');
?>

<main>

<?php if ($info): ?>
<div class="alert-success-fixed">
    <div class="alert-content">
        <strong>Success!</strong> <?= encode($info) ?>
        <span class="alert-close">×</span>
    </div>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert-error-fixed">
    <div class="alert-content">
        <strong>Error!</strong> <?= encode($error) ?>
        <span class="alert-close">×</span>
    </div>
</div>
<?php endif; ?>

<div class="container-profile">
    <h1>Change Password</h1>   
    
    <form action="change_password.php" method="POST">

        <h2>Security</h2>
         
        <table>
            <tr>
                <th><label for="current_password">Current Password:</label></th>
                <td>
                    <input type="password" id="current_password" name="current_password" required>
                    <?php if (isset($_err['current_password'])): ?>
                        <span style="color:red; display:block; font-size:0.9em;"><?= encode($_err['current_password']) ?></span>
                    <?php endif; ?>
                </td> 
            </tr>
            
            <tr>
                <th><label for="new_password">New Password:</label></th>
                <td>
                    <input type="password" id="new_password" name="new_password" required>
                    <?php if (isset($_err['new_password'])): ?>
                        <span style="color:red; display:block; font-size:0.9em;"><?= encode($_err['new_password']) ?></span>
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <th><label for="confirm_password">Confirm New Password:</label></th>
                <td>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <?php if (isset($_err['confirm_password'])): ?>
                        <span style="color:red; display:block; font-size:0.9em;"><?= encode($_err['confirm_password']) ?></span>
                    <?php endif; ?>
                </td>
            </tr>

        </table>
        
        <div class="actions" style="margin-top: 30px;">
            <button type="submit" class="button-primary">Change Password</button>
            <a href="profile.php" class="button-secondary">Cancel</a>
        </div>
        
    </form>
</div>

</main>

<?php include '../../_footer.php'; ?>
