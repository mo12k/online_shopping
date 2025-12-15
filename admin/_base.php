<?php

// ============================================================================
// PHP Setups
// ============================================================================

date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();

// ============================================================================
// General Page Functions
// ============================================================================

// Is GET request?
function is_get() {
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

// Is POST request?
function is_post() {
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

// Obtain GET parameter
function get($key, $value = null) {
    $value = $_GET[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain POST parameter
function post($key, $value = null) {
    $value = $_POST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain REQUEST (GET and POST) parameter
function req($key, $value = null) {
    $value = $_REQUEST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Redirect to URL
function redirect($url = null) {
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit();
}

// Set or get temporary session variable
function temp($key, $value = null) {
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    }
    else {
        $value = $_SESSION["temp_$key"] ?? null;
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}

// ============================================================================
// HTML Helpers
// ============================================================================

// Encode HTML special characters
function encode($value) {
    return htmlentities($value);
}

function html_textarea($key, $attr = '') {
$value = encode($GLOBALS[$key] ?? '');
echo "<textarea id='$key' name='$key' $attr>$value</textarea>";
}

// Generate <input type='text'>
function html_text($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='text' id='$key' name='$key' value='$value' $attr>";
}


// Generate <input type='number'>
function html_number($key, $min = '', $max = '', $step = '', $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='number' id='$key' name='$key' value='$value'
                min='$min' max='$max' step='$step' $attr>";
}
// Generate <input type='search'>
function html_search($key, $placeholder ,$attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    $ph = $placeholder ? "placeholder='$placeholder'" : '';
    echo "<input type='search' id='$key' name='$key' value='$value' $ph $attr>";
}

// Generate <input type='radio'> list
function html_radios($key, $items, $br = false) {
    $value = encode($GLOBALS[$key] ?? '');
    echo '<div>';
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'checked' : '';
        echo "<label><input type='radio' id='{$key}_$id' name='$key' value='$id' $state>$text</label>";
        if ($br) {
            echo '<br>';
        }
    }
    echo '</div>';
}

// Generate <select>
function html_select($key, $items, $default = '- Select One -', $attr = '', $auto_submit =false) {
    $value = encode($GLOBALS[$key] ?? '');
    $onchange = $auto_submit ?'onchange ="this.form.submit()"' :'';
    

    echo "<select id='$key' name='$key' $attr $onchange>";
    if ($default !== null) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $state = ($id.'') ==($value.'')  ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}

// Generate <input type='file'>
function html_file($key, $accept = '', $attr = '') {
    echo "<input type='file' id='$key' name='$key' accept='$accept' $attr>";
}

function get_file($key) {
    $f = $_FILES[$key] ?? null;
    
    if ($f && $f['error'] == 0) {
        return (object)$f;
    }

    return null;
}

// Crop, resize and save photo
function save_photo($f, $folder) {
    $photo = uniqid() . '.jpg';
    move_uploaded_file($f->tmp_name, "$folder/$photo");

    return $photo;
}

    function html_status_toggle($key, $default = 1) {
    $checked = ($GLOBALS[$key] ?? $default) == 1 ? 'checked' : '';
    echo <<<HTML
    <div style="display:flex; align-items:center; gap:30px; margin:12px 0; font-size:15px; user-select:none;">
        <span style="color:#999;">Draft</span>
        <label class="toggle-switch">
            <input type="checkbox" name="$key" value="1" $checked>
            <span class="slider round"></span>
        </label>
        <span style="color:#333; font-weight:500;">Publish</span>
    </div>
    HTML;
}


// Is money?
function is_money($value) {
    return preg_match('/^\-?\d+(\.\d{1,2})?$/', $value);
}



function table_headers($fields, $sort, $dir, $href = '') {
    foreach ($fields as $k => $v) {
        $d = 'asc'; // Default direction
        $c = '';    // Default class
        
        // TODO
        if($k ==$sort){
            $d = $dir =='asc'?'desc':'asc';
            $c = $dir;

        }

        echo "<th><a href='?sort=$k&dir=$d&$href' class='$c'>$v</a></th>";
    }
}

// Generate <input type='password'>
function html_password($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='password' id='$key' name='$key' value='$value' $attr>";
}


// ============================================================================
// Error Handlings
// ============================================================================

// Global error array
$_err = [];

// Generate <span class='err'>
function err($key) {
    global $_err;
    if ($_err[$key] ?? false) {
        echo "<span class='err'>$_err[$key]</span>";
    }
    else {
        echo '<span></span>';
    }
}



// ============================================================================
// Database Setups and Functions
// ============================================================================


// Global PDO object
$_db = new PDO('mysql:dbname=bookstore', 'root', '', [    
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
]);

// Is unique?
function is_unique($value, $table, $field) {
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() == 0;
}

// Is exists?
function is_exists($value, $table, $field) {
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() > 0;
}

function verify_credentials($username, $password) {
    global $_db;
    
    $hased_password = SHA1($password);
    $stm = $_db->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
    $stm->execute([$username]);
    $user = $stm->fetch();

    if ($user && $hased_password === $user->password) {
        return $user;
    }
    return null;
}

function root($path = '') {
    return "$_SERVER[DOCUMENT_ROOT]/$path";
}


function base($path = '') {
    return "http://$_SERVER[SERVER_NAME]:$_SERVER[SERVER_PORT]/$path";
}

function get_mail() {
    require_once 'lib/PHPMailer.php';
    require_once 'lib/SMTP.php';

    $m = new PHPMailer(true);
    $m->isSMTP();
    $m->SMTPAuth = true;
    $m->Host = 'smtp.gmail.com';
    $m->Port = 587;
    $m->Username = 'triedexample@gmail.com';
    $m->Password = 'lyin euay ljcg yope';
    $m->CharSet = 'utf-8';
    
    return $m;
}

// ============================================================================
// Global Constants and Variables
// ============================================================================

$_genders = [
    'F' => 'Female',
    'M' => 'Male',
];

    $_category = $_db->query('SELECT category_id, category_name FROM category')
                 ->fetchAll(PDO::FETCH_KEY_PAIR);

$_status = [1 => 'Published', 0 => 'Draft'];


// ============================================================================
// authentication check
// ============================================================================
function admin_require_login() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: /admin/page/adminlogin.php");
        exit;
    }
}


    
   