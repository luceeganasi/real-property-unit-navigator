<?php
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPU Real Estate</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
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
        <script>
        function initMap(mapId, properties) {
            const map = L.map(mapId).setView([14.5995, 120.9842], 10); // Default view centered on Manila

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            properties.forEach(property => {
                if (property.latitude && property.longitude) {
                    L.marker([property.latitude, property.longitude])
                        .addTo(map)
                        .bindPopup(`
                            <strong>${property.title}</strong><br>
                            Price: ₱${property.price.toLocaleString()}<br>
                            <a href="/pages/property.php?id=${property.property_id}">View Details</a>
                        `);
                }
            });

            return map;
        }
        </script>

