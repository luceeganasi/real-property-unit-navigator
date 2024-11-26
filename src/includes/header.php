<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPU Real Estate</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="header">
        <nav class="nav-container">
            <div class="nav-left">
                <a href="/pages/buy.php" class="nav-link">Buy</a>
                <a href="/pages/rent.php" class="nav-link">Rent</a>
                <a href="/pages/sell.php" class="nav-link">Sell</a>
            </div>
            
            <div class="nav-center">
                <a href="/">RPU</a>
            </div>
            
            <div class="nav-right">
                <a href="/pages/help.php" class="nav-link">Help</a>
                <?php if ($isLoggedIn): ?>
                    <a href="/pages/bookmarks.php" class="nav-link">Bookmarks</a>
                    <a href="/pages/profile.php" class="nav-link">Profile</a>
                <?php else: ?>
                    <a href="/pages/login.php" class="nav-link">Log in</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main>

