<?php

$_body_class = 'register-page';
$_page_title = "Register";

require '../_base.php';
require '../_head.php';

if (is_post()) {

    

    $name = req('name');
    $email = req('email');
    $password = req('password');
    $phone = req('phone');
    $birthdate = req('birthdate');  
    $gender = req('gender');  
    $address = req('address');
    $city = req('city');
    $state = req('state');
    $postcode = req('postcode');

    //Validate name
    if(empty($name)){
        $$_err['name'] = "Required";
    }
    else if (!is_unique($name, 'customer', 'name')) {
        $_err['name'] = "Duplicate Name";
    }
    else if(strlen($name) > 100){
        $_err['name'] = "Maximum length 100";
    }

    //Validate email
    if(empty($email)){
        $_err['email'] = "Required";
    }
    else if (!is_unique($email, 'customer', 'email')) {
        $_err['email'] = "Duplicate Email";
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_err['email'] = "Invalid Email Format";
    }

    //Validate password
    if(empty($password)){
        $_err['password'] = "Required";
    }
    else if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $_err['password'] = "Invalid Password Format";
    }
    else if(strlen($password) > 11){
        $_err['password'] = "Maximum length 11";
    }
    else if($password !== req('confirm_password')){
        $_err['confirm_password'] = "Password Mismatch";
    }

    //Validate phone
    if(empty($phone)){
        $_err['phone'] = "Required";
    }
    else if (!preg_match('/^01[0-9]-?[0-9]{7,8}$/', $phone)) {
        $_err['phone'] = "Invalid Phone Number Format";
    }

    //Validate birthdate
    if(!empty($birthdate)){
        $date_now = date('Y-m-d');
        if ($birthdate >= $date_now) {
            $_err['birthdate'] = "Invalid Birthdate";
        }
    }
    else if(empty($birthdate)){
        $_err['birthdate'] = "Required";
    }

    //Validate gender
    if(empty($gender)){
        $_err['gender'] = "Required";
    }
    else if (!array_key_exists($gender, $_genders)) {
        $_err['gender'] = "Invalid value";
    }

    //Validate address
    if(empty($address)){
        $_err['address'] = "Required";
    }

    //Validate city
    if(empty($city)){
        $_err['city'] = "Required";
    }

    //Validate state
    if(empty($state)){
        $_err['state'] = "Required";
    }

    //Validate postcode
    if(empty($postcode)){
        $_err['postcode'] = "Required";
    }
    else if (!preg_match('/^[0-9]{5}$/', $postcode)) {
        $_err['postcode'] = "Invalid Postcode Format";
    }


}
?>

<div class="container">
    <div class="progress-bar">
        <div class="step active" aria-label="Step 1 of 3: Account Details" aria-current="step">1</div>
        <div class="step" aria-label="Step 2 of 3: Personal Information">2</div>
        <div class="step" aria-label="Step 3 of 3: Shipping Address">3</div>

    </div>
    
    <form id=multi-step-form method="POST" action="register.php">
        
        <!-- Step 1: Account Details -->
        <div class="form-step active">
            <h2> Account Details</h2>

            <label for="name">Full Name *</label>
            <?= html_text('name', 'maxlength="100" ') ?>
            <?= err('name') ?>

            <label for="email">Email Address *</label>
            <?= html_text('email', ) ?>
            <?= err('email') ?>
            
            <label for="password">Password *</label>
            <?= html_text('password', 'type="password" maxlength="11" ') ?>
            <?= err('password') ?>

            <label for="confirm_password">Confirm Password *</label>
            <?= html_text('confirm_password', 'type="password" maxlength="11" ') ?>
            <?= err('confirm_password') ?>

            <button type="button" class="btn-next">Next</button>
         </div>

        <!-- Step 2: Personal Information -->
        <div class="form-step">
            <h2> Personal Information</h2>

            <label for="phone">Phone Number *</label>
            <?= html_text('phone', 'type="tel" placeholder="0123456789"') ?>
            <?= err('phone') ?>

            <label for="birthdate">Date of Birth</label>
            <?= html_text('birthdate', 'type="date"') ?>
            <?= err('birthdate') ?>

            <label>Gender</label>
            <?= html_radios('gender', $_genders) ?>
            <?= err('gender') ?>

            <div class="btn">
                <button type="button" class="btn-prev">Previous</button>
                <button type="button" class="btn-next">Next</button>
            </div>
        </div>

        <!-- Step 3: Shipping Address -->
        <div class="form-step">
            <h2> Shipping Address</h2>

            <label for="address">Street Address *</label>
            <?= html_text('address', '') ?>
            <?= err('address') ?>

            <label for="city">City *</label>
            <?= html_text('city', '') ?>
            <?= err('city') ?>

            <label for="state">State *</label>
            <select>
                <option value="">Select State</option>
                <option>Johor</option>
                <option>Kedah</option>
                <option>Kelantan</option>
                <option>Malacca</option>
                <option>Negeri Sembilan</option>
                <option>Pahang</option>
                <option>Penang</option>
                <option>Perak</option>
                <option>Perlis</option>
                <option>Sabah</option>
                <option>Sarawak</option>
                <option>Selangor</option>
                <option>Terengganu</option>
                <option>Federal Territory of Kuala Lumpur</option>
                <option>Federal Territory of Labuan</option>
                <option>Federal Territory of Putrajaya</option>
            </select>
            <?= err('state') ?>

            <label for="postcode">Postcode *</label>
            <?= html_text('postcode', '') ?>
            <?= err('postcode') ?>

            <div class="btn">
                <button type="button" class="btn-prev">Previous</button>
                <button type="submit">Create Account</button>
            </div>
        </div>
    </form>
</div>
<!-- <?php include '../_footer.php'; ?> -->