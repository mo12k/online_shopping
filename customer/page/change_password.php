
<?php
$_body_class = 'change-password-page';
$_page_title = "Change Password";
$_title = $_page_title;

require '_base.php'; 
include '_head.php'; 
include '_header.php';



global $_user, $_db; 
$customer = $_user;
$customer_id = $customer->customer_id;

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
        $check_sql = "SELECT COUNT(*) FROM customer WHERE password = SHA1(?) AND customer_id = ?";
        $check_stm = $_db->prepare($check_sql);
        $check_stm->execute([$current_password, $customer->customer_id]);
        
        if ($check_stm->fetchColumn() == 0) {
            $_err['current_password'] = "Incorrect current password.";
        }
    }

    if (!isset($_err['current_password'])) {
        
        if (!$new_password) {
            $_err['new_password'] = 'Required';
        } elseif (strlen($new_password) < 8 || strlen($new_password) > 11) {
            $_err['new_password'] = 'Between 8–11 characters';
        } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9])/', $new_password)) {
            $_err['new_password'] = "Must contain uppercase, lowercase, number and special character";
        }

        if ($new_password !== $confirm_password) {
            $_err['confirm_password'] = "Passwords do not match.";
        }
    }

    if (!$_err) {
        $hashed_password = SHA1($new_password);

        $update_sql = "UPDATE customer SET password = ? WHERE customer_id = ?";
        $update_stm = $_db->prepare($update_sql);

        if ($update_stm->execute([$hashed_password, $customer->customer_id])) {
            temp('info', 'Your password has been updated successfully.');
            redirect('profile.php'); 
        } else {
            $_err['general'] = "Failed to update password due to a database error.";
        }
    }
}
?>

<div class="container-profile">
    <form id="change-password-form" method="POST" action="change_password.php">
        <h1>Change Password</h1>
        
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
            <p class="password-hint">
                (8-11 characters, must include Uppercase, Lowercase, Number, and Special character)
            </p>

            <label for="confirm_password">Confirm New Password *</label>
            <?= html_password('confirm_password', '', $confirm_password) ?>
            <?= err('confirm_password') ?>
        
        </div>

        <div class="actions">
            <button type="submit" name="change_password" class="button-primary">Set New Password</button>
            <a href="profile.php" class="button-secondary">Cancel</a>
        </div>
        
    </form>
</div>

<?php 
include '_foot.php'; 
?>

