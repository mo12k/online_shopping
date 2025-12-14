<?php
$_body_class = 'change-password-page';
$_page_title = "Change Password";
$_title = $_page_title;

// FIX 1: Change relative path from '../_base.php' to '_base.php'
require '_base.php'; 
// FIX 2 & 3: Change relative paths for includes
include '_head.php'; 
include '_header.php';

// Ensure the user is logged in
auth(); 

global $_customer, $_db; 
$customer = $_customer; 
$customer_id = $customer->customer_id; 

$_err = []; 

if (is_post()) {
    
    $current_password = req('current_password');
    $new_password = req('new_password');
    $confirm_password = req('confirm_password');

    // --- Validation ---

    if (!$current_password) {
        $_err['current_password'] = "Required";
    }

    if (!$new_password) {
        $_err['new_password'] = "Required";
    } else if (strlen($new_password) < 8) {
        $_err['new_password'] = "Must be at least 8 characters";
    }

    if ($new_password !== $confirm_password) {
        $_err['confirm_password'] = "Passwords do not match";
    }
    
    // Check if passwords are the same
    if ($current_password && $new_password && $current_password === $new_password) {
        $_err['new_password'] = "New password cannot be the same as current password";
    }

    // --- Check Current Password Against Database ---
    if (!$_err) {
        $check_sql = "SELECT password FROM customer WHERE customer_id = ?";
        $check_stm = $_db->prepare($check_sql);
        $check_stm->execute([$customer_id]);
        $customer_data = $check_stm->fetch(); 

        if ($customer_data && password_verify($current_password, $customer_data->password)) {
            // Success: Current password is correct
            
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update the password in the database
            $update_sql = "UPDATE customer SET password = ? WHERE customer_id = ?";
            $update_stm = $_db->prepare($update_sql);
            $success = $update_stm->execute([$hashed_password, $customer_id]);

            if ($success) {
                temp('info', 'Your password has been successfully updated. Please log in with your new password.');
                redirect('logout.php'); 
            } else {
                $_err['general'] = 'Failed to update password due to a database error.';
            }

        } else {
            // Failure: Current password is incorrect
            $_err['current_password'] = 'Incorrect current password';
        }
    }
}
?>

<div class="container-change-password">
    <form id="change-password-form" method="POST" action="change_password.php">
        <h2>Change Password</h2>
        
        <?php if (isset($_err['general'])): ?>
            <div class="alert-error"><?= htmlspecialchars($_err['general']) ?></div>
        <?php endif; ?>

        <label for="current_password">Current Password *</label>
        <?= html_password('current_password') ?>
        <?= err('current_password') ?>

        <label for="new_password">New Password *</label>
        <?= html_password('new_password') ?>
        <?= err('new_password') ?>
        
        <label for="confirm_password">Confirm New Password *</label>
        <?= html_password('confirm_password') ?>
        <?= err('confirm_password') ?>
        
        <button type="submit" name="change_password">Change Password</button>
        
        <div class="back-link">
            <a href="profile.php">← Back to Profile</a>
        </div>
    </form>
</div>

<?php 
// FIX 4: Change relative path for _foot.php
include '_foot.php'; 
?>