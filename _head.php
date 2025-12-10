<?php
// head.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_page_title ?? 'Bookstore' ?></title>
    <link rel="shortcut icon" href="images/book.png" type="image/x-icon">
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>    
    <link href='https://cdn.boxicons.com/3.0.5/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <script src="/js/app.js"></script>
</head>
<body class="<?= $_body_class ?? '' ?>">


