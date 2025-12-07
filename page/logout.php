<?php
require '../_base.php';

// 1. Destroy the session
session_destroy();

// 2. Delete the "Remember Me" cookies (if they exist)
setcookie('remember_me',       '', time() - 3600, '/');
setcookie('remember_me_user',  '', time() - 3600, '/');

// 3. (Recommended) Delete the remember-me token from database
//     We use the null-coalescing operator to avoid errors if the key doesn't exist
$customerId = $_SESSION['customer_id'] ?? null;

if ($customerId !== null) {
    $stmt = $_db->prepare("
        DELETE FROM token 
        WHERE customer_id = ? 
          AND token_type = 'remember'
    ");
    $stmt->execute([$customerId]);
}
redirect('/index.php');