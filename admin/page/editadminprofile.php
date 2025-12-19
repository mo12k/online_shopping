<?php
$_body_class = 'edit-profile-page';
$_page_title = "Edit Profile";
$_title = $_page_title;

require '../_base.php'; 

admin_require_login(); 

$admin_id = $_SESSION['admin_id'];

include '../_head.php';

$info = temp('info'); 
$error = temp('error');
$default_pic = 'default_pic.jpg';

$stm = $_db->prepare('SELECT * FROM admin WHERE admin_id = ? ');
$stm->execute([$admin_id]);
$admin = $stm->fetch();

if (is_post()) {
    $_err = []; 
    $username = trim(req('username') ?? '');
    $email    = trim(req('email'));
    $photo_name = $admin->photo; 

    if ($username == '') {
        $_err['username'] = 'Username is required';
    } 
    
    if ($email == '') {
        $_err['email'] = 'Email is required';
    }

    if (!$_err) {
        $stm = $_db->prepare("SELECT COUNT(*) FROM admin WHERE (username = ? OR email = ?) AND admin_id <> ?");
        $stm->execute([$username, $email, $admin_id]);
        if ($stm->fetchColumn() > 0) {
            $_err['username'] = 'This username or email is already taken by someone else';
        }
    }

    if (!$_err) {
        $f = get_file('photo');
        if ($f && $f->size > 0) {
            if ($photo_name && $photo_name !== $default_pic && file_exists("../images/profile/$photo_name")) {
                unlink("../images/profile/$photo_name");
            }
            $photo_name = save_photo($f, '../images/profile');
        } 
            
        $stm = $_db->prepare("UPDATE admin SET username = ?, email = ?, photo = ? WHERE admin_id = ?");
        $stm->execute([$username, $email, $photo_name, $admin_id]);

        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        
        temp('info', "Profile updated successfully!");
        redirect('adminprofile.php'); 
    }
} else {
    $username = $admin->username;
    $email    = $admin->email;
}
?>

<main>
<?php if ($info): ?>
<div class="alert-success-fixed">
    <div class="alert-content">
        <strong>Success!</strong> <?= encode($info) ?>
        <span class="alert-close">×</span>
    </div>
</div>
<?php endif; ?>

<div class="profile">
    <h1>Edit Account Profile</h1>   
    <form action="editadminprofile.php" method="POST" enctype="multipart/form-data">
       <div class="profile-photo">
            <img src="../images/profile/<?= $admin->photo ?: $default_pic ?>">
            <div class="file-upload-group">
                <label for="photo">Change Photo</label>
                <input type="file" id="photo" name="photo" accept="image/*">
                <?php if (isset($_err['photo'])): ?>
                    <span class="error"><?= encode($_err['photo']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <table>
            <tr>
                <th><label for="username">Username:</label></th>
                <td>
                    <input type="text" id="username" name="username" value="<?= encode($username) ?>" required>
                    <?php if (isset($_err['username'])): ?>
                        <span style="color:red; display:block; font-size:0.9em;"><?= encode($_err['username']) ?></span>
                    <?php endif; ?>
                </td> 
            </tr>
            <tr>
                <th><label for="email">Email Address:</label></th>
                <td>
                    <input type="email" id="email" name="email" value="<?= encode($email) ?>" required>
                    <?php if (isset($_err['email'])): ?>
                        <span style="color:red; display:block; font-size:0.9em;"><?= encode($_err['email']) ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        
        <div class="actions" style="margin-top: 30px;">
            <button type="submit" class="button-primary">Save Changes</button>
            <a href="adminprofile.php" class="button-secondary">Cancel</a>
        </div>
    </form>
</div>
</main>
<?php include '../_foot.php'; ?>