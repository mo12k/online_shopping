<?php
// Normal page
$_page_title = "Home - Bookstore";

require '_base.php';
include '_head.php';
include '_header.php';
?>

<main>

    <!-- HERO SECTION -->
    <section
        style="background-image: url('images/home_background.jpg');
               background-size: cover;
               background-position: center;
               min-height: 500px;
               display: flex;
               align-items: center;
               justify-content: center;
               flex-direction: column;
               color: white;
               text-align: center;
               text-shadow: 2px 2px 4px rgba(0,0,0,0.7);">

        <h1 style="font-size: 3rem; margin-bottom: 20px;">
            Welcome to PaperNest Bookstore!
        </h1>

        <p style="font-size: 1.5rem;">
            Discover Amazing Books
        </p>
    </section>

    <!-- ABOUT SECTION -->
    <section style="padding: 60px 20px; text-align: center;">
        <h2 style="font-size: 2rem; margin-bottom: 20px;">
            About Our Bookstore
        </h2>

        <p style="max-width: 700px; margin: auto; font-size: 1.1rem;">
            Our bookstore offers a wide selection of books ranging from fiction,
            non-fiction, educational materials, and reference books.
            We aim to provide readers with a comfortable and enjoyable shopping experience.
        </p>
    </section>

</main>

<?php
include '_footer.php';
?>
