<?php 
// normal page

$_page_title = "Home - Bookstore";
$_body_class = "home-page"; // optional, for styling if needed
require '_base.php';    // include core functions, DB connection

include '_head.php';     // include CSS/JS, open body
include '_header.php';   // include top navigation
?>

<main>
    <h2>Home Page</h2>
    <p>Welcome to the bookstore!</p>
    <!-- Add your normal page content here -->
</main>

<?php 
include '_footer.php';  // close body/html
?>
