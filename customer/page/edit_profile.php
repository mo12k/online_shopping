<?php

$_body_class = 'edit-profile-page';
$_page_title = "Edit Profile";
$_title = $_page_title;

require '../_base.php'; 

$customer_id = $_SESSION['customer_id'];

// Pending address changes (only committed when Save Changes is clicked)
$pending_add_key = 'profile_pending_address_add';
$pending_del_key = 'profile_pending_address_delete';

// Cancel should discard any staged address changes
if (is_get() && get('cancel')) {
    unset($_SESSION[$pending_add_key], $_SESSION[$pending_del_key]);
    redirect('profile.php');
}

include '../../_head.php';
include '../../_header.php';

$info = temp('info'); 
$error = temp('error');



$stm = $_db->prepare('SELECT * FROM customer WHERE customer_id = ? ');
$stm->execute([$customer_id]);
$customer = $stm->fetch();

$addresses_db = get_customer_addresses($customer_id);
$pending_add = $_SESSION[$pending_add_key] ?? [];
$pending_del = $_SESSION[$pending_del_key] ?? [];
$pending_del = array_values(array_unique(array_map('intval', (array)$pending_del)));

$addresses = array_values(array_filter($addresses_db, function ($a) use ($pending_del) {
    return !in_array((int)$a->address_id, $pending_del, true);
}));

foreach ((array)$pending_add as $p) {
    $o = (object)[
        'address_id' => null,
        'address' => $p['address'] ?? '',
        'city' => $p['city'] ?? '',
        'state' => $p['state'] ?? '',
        'postcode' => $p['postcode'] ?? '',
        '_pending_index' => null,
    ];
    $addresses[] = $o;
}

// attach pending index for delete forms
for ($i = 0, $pi = 0; $i < count($addresses); $i++) {
    if ($addresses[$i]->address_id === null) {
        $addresses[$i]->_pending_index = $pi;
        $pi++;
    }
}

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
        try {
            $_db->beginTransaction();

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

            // Apply staged address changes (commit on Save Changes)
            $pending_add = $_SESSION[$pending_add_key] ?? [];
            $pending_del = $_SESSION[$pending_del_key] ?? [];
            $pending_del = array_values(array_unique(array_map('intval', (array)$pending_del)));

            if (!empty($pending_del)) {
                $stm_del = $_db->prepare('DELETE FROM customer_address WHERE address_id = ? AND customer_id = ?');
                foreach ($pending_del as $aid) {
                    if ($aid > 0) {
                        $stm_del->execute([$aid, $customer_id]);
                    }
                }
            }

            if (!empty($pending_add)) {
                $stm_ins = $_db->prepare('
                    INSERT INTO customer_address (customer_id, address, city, state, postcode)
                    VALUES (?, ?, ?, ?, ?)
                ');

                // enforce max 3 at commit time
                $after = get_customer_addresses($customer_id);
                $remaining_slots = 3 - count($after);
                if ($remaining_slots < 0) $remaining_slots = 0;

                $i = 0;
                foreach ($pending_add as $p) {
                    if ($i >= $remaining_slots) break;
                    $addr = trim($p['address'] ?? '');
                    $city = trim($p['city'] ?? '');
                    $state = trim($p['state'] ?? '');
                    $postcode = trim($p['postcode'] ?? '');
                    if ($addr === '' || $city === '' || $state === '' || $postcode === '') {
                        continue;
                    }
                    $stm_ins->execute([$customer_id, $addr, $city, $state, $postcode]);
                    $i++;
                }
            }

            $_db->commit();

            unset($_SESSION[$pending_add_key], $_SESSION[$pending_del_key]);

            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['profile_picture'] = $photo_name ?: 'default_pic.jpg';

            temp('info', "Profile updated successfully!");
            redirect('profile.php');

        } catch (Exception $e) {
            if ($_db->inTransaction()) {
                $_db->rollBack();
            }
            error_log('Edit profile save failed: ' . $e->getMessage());
            temp('error', 'Failed to save changes. Please try again.');
            redirect('edit_profile.php');
        }
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

                                    <button
                                        type="submit"
                                        class="button-secondary"
                                        style="width:90px;"
                                        form="<?= $address->address_id ? ('delete_address_' . $address->address_id) : ('delete_pending_address_' . $address->_pending_index) ?>"
                                        onclick="return confirm('Delete this address?');"
                                    >Delete</button>
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
            <a href="edit_profile.php?cancel=1" class="button-secondary">Cancel</a>
        </div>
        
    </form>

    <?php if (!empty($addresses)): ?>
        <?php foreach ($addresses as $address): ?>
            <?php if ($address->address_id): ?>
                <form id="delete_address_<?= $address->address_id ?>" method="post" action="delete_address.php" style="display:none;">
                    <input type="hidden" name="address_id" value="<?= $address->address_id ?>">
                </form>
            <?php else: ?>
                <form id="delete_pending_address_<?= $address->_pending_index ?>" method="post" action="delete_address.php" style="display:none;">
                    <input type="hidden" name="pending_index" value="<?= $address->_pending_index ?>">
                </form>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</main>

<?php include '../../_footer.php'; ?>
