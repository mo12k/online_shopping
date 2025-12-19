<?php

$_body_class = 'admin-profile-page';
$_page_title = "Admin Profile";
$_title = $_page_title;

require '../_base.php'; 

admin_require_login(); 

include '../_head.php';

$admin_id = $_SESSION['admin_id'];

$info = temp('info'); 
$error = temp('error'); 

$stm = $_db->prepare('SELECT * FROM admin WHERE admin_id = ? ');
$stm->execute([$admin_id]);
$admin = $stm->fetch();

if (!$admin) {
    redirect('/admin/login.php', 'error', 'Admin account not found. Please log in again.');
}

?>

<?php if ($info): ?>
<div class="alert-success-fixed">
    <div class="alert-content">
        <strong>Success!</strong> <?= encode($info) ?>
        <span class="alert-close">×</span>
    </div>
</div>
<?php endif; ?>


<div class="profile">
    <h1>Admin Profile</h1>
    <div class="profile-photo">

        <?php if ($admin->photo): ?>
            <img src="../images/profile/<?= $admin->photo?>">
        <?php else: ?>
            <img src="../images/profile/default_pic.jpg">   
        <?php endif; ?>
        <div>
    <table>
        <tr>
            <th>Admin ID:</th>
            <td><?= encode($admin->admin_id) ?></td> 
        </tr>           
        <tr>
            <th>Username:</th>
            <td><?= encode($admin->username) ?></td> 
        </tr>
        <tr>
            <th>Email Address:</th>
            <td><?= encode($admin->email) ?></td>
        </tr>
    </table>

    <div class="actions" style="margin-top: 30px;">
        <a href="editadminprofile.php" class="button-secondary">Edit Profile</a>
    </div>
    <div class="actions" style="margin-top: 30px;">
        <a href="changeadminpassword.php" class="button-secondary">Change Password</a>
    </div>
    
        
</div>

<?php include '../_foot.php'; ?>
