<?php
require '../_base.php';

if (!isset($_SESSION['customer_id'])) {
    redirect('../../page/login.php');
}

$customer_id = $_SESSION['customer_id'];

$return_to = 'edit_profile.php';

if (!is_post()) {
    redirect($return_to);
}

$address_id = (int)req('address_id');

if ($address_id <= 0) {
    temp('error', 'Invalid address.');
    redirect($return_to);
}

try {
    $stm = $_db->prepare('DELETE FROM customer_address WHERE address_id = ? AND customer_id = ?');
    $stm->execute([$address_id, $customer_id]);

    if ($stm->rowCount() > 0) {
        temp('info', 'Address deleted.');
    } else {
        temp('error', 'Address not found (or already deleted).');
    }
} catch (Throwable $e) {
    error_log('Delete address failed: ' . $e->getMessage());
    temp('error', 'Unable to delete this address right now.');
}

redirect($return_to);