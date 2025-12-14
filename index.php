<?php 
// normal page

$_page_title = "Home - Bookstore";
$_body_class = "home-page"; // optional, for styling if needed
require '_base.php';    // include core functions, DB connection
include '_head.php';     // include CSS/JS, open body
include '_header.php';   // include top navigation
?>

<main>
    <div class="home-hero" style="background-image: url('images/home_background.jpg'); background-size: cover; background-position: center; min-height: 500px; display: flex; align-items: center; justify-content: center; flex-direction: column; color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.7);">
        <h1 style="font-size: 3rem; margin-bottom: 20px;">Welcome to Our Bookstore!</h1>
        <p style="font-size: 1.5rem; margin-bottom: 30px;">Discover Amazing Books</p>
        <a href="customer/page/product.php" class="btn-shopping" style="background-color: #007bff; color: white; padding: 15px 40px; text-decoration: none; font-size: 1.2rem; border-radius: 5px; transition: background-color 0.3s; display: inline-block;">
            Shopping
        </a>
    </div>
</main>

<?php 
include '_footer.php';  // close body/html
?>
