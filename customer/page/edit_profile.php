<?php

$_body_class = 'edit-profile-page';
$_page_title = "Edit Profile";
$_title = $_page_title;

require '../_base.php'; 

$customer_id = $_SESSION['customer_id'];

include '../../_head.php';
include '../../_header.php';

$info = temp('info'); 
$error = temp('error');



$stm = $_db->prepare('SELECT * FROM customer WHERE customer_id = ? ');
$stm->execute([$customer_id]);
$customer = $stm->fetch();

$addresses = get_customer_addresses($customer_id);
$address_count = count($addresses);


if (is_post()) {
    
    $_err = []; 
    $username = trim(req('username') ?? '');
    $email    = trim(req('email'));
    $phone    = trim(req('phone') ?? '');

    
    $photo_name = $customer->photo; 

    
    if ($username == '') {
        $_err['username'] = 'Username is required';
    } elseif (mb_strlen($username) > 50) {
        $_err['username'] = 'Username must not exceed 50 characters';
    }
    
    if ($email == '') {
        $_err['email'] = 'Email is required';
    } elseif (!is_email($email)) { 
        $_err['email'] = 'Invalid email format';
    }

    
    $f = get_file('photo');
    if ($f && $f->size > 0) {

   
        if ($f->size > 5 * 1024 * 1024) {
            $_err['photo'] = 'Maximum 5MB';
        }
        elseif (!str_starts_with($f->type, 'image/')) {
            $_err['photo'] = 'Must be an image';
        }
    }


    $isIdentityChanged =
        $username !== $customer->username ||
        $email !== $customer->email;

    if (!$_err && $isIdentityChanged) {
        $stm = $_db->prepare(
            "SELECT COUNT(*) 
             FROM customer 
             WHERE (username = ? OR email = ?)
               AND customer_id <> ?" 
        );
        $stm->execute([$username, $email, $customer_id]);

        if ($stm->fetchColumn()) {
            $_err['username'] = 'This username or email is already in use by another account.';
        }
    }


    
    
    if (!$_err) {
         
            if ($f && $f->size > 0) {
                // Only delete customer-specific images; never delete shared defaults.
                $oldPhoto = $photo_name ? basename($photo_name) : '';
                if ($oldPhoto && $oldPhoto !== 'default_pic.jpg') {
                    $oldPath = "../../images/profile/$oldPhoto";
                    if (is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                $photo_name = save_photo($f, '../../images/profile');
            }
            
        $_db->prepare(
            "UPDATE customer 
             SET username=?, email=?, photo=?, phone=? 
             WHERE customer_id=?"
        )->execute([
            $username, 
            $email, 
            $photo_name, 
            $phone,      
            $customer_id
        ]);

        
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['profile_picture'] = $photo_name ?: 'default_pic.jpg';
        
        temp('info', "Profile updated successfully!");
        redirect('profile.php'); 
    }

}
else {
    
    $username = $customer->username;
    $email    = $customer->email;
    $phone    = $customer->phone;
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

<?php if ($error): ?>
<div class="alert-error-fixed"> 
    <div class="alert-content">
        <strong>Error!</strong> <?= encode($error) ?>
        <span class="alert-close">×</span>
    </div>
</div>
<?php endif; ?>

<div class="container-profile">
    <h1>Edit Account Profile</h1>   
    
    <form action="edit_profile.php" method="POST" enctype="multipart/form-data">

        <div class="profile-photo">
                <?php if ($customer->photo): ?>
            <img src="../../images/profile/<?= $customer->photo?>">
                <?php else: ?>
            <img src="../../images/profile/default_pic.jpg">
                <?php endif; ?>
            
            <div class="file-upload-group" style="margin-top: 10px;">
                <label for="photo">Change Photo:</label>
                <input type="file" id="photo" name="photo" accept="image/*">
                <small style="display:block; color:#666;">Leave blank to keep current photo.</small>
                <?php if (isset($_err['photo'])): ?>
                    <span style="color:red; font-size:0.9em;"><?= encode($_err['photo']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <h2>Update Information</h2>
         
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

            <tr>
                <th><label for="phone">Phone Number:</label></th>
                <td>
                    <input type="tel" id="phone" name="phone" value="<?= encode($phone) ?>">
                </td>
            </tr>

        </table>

        <h2>Address Information</h2>
        <table>
            <tr>
                <th style="vertical-align:top;">Saved Addresses:</th>
                <td>
                    <?php if (empty($addresses)): ?>
                        <div style="color:#666;">No address saved yet.</div>
                    <?php else: ?>
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            <?php foreach ($addresses as $i => $address): ?>
                                <div style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
                                    <div style="flex:1 1 auto; min-width:0;">
                                        <strong>Address <?= $i + 1 ?>:</strong>
                                        <?= encode($address->address) ?>,
                                        <?= encode($address->city) ?>,
                                        <?= encode($address->state) ?>,
                                        <?= encode($address->postcode) ?>
                                    </div>

                                    <form method="post" action="delete_address.php" style="margin:0; flex:0 0 auto;">
                                        <input type="hidden" name="address_id" value="<?= $address->address_id ?>">
                                        <button type="submit" class="button-secondary" style="width:90px;" onclick="return confirm('Delete this address?');">Delete</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div style="margin-top:12px; color:#666;">
                        Maximum 3 addresses (<?= $address_count ?>/3).
                    </div>

                    <div style="margin-top:12px;">
                        <?php if ($address_count < 3): ?>
                            <a href="add_address.php?return=profile" class="button-secondary">Add Address</a>
                        <?php else: ?>
                            <span style="color:#b00020;">Address limit reached.</span>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        </table>
        <div class="actions" style="margin-top: 30px;">
            <button type="submit" class="button-primary">Save Changes</button>
            <a href="profile.php" class="button-secondary">Cancel</a>
        </div>
        
    </form>
</div>

</main>

<?php include '../../_footer.php'; ?>