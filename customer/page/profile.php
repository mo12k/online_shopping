<?php

$_body_class = 'profile-page';
$_page_title = "My Profile";
$_title = $_page_title;


require '../_base.php'; 

$customer_id = $_SESSION['customer_id'];

include '../../_head.php';
include '../../_header.php';

$info = temp('info'); 
$default_photo = 'default_pic.jpg'; 

$stm = $_db->prepare('SELECT * FROM customer WHERE customer_id = ? ');
$stm->execute([$customer_id]);
$customer = $stm->fetch();
$photo_column = 'photo';
$default_photo = 'default_pic.jpg';
$photo_name = $customer->photo;

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
       <?php 
            if ($customer->photo) {
                $img_src = '../upload/' . $customer->photo;
            } else {
                $img_src = '../../admin/images/profile/' . $default_photo;
            }
            ?>
            
        <img src="<?= $img_src ?>" 
             alt="Profile Picture" 
             style="width: 150px; height: 150px; border-radius: 50%;">

        <h2>Your Information</h2>
         
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
