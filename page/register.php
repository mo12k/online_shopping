<?php

$_body_class = 'register-page';
$_page_title = "Register";

require '../_base.php';
include '../_head.php';

if (is_post()) {

    $username = req('username');
    $email = req('email');
    $password = req('password');
    $confirm_password = req('confirm_password');
    $phone = req('phone');
    $birthdate = req('birthdate');  
    $gender = req('gender');  
    $address = req('address');
    $city = req('city');
    $state = req('state');
    $postcode = req('postcode');

    //Validate username
    if(!$username){
        $_err['username'] = "Required";
    }
    else if(strlen($username) > 100){
        $_err['username'] = "Maximum length 100";
    }
    else if (!is_unique($username, 'customer', 'username')) {
        $_err['username'] = "Duplicate Username";
    }

    //Validate email
    if(!$email){
        $_err['email'] = "Required";
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_err['email'] = "Invalid Email Format";
    }
    else if (!is_unique($email, 'customer', 'email')) {
        $_err['email'] = "Duplicate Email";
    }

    //Validate password
    if(!$password){
        $_err['password'] = "Required";
    }
    else if(strlen($password) < 8){
        $_err['password'] = "Minimum length 8 characters";
    }
    else if(strlen($password) > 11){
        $_err['password'] = "Maximum length 11";
    }
    elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9])/', $password)) {
        $_err['password'] = "Must contain uppercase, lowercase, number and special character";
    }

    //Validate confirm password
    if (!$confirm_password) {
        $_err['confirm_password'] = "Required";
    } 
    else if ($confirm_password !== $password) {
        $_err['confirm_password'] = "Passwords do not match";
    }

    //Validate phone
    if(!$phone){
        $_err['phone'] = "Required";
    }
    else if (!preg_match('/^01[0-9]-?[0-9]{7,8}$/', $phone)) {
        $_err['phone'] = "Invalid Phone Number Format";
    }
    else if (!is_unique($phone, 'customer', 'phone')) {
        $_err['phone'] = "Duplicate Phone Number";
    }

    //Validate birthdate
    if($birthdate){
        $date_now = date('Y-m-d');
        if ($birthdate >= $date_now) {
            $_err['birthdate'] = "Invalid Birthdate";
        }
    }
    else if(!$birthdate){
        $_err['birthdate'] = "Required";
    }

    //Validate gender
    if(!$gender){
        $_err['gender'] = "Required";
    }
    else if (!array_key_exists($gender, $_genders)) {
        $_err['gender'] = "Invalid value";
    }
    
if (!$_err) {
    
    try {
        echo "Starting transaction...<br>";
        $_db->beginTransaction();

        $sql = "INSERT INTO customer 
                (username, email, password, phone, birthdate, gender, created_at, photo) 
                VALUES 
                (:username, :email, :password, :phone, :birthdate, :gender, NOW(), :photo)";

        $stm = $_db->prepare($sql);
        
        $stm->execute([
            ':username'  => $username,
            ':email'     => $email,
            ':password'  => password_hash($password, PASSWORD_DEFAULT),
            ':phone'     => $phone,
            ':birthdate' => $birthdate ?: null,
            ':gender'    => $gender,
            ':photo'     => 'default_pic.jpg'
        ]);

        // Get the auto-generated customer ID
        $customer_id = $_db->lastInsertId();

        $sql2 = "INSERT INTO customer_address 
                (customer_id, address, city, state, postcode) 
                VALUES (:customer_id, :address, :city, :state, :postcode)";

        $stm2 = $_db->prepare($sql2);
        
        $stm2->execute([
            ':customer_id' => $customer_id,
            ':address'     => $address,
            ':city'        => $city,
            ':state'       => $state,
            ':postcode'    => $postcode
        ]);

        $_db->commit();

        $_SESSION['customer_id']   = $customer_id;
        $_SESSION['customer_username'] = $username;

        redirect('/');
        
    } catch (Exception $e) {
        echo "ERROR: " . $e->getMessage() . "<br>";
        if ($_db->inTransaction()) {
            $_db->rollBack();
        }
        $_err['db'] = "Registration failed. Please try again.";
    }
}
}
?>

<div class="container-register">
    <div class="progress-bar">
        <div class="step active" aria-label="Step 1 of 3: Account Details" aria-current="step">1</div>
        <div class="step" aria-label="Step 2 of 3: Personal Information">2</div>
        <div class="step" aria-label="Step 3 of 3: Shipping Address">3</div>

    </div>
    
    <form id=multi-step-form method="POST" action="register.php">
        
        <!-- Step 1: Account Details -->
        <div class="form-step active">
            <h2> Account Details</h2>

            <label for="username">Username *</label>
            <?= html_text('username', 'maxlength="100" ') ?>
            <?= err('username') ?>

            <label for="email">Email Address *</label>
            <?= html_text('email', 'placeholder="example@example.com"') ?>
            <?= err('email') ?>
            
            <label for="password">Password *</label>
            <input type ='password' name='password' id='password' maxlength="11"> 
            <?= err('password') ?>
            <div class="password-rules">
                <p class="rule" id="rule-length">Minimum 8 characters</p>
                <p class="rule" id="rule-upper">At least one uppercase letter</p>
                <p class="rule" id="rule-lower">At least one lowercase letter</p>
                <p class="rule" id="rule-number">At least one number</p>
                <p class="rule" id="rule-special">At least one special character</p>
            </div>

            <label for="confirm_password">Confirm Password *</label>
            <input type="password" name="confirm_password" id="confirm_password">
            <?= err('confirm_password') ?>

            <button type="button" class="btn-next">Next</button>

            <div class="already-account">
                Already have an account? 
                <a href="login.php" class="login-link">Log in here</a>
            </div>

         </div>

        <!-- Step 2: Personal Information -->
        <div class="form-step">
            <h2> Personal Information</h2>

            <label for="phone">Phone Number *</label>
            <?= html_text('phone', 'type="tel" placeholder="0123456789"') ?>
            <?= err('phone') ?>

            <label for="birthdate">Date of Birth *</label>
            <input type="date" 
                id="birthdate" 
                name="birthdate" 
                value="<?= htmlspecialchars($_POST['birthdate'] ?? '') ?>"
                max="<?= date('Y-m-d') ?>">
            <?= err('birthdate') ?>

            <label>Gender</label>
            <select name="gender" id="gender" >
                <option value="">Select Gender</option>
                <?php foreach ($_genders as $value => $label): ?>
                    <option value="<?= $value ?>" <?= ($gender ?? '') == $value ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
            <?= err('gender') ?>

            <div class="btn">
                <button type="button" class="btn-prev">Previous</button>
                <button type="button" class="btn-next">Next</button>
            </div>
        </div>

        <!-- Step 3: Shipping Address -->
        <div class="form-step">
            <h2> Shipping Address</h2>

            <label for="address">Street Address </label>
            <?= html_text('address', 'placeholder="No. 12, Jalan ABC"') ?>

            <label for="city">City </label>
            <?= html_text('city', 'placeholder="Kuala Lumpur"') ?>

            <label for="state">State </label>
            <select name="state" id="state">
                <option value="">- Select State -</option>
                <?php 
                $states = [
                    'Johor' => 'Johor',
                    'Kedah' => 'Kedah',
                    'Kelantan' => 'Kelantan',
                    'Malacca' => 'Malacca',
                    'Negeri Sembilan' => 'Negeri Sembilan',
                    'Pahang' => 'Pahang',
                    'Penang' => 'Penang',
                    'Perak' => 'Perak',
                    'Perlis' => 'Perlis',
                    'Sabah' => 'Sabah',
                    'Sarawak' => 'Sarawak',
                    'Selangor' => 'Selangor',
                    'Terengganu' => 'Terengganu',
                    'Kuala Lumpur' => 'Federal Territory of Kuala Lumpur',
                    'Labuan' => 'Federal Territory of Labuan',
                    'Putrajaya' => 'Federal Territory of Putrajaya'
                ];
                $selected = $_POST['state'] ?? '';
                foreach ($states as $value => $label): ?>
                    <option value="<?= $value ?>" <?= $selected === $value ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="postcode">Postcode </label>
            <?= html_text('postcode', 'placeholder="50050" maxlength="5"') ?>            
            <?= err('postcode') ?>

            <div class="btn">
                <button type="button" class="btn-prev">Previous</button>
                <button type="submit">Create Account</button>
            </div>
        </div>
    </form>
</div>
<!-- <?php include '../_footer.php'; ?> -->
