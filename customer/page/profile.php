<?php
$_body_class = 'profile-page';
$_page_title = "My Profile";
$_title = $_page_title;

require '../_base.php';
include '../../_head.php';
include '../../_header.php';


global $_user;
$customer = $_user; 

$default_photo = 'default_pic.jpg'; 

$customer->photo = $customer->photo ?: 'default_pic.jpg';

$info = temp('info'); 
?>

<?php if ($info): ?>
<div class="alert-success-fixed">
    <div class="alert-content">
        <strong>Success!</strong> <?= encode($info) ?>
        <span class="alert-close">×</span>
    </div>
</div>
<?php endif; ?>

<div class="container-profile">
    <h1>Account Profile</h1>
    
    <div class="profile-details">
        
        <div class="profile-photo">
            <img src="<?= base("uploads/{$customer->photo}") ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%;">
        </div>

        <h2>Information</h2>
        <table>
            <tr>
                <th>Customer ID:</th>
                <td><?= encode($customer->customer_id) ?></td> 
            </tr>
            <tr>
                <th>Username:</th>
                <td><?= encode($customer->username) ?></td> 
            </tr>
            <tr>
                <th>Email Address:</th>
                <td><?= encode($customer->email) ?></td>
            </tr>
        </table>
        
        <div class="actions" style="margin-top: 30px;">
            <a href="edit_profile.php" class="button-primary">Edit Profile</a>
            <a href="change_password.php" class="button-secondary">Change Password</a>
            <a href="/page/logout.php" class="button-secondary">Log Out</a>
        </div>
        
    </div>
</div>




