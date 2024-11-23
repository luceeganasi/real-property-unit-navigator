<?php
// src/pages/index.php
include './includes/header.php';
require_once './config/database.php';
?>

<main class="main-content">
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-image-container">
            <img src="./assets/images/house-hero1.jpg" alt="Beautiful house with white picket fence" class="hero-image">
            <div class="hero-overlay"></div>
        </div>
        
        <div class="hero-content">
            <h1 class="hero-title">Buy. Rent. Sell. Homes</h1>
            
            <form action="/pages/search.php" method="GET" class="search-form">
                <input 
                    type="text" 
                    name="location" 
                    placeholder="Enter an address, neighborhood, city, or ZIP code" 
                    class="search-input"
                >
                <button type="submit" class="search-button">Search</button>
            </form>
        </div>
    </section>

    <!-- Recommendations Section -->
    <section class="recommendations">
        <div class="recommendations-content">
            <div class="recommendations-text">
                <h2>Get home recommendations</h2>
                <p>Sign in for a more personalized experience.</p>
                <a href="/pages/signup.php" class="sign-in-button">Sign In</a>
            </div>
            <div class="featured-image-container">
                <img class="responsive-image" src="./assets/images/card.png" alt="house for sale card">
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="features-content">
            <!-- Buy -->
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="./assets/images/buy.png" alt="someone looking at magnifying glass">
                </div>
                <h3>Buy a home</h3>
                <p>Find your place with an immersive photo experience and the most listings, including things you won't find anywhere else.</p>
                <a href="/pages/buy.php" class="feature-link">Browse homes</a>
            </div>

            <!-- Sell -->
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="./assets/images/sell.png" alt="house"> 
                </div>
                <h3>Sell a home</h3>
                <p>No matter what path you take to sell your home, we can help you navigate a successful sale.</p>
                <a href="/pages/sell.php" class="feature-link">See your options</a>
            </div>

            <!-- Rent -->
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="./assets/images/rent.png" alt="two person talking at the window">
                </div>
                <h3>Rent a home</h3>
                <p>We're creating a seamless online experience – from shopping on the largest rental network, to applying, to paying rent.</p>
                <a href="/pages/rent.php" class="feature-link">Find rentals</a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about">
        <h2>About RPU's Recommendations</h2>
        <p>
            Recommendations are based on your location and search activity, such as the homes you've viewed and saved and the 
            filters you've used. We use this information to bring similar homes to your attention, so you don't miss out.
        </p>
    </section>

<?php include './includes/footer.php'; ?>