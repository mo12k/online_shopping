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

<style>
    /* Center the whole photo section */
    .profile-photo {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* Restrict profile image size */
    .profile-photo img {
        width: 160px;
        height: 160px;
        border-radius: 8px;
        object-fit: cover;
        display: block;
        border: 2px solid #ddd;
        margin-bottom: 16px;
    }

    /* Keep the table full width under the centered image */
    .profile-photo > div {
        width: 100%;
    }

    /* Make the links look like buttons */
    .button-secondary {
        display: inline-block;
        padding: 10px 18px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 15px;
        min-width: 220px;
        text-align: center;
        background: #ffa502;
        color: #000;
        border: 1px solid #ffa502;
        transition: 0.2s ease;
    }

    .button-secondary:hover {
        background: #ff8c00;
        border-color: #ff8c00;
    }

    /* Optional: reduce spacing between the two action sections */
    .actions {
        margin-top: 18px !important;
        text-align: center;
    }
</style>


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
