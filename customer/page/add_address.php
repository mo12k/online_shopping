<?php
require '../_base.php';
include '../../_head.php';
include '../../_header.php';
$current = 'profile';
$_title = 'Add Address';

if (!isset($_SESSION['customer_id'])) {
    redirect('../../page/login.php');
}

$customer_id = $_SESSION['customer_id'];

// Where to go after cancel/save (safe whitelist)
$return = req('return') ?? 'checkout';
$return_to = $return === 'profile' ? 'edit_profile.php' : 'checkout.php';

$addresses = get_customer_addresses($customer_id);
$address_count = count($addresses);
$limit_reached = $address_count >= 3;

if (is_post()) {
    if ($limit_reached) {
        $limit_reached = true;
    }

    $address = trim(req('address'));
    $city = trim(req('city'));
    $state = trim(req('state'));
    $postcode = trim(req('postcode'));
    
    if (empty($address) || empty($city) || empty($state) || empty($postcode)) {
        redirect();
    }
    
    if (!$limit_reached) {
        $stm = $_db->prepare('
            INSERT INTO customer_address (customer_id, address, city, state, postcode) 
            VALUES (?, ?, ?, ?, ?)
        ');
        $stm->execute([$customer_id, $address, $city, $state, $postcode]);
        redirect($return_to);
    }
}

?>

<style>
.container {
    max-width: 600px;
    margin: 40px auto;
    padding: 0 20px;
}

.address-form {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 16px;
    box-sizing: border-box;
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn-primary {
    padding: 12px 30px;
    background: #6d4c41;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
}

.btn-cancel {
    padding: 12px 30px;
    background: #f0f0f0;
    color: #666;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
}
</style>

<div class="container">
    <h1>Add New Address</h1>

    <?php if ($limit_reached): ?>
        <div style="background:#fff3cd; border:1px solid #ffeeba; color:#856404; padding:12px 14px; border-radius:8px; margin:15px 0;">
            You have reached the maximum of 3 saved addresses.
        </div>
    <?php endif; ?>
    
    <form method="post" class="address-form">
        <div class="form-group">
            <label for="address">Full Address *</label>
            <textarea id="address" name="address" rows="3" required <?= $limit_reached ? 'disabled' : '' ?>></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="city">City *</label>
                <input type="text" id="city" name="city" required <?= $limit_reached ? 'disabled' : '' ?>>
            </div>
            
            <div class="form-group">
                <label for="state">State *</label>
                <input type="text" id="state" name="state" required <?= $limit_reached ? 'disabled' : '' ?>>
            </div>
            
            <div class="form-group">
                <label for="postcode">Postcode *</label>
                <input
                    type="text"
                    id="postcode"
                    name="postcode"
                    pattern="[0-9]+"
                    oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                    required
                    <?= $limit_reached ? 'disabled' : '' ?>
                >
            </div>
        </div>
        
        <div class="form-actions">
            <?php if (!$limit_reached): ?>
                <button type="submit" class="btn-primary">Save Address</button>
            <?php endif; ?>
            <a href="<?= $return_to ?>" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<?php include '../../_footer.php'; ?>