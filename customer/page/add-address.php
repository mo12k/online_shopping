<?php
require '../_base.php';
include '../../_head.php';
include '../../_header.php';
$current = 'profile';
$_title = 'Add Address';

// 检查登录
if (!isset($_SESSION['customer_id'])) {
    temp('info', 'Please login to add address');
    redirect('../../page/login.php');
}

$customer_id = $_SESSION['customer_id'];

if (is_post()) {
    $address = trim(req('address'));
    $city = trim(req('city'));
    $state = trim(req('state'));
    $postcode = trim(req('postcode'));
    
    if (empty($address) || empty($city) || empty($state) || empty($postcode)) {
        temp('error', 'All fields are required');
        redirect();
    }
    
    $stm = $_db->prepare('
        INSERT INTO customer_address (customer_id, address, city, state, postcode) 
        VALUES (?, ?, ?, ?, ?)
    ');
    $stm->execute([$customer_id, $address, $city, $state, $postcode]);
    
    temp('success', 'Address added successfully');
    redirect('checkout.php');
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
    
    <form method="post" class="address-form">
        <div class="form-group">
            <label for="address">Full Address *</label>
            <textarea id="address" name="address" rows="3" required></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="city">City *</label>
                <input type="text" id="city" name="city" required>
            </div>
            
            <div class="form-group">
                <label for="state">State *</label>
                <input type="text" id="state" name="state" required>
            </div>
            
            <div class="form-group">
                <label for="postcode">Postcode *</label>
                <input type="text" id="postcode" name="postcode" required>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">Save Address</button>
            <a href="checkout.php" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<?php include '../../_footer.php'; ?>