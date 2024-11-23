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

           
            
        
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="features-content">
            <!-- Buy -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="M21 21l-4.35-4.35"/>
                    </svg>
                </div>
                <h3>Buy a home</h3>
                <p>Find your place with an immersive photo experience and the most listings, including things you won't find anywhere else.</p>
                <a href="/pages/buy.php" class="feature-link">Browse homes</a>
            </div>

            <!-- Sell -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 12h18M3 6h18M3 18h18"/>
                    </svg>
                </div>
                <h3>Sell a home</h3>
                <p>No matter what path you take to sell your home, we can help you navigate a successful sale.</p>
                <a href="/pages/sell.php" class="feature-link">See your options</a>
            </div>

            <!-- Rent -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        <path d="M9 22V12h6v10"/>
                    </svg>
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

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Resources</h3>
                <ul>
                    <li><a href="#">Real estate guides</a></li>
                    <li><a href="#">House price index</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>About us</h3>
                <ul>
                    <li><a href="#">Meet the team</a></li>
                    <li><a href="#">Contact us</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Follow us</h3>
                <div class="social-icons">
                    <a href="#" class="social-icon">
                        <svg viewBox="0 0 24 24"><path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/></svg>
                    </a>
                    <a href="#" class="social-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <a href="#" class="social-icon">
                        <svg viewBox="0 0 24 24"><path d="M23.954 4.569c-.885.389-1.83.654-2.825.775 1.014-.611 1.794-1.574 2.163-2.723-.951.555-2.005.959-3.127 1.184-.896-.959-2.173-1.559-3.591-1.559-2.717 0-4.92 2.203-4.92 4.917 0 .39.045.765.127 1.124C7.691 8.094 4.066 6.13 1.64 3.161c-.427.722-.666 1.561-.666 2.475 0 1.71.87 3.213 2.188 4.096-.807-.026-1.566-.248-2.228-.616v.061c0 2.385 1.693 4.374 3.946 4.827-.413.111-.849.171-1.296.171-.314 0-.615-.03-.916-.086.631 1.953 2.445 3.377 4.604 3.417-1.68 1.319-3.809 2.105-6.102 2.105-.39 0-.779-.023-1.17-.067 2.189 1.394 4.768 2.209 7.557 2.209 9.054 0 13.999-7.496 13.999-13.986 0-.209 0-.42-.015-.63.961-.689 1.8-1.56 2.46-2.548l-.047-.02z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</main>

<?php include './includes/footer.php'; ?>