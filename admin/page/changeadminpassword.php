<?php
$_body_class = 'admin-change-password-page';
$_page_title = "Change Admin Password";
$_title = $_page_title;

require '../_base.php'; 

admin_require_login(); 

include '../_head.php'; 
$admin_id = $_SESSION['admin_id'];

$_err = [];
$current_password = '';
$new_password = '';
$confirm_password = '';

if (is_post()) {
    $current_password = req('current_password');
    $new_password = req('new_password');
    $confirm_password = req('confirm_password');
    
    if (!$current_password) {
        $_err['current_password'] = "Required";
    } else {
        $check_sql = "SELECT COUNT(*) FROM admin WHERE password = SHA1(?) AND admin_id = ?";
        $check_stm = $_db->prepare($check_sql);
        $check_stm->execute([$current_password, $admin_id]);
        
        if ($check_stm->fetchColumn() == 0) {
            $_err['current_password'] = "Incorrect current password.";
        }
    }

    if (!isset($_err['current_password'])) {

        if ($new_password !== $confirm_password) {
            $_err['confirm_password'] = "Passwords do not match.";
        }
    }

    if (!$_err) {
        $hashed_password = SHA1($new_password);

        $update_sql = "UPDATE admin SET password = ? WHERE admin_id = ?";
        $update_stm = $_db->prepare($update_sql);

        if ($update_stm->execute([$hashed_password, $admin_id])) {
            temp('info', 'Your password has been updated successfully.');
            redirect('adminprofile.php'); 
        } else {
            $_err['general'] = "Failed to update password due to a database error.";
        }
    }
}


?>
<main>
<div class="profile">
    <form id="change-password-form" method="POST" action="changeadminpassword.php">
        <h1>Change Admin Password</h1>
        
        <?php if (isset($_err['general'])): ?>
            <div class="alert-error"><?= htmlspecialchars($_err['general']) ?></div>
        <?php endif; ?>

        <div class="form-details">

            <label for="current_password">Current Password *</label>
            <?= html_password('current_password', '', $current_password) ?>
            <?= err('current_password') ?>

            <label for="new_password">New Password *</label>
            <?= html_password('new_password', '', $new_password) ?>
            <?= err('new_password') ?>

            <label for="confirm_password">Confirm New Password *</label>
            <?= html_password('confirm_password', '', $confirm_password) ?>
            <?= err('confirm_password') ?>
        
        </div>

        <div class="actions">
            <button type="submit" name="change_password" class="button-primary">Set New Password</button>
            <a href="adminprofile.php" class="button-secondary">Cancel</a>
        </div>
        
    </form>
</div>

<?php 
include '../_foot.php'; 
?>
