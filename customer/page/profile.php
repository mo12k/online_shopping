<?php

$_body_class = 'profile-page';
$_page_title = "My Profile";
$_title = $_page_title;

require '../_base.php';
include '../../_head.php';
include '../../_header.php';


$customer_id = $_SESSION['customer_id'];

include '../../_head.php';
include '../../_header.php';

$info = temp('info'); 


$stm = $_db->prepare('SELECT * FROM customer WHERE customer_id = ? ');
$stm->execute([$customer_id]);
$customer = $stm->fetch();

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

        <div class="profile-photo">

        <?php if ($customer->photo): ?>
            <img src="../../images/profile/<?= $customer->photo?>">
        <?php else: ?>
            <img src="../../images/profile/default_pic.jpg">
        <?php endif; ?>
        <div>

        <h2>Your Information</h2>
         
        <table>                     
            <tr>
                <th>Username:</th>
                <td><?= encode($customer->username) ?></td> 
            </tr>
            <tr>
                <th>Email Address:</th>
                <td><?= encode($customer->email) ?></td>
            </tr>
            <tr>
                <th>Phone Number:</th>
                <td><?= encode($customer->phone) ?></td>
            </tr>
        </table>
        
        <div class="actions" style="margin-top: 30px;">
            <a href="edit_profile.php" class="button-primary">Edit Profile</a>
            <a href="change_password.php" class="button-secondary">Change Password</a>
            <a href="/page/logout.php" class="button-secondary">Log Out</a>
        </div>
        
    </div>
</div>

<?php include '../../_footer.php'; ?>
