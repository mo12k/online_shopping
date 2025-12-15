<?php
$_body_class = 'edit-profile-page';
$_page_title = "Edit Profile";
$_title = $_page_title;

require '_base.php'; 
include '_head.php'; 
include '_header.php';

auth(); 

global $_user, $_db; 
$customer = $_user;
$customer_id = $customer->customer_id; 

$customer->photo = $customer->photo ?: 'default_pic.jpg';


$username = $customer->username;
$email = $customer->email;
$photo_filename = $customer->photo; 

if (is_post()) {
    
    $username = req('username');
    $email = req('email');
    $photo_file = get_file('photo');

    if (!$username) {
        $_err['username'] = "Required";
    } else if (strlen($username) > 100) {
        $_err['username'] = "Maximum length 100";
    } 
    else if ($username !== $customer->username && !is_unique($username, 'customer', 'username')) {
        $_err['username'] = "Duplicate Username";
    }

    if (!$email) {
        $_err['email'] = "Required";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_err['email'] = "Invalid Email Format";
    } 
    else if ($email !== $customer->email && !is_unique($email, 'customer', 'email')) {
        $_err['email'] = "Duplicate Email";
    }
    
    if ($photo_file) {
        if (!str_starts_with($photo_file->type, 'image/')) { 
            $_err['photo'] = 'Must be an image.';
        }
        else if ($photo_file->size > 5 * 1024 * 1024) { 
            $_err['photo'] = 'Maximum 5MB.';
        }
    }
    
    if (!$_err) {
        $upload_successful = true;

        if ($photo_file) {
            $upload_dir = root('uploads/');
            $new_filename = uniqid() . '_' . $photo_file->name;
            
            if (move_uploaded_file($photo_file->tmp_name, $upload_dir . $new_filename)) {
                
                if ($customer->photo && $customer->photo !== $default_photo) {
                    $old_photo_path = root("uploads/{$customer->photo}");
                    if (file_exists($old_photo_path)) {
                        unlink($old_photo_path);
                    }
                }
                $photo_filename = $new_filename; 
            } else {
                $_err['photo'] = 'Failed to move uploaded file. Check folder permissions.';
                $upload_successful = false;
            }
        }

        if ($upload_successful) {
            $update_sql = "UPDATE customer SET username = ?, email = ?, photo = ? WHERE customer_id = ?";
            $update_stm = $_db->prepare($update_sql);
            $success = $update_stm->execute([$username, $email, $photo_filename, $customer_id]);

            if ($success) {
                $_user->username = $username;
                $_user->email = $email;
                $_user->photo = $photo_filename;
                $_SESSION['customer_username'] = $username;
                
                temp('info', 'Your profile details and photo have been updated successfully.');
                redirect('profile.php'); 
            } else {
                temp('info', 'No changes were detected or database error occurred.');
                redirect('profile.php'); 
            }
        }
    }
}
?>

<div class="container-profile">
    <form id="edit-profile-form" method="POST" action="edit_profile.php" enctype="multipart/form-data">
        <h2>Edit Account Details</h2>
        
        <label for="username">Username *</label>
        <?= html_text('username', 'maxlength="100"', $username) ?>
        <?= err('username') ?>

        <label for="email">Email Address *</label>
        <?= html_text('email', 'placeholder="example@example.com"', $email) ?>
        <?= err('email') ?>
        
        <label for="photo">Profile Picture</label>
        
        <label class="upload">
            <?= html_file('photo', 'image/*', 'onchange="previewImage(event)"') ?>
            
            <img id="profile-preview" src="<?= base("uploads/{$photo_filename}") ?>">
        </label>
        <?= err('photo') ?>
        
        <button type="submit" name="save_changes">Save Changes</button>
        
        <div class="back-link">
            <a href="profile.php">← Back to Profile</a>
        </div>
    </form>
</div>

