<?php
require '../_base.php';

if (!isset($_SESSION['customer_id'])) {
    redirect('../../page/login.php');
}

$customer_id = $_SESSION['customer_id'];

$return_to = 'edit_profile.php';

if(is_post()) {
    $address_id = req('address_id');

    if (!$address_id) {
        redirect($return_to);
    }
    
    // Delete address
    $stm = $_db->prepare('DELETE FROM customer_address WHERE address_id = ? AND customer_id = ?');
    $stm->execute([$address_id, $customer_id]);
    redirect($return_to);

}
