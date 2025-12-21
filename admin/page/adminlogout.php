<?php
require '../_base.php';

// Only log out the admin portion of the session.
unset(
	$_SESSION['admin_id'],
	$_SESSION['admin_username'],
	$_SESSION['profile_pic'],
	$_SESSION['username'],
	$_SESSION['email']
);

redirect('../../index.php');
?>