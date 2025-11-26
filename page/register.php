<?php

$_body_class = 'register-page';
$_page_title = "Register";

require '../_base.php';
require '../_head.php';

if (is_post()) {

    

    $name = req('name');
    $email = req('email');
    $password = req('password');
    var_dump($password);
    $confirm_password = req('confirm_password');
    $phone = req('phone');
    $birthdate = req('birthdate');  
    $gender = req('gender');  
    $address = req('address');
    $city = req('city');
    $state = req('state');
    $postcode = req('postcode');

    //Validate name
    if(empty($name)){
        $_err['name'] = "Required";
    }
    else if(strlen($name) > 100){
        $_err['name'] = "Maximum length 100";
    }
    else if (!is_unique($name, 'customer', 'name')) {
        $_err['name'] = "Duplicate Name";
    }

    //Validate email
    if(empty($email)){
        $_err['email'] = "Required";
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_err['email'] = "Invalid Email Format";
    }
    else if (!is_unique($email, 'customer', 'email')) {
        $_err['email'] = "Duplicate Email";
    }

    //Validate password
    if(empty($password)){
        $_err['password'] = "Required";
    }
    else if(strlen($password) < 8){
        $_err['password'] = "Minimum length 8 characters";
    }
    else if(strlen($password) > 11){
        $_err['password'] = "Maximum length 11";
    }
    elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])/', $password)) {
        $_err['password'] = "Must contain uppercase, lowercase, number and special character (@$!%*?&)";
    }
    //Validate confirm password
    if (empty(req('confirm_password'))) {
        $_err['confirm_password'] = "Required";
    } 
    else if (req('confirm_password') !== $password) {
        $_err['confirm_password'] = "Passwords do not match";
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
    echo "<pre>";
    print_r($_err);
    echo "</pre>";

if (empty($_err)) {
    echo "Validation passed!<br>";
    
    try {
        echo "Starting transaction...<br>";
        $_db->beginTransaction();

        $sql = "INSERT INTO customer 
                (name, email, password, phone, birthdate, gender, created_at) 
                VALUES 
                (:name, :email, :password, :phone, :birthdate, :gender, NOW())";

        $stm = $_db->prepare($sql);
        echo "Prepared first query<br>";
        
        $stm->execute([
            ':name'      => $name,
            ':email'     => $email,
            ':password'  => password_hash($password, PASSWORD_DEFAULT),
            ':phone'     => $phone,
            ':birthdate' => $birthdate ?: null,
            ':gender'    => $gender
        ]);
        echo "Inserted customer record<br>";

        // Get the auto-generated customer ID
        $customer_id = $_db->lastInsertId();
         echo "Customer ID: " . $customer_id . "<br>";

        $sql2 = "INSERT INTO customer_address 
                (customer_id, address, city, state, postcode) 
                VALUES (:customer_id, :address, :city, :state, :postcode)";

        $stm2 = $_db->prepare($sql2);
        echo "Prepared second query<br>";
        
        $stm2->execute([
            ':customer_id' => $customer_id,
            ':address'     => $address,
            ':city'        => $city,
            ':state'       => $state,
            ':postcode'    => $postcode
        ]);
        echo "Inserted address record<br>";

        $_db->commit();
        echo "Transaction committed!<br>";

        $_SESSION['customer_id']   = $customer_id;
        $_SESSION['customer_name'] = $name;

        echo "About to redirect...<br>";
        redirect('index.php');
        
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
            <label>Gender</label>
            <select name="gender" id="gender" required>
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

            <label for="address">Street Address *</label>
            <?= html_text('address', '') ?>
            <?= err('address') ?>

            <label for="city">City *</label>
            <?= html_text('city', '') ?>
            <?= err('city') ?>

            <label for="state">State *</label>
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