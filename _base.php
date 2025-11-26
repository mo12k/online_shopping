<?php

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

  // Generate <input type='text'>
function html_text($name, $attr = '') {
    $value = $_POST[$name] ?? '';
    $value = htmlspecialchars($value, ENT_QUOTES);
    echo "<input type=\"text\" id=\"$name\" name=\"$name\" value=\"$value\" $attr>";
}

function html_password($name, $attr = '') {
    echo "<input type=\"password\" id=\"$name\" name=\"$name\" $attr>";
}

// Generate <select>
function html_select($key, $items, $default = '- Select One -', $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<select id='$key' name='$key' $attr>";
    if ($default !== null) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}

// ============================================================================
// Error Handlings
// ============================================================================

// Global error array
$_err = [];

// Generate <span class='err'>
function err($key) {
    global $_err;
    if($_err[$key] ?? false) {
        echo "<span class='err' >$_err[$key]</span>";
    }
    else{
        echo '<span></span>';
    }
}

//Global PDO object
$_db = new PDO('mysql:dbname=bookstore', 'root', '', [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
]);

//Is unique?
function is_unique($value, $table, $field) {
    global $_db;
    $stm = $_db->prepare("SELECT 1 FROM $table WHERE $field = ? LIMIT 1");
    $stm->execute([$value]);
    return $stm->rowCount() === 0;
}

function get_next_id(string $table): int
{
    global $_db;

    try {
        // Get current highest ID + 1 without locking
        // The transaction in the calling code will handle concurrency
        $sql = "SELECT IFNULL(MAX(id), 0) + 1 as next_id FROM $table";
        $result = $_db->query($sql)->fetch(PDO::FETCH_ASSOC);
        
        if (!$result || !isset($result['next_id'])) {
            throw new Exception("Failed to get next ID");
        }
        
        return (int)$result['next_id'];

    } catch (Exception $e) {
        error_log("get_next_id($table) failed: " . $e->getMessage());
        throw new Exception("Cannot generate ID. Please try again.");
    }
}

// ============================================================================
// Global Constants and Variables
// ============================================================================

$_genders = [
    'F' => 'Female',
    'M' => 'Male',
];

