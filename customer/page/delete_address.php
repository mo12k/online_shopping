<?php

require '../_base.php';

if (!isset($_SESSION['customer_id'])) {
    redirect('../../page/login.php');
}

$customer_id = $_SESSION['customer_id'];

$return_to = 'edit_profile.php';

// Pending changes (edit profile flow)
$pending_add_key = 'profile_pending_address_add';
$pending_del_key = 'profile_pending_address_delete';

if(is_post()) {
    $pending_index = req('pending_index');
    $address_id = req('address_id');

    // Remove a not-yet-saved (pending) address
    if ($pending_index !== null && $pending_index !== '') {
        $pending_add = $_SESSION[$pending_add_key] ?? [];
        $idx = (int)$pending_index;
        if (isset($pending_add[$idx])) {
            unset($pending_add[$idx]);
            $_SESSION[$pending_add_key] = array_values($pending_add);
        }
        redirect($return_to);
    }

    //commit on Save Changes
    $address_id = (int)$address_id;
    if ($address_id <= 0) {
        redirect($return_to);
    }

    $pending_del = $_SESSION[$pending_del_key] ?? [];
    $pending_del[] = $address_id;
    $_SESSION[$pending_del_key] = array_values(array_unique(array_map('intval', $pending_del)));
    redirect($return_to);


redirect($return_to);
}
