<?php
require '../_base.php';

// Capture customer id before clearing session keys
$customerId = $_SESSION['customer_id'] ?? null;

// 2. Delete the "Remember Me" cookies (if they exist)
setcookie('remember_me',       '', time() - 3600, '/');
setcookie('remember_me_user',  '', time() - 3600, '/');

// 3. (Recommended) Delete the remember-me token from database
//     We use the null-coalescing operator to avoid errors if the key doesn't exist
if ($customerId !== null) {
    $stmt = $_db->prepare("
        DELETE FROM token 
        WHERE customer_id = ? 
          AND token_type = 'remember'
    ");
    $stmt->execute([$customerId]);
}

// Clear only customer-related session keys (do not destroy the whole session,
unset(
    $_SESSION['customer_id'],
    $_SESSION['customer_username'],
    $_SESSION['profile_picture'],
    $_SESSION['pending_order'],
    $_SESSION['payment_retry']
);


redirect('/index.php');