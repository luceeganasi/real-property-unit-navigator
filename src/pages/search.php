<?php
// src/pages/search.php
session_start();
include '../config/database.php';
include '../includes/header.php';

// Retrieve search term from query parameters
$searchTerm = isset($_GET['location']) ? trim($_GET['location']) : '';

// Initialize query
$query = "SELECT p.*, 
    (SELECT image_url FROM property_images WHERE property_id = p.property_id AND is_primary = 0 LIMIT 1) as image_url 
    FROM properties p 
    WHERE (p.address LIKE ? OR p.city LIKE ? OR p.state LIKE ? OR p.zip_code LIKE ? OR p.title LIKE ?)
    AND (transaction_type = 'sale' OR transaction_type = 'rent')";

$params = ["%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%"];
$types = "sssss";

// Prepare and execute the query
$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$properties = $result->fetch_all(MYSQLI_ASSOC);
?>

<main class="main-content">
    <section class="search-results">
        <h1>Search Results</h1>
        <?php if (empty($properties)): ?>
            <p>No properties found matching your criteria.</p>
        <?php else: ?>
            <div class="properties-grid">
                <?php foreach ($properties as $property): ?>
                    <div class="property-card">
                        <div class="property-image">
                            <img src="<?php echo htmlspecialchars($property['image_url'] ?? '/assets/images/placeholder.jpg'); ?>" alt="Property Image">
                        </div>
                        <div class="property-details">
                            <h3 class="property-price">₱<?php echo number_format($property['price'], 2); ?></h3>
                            <div class="property-specs">
                                <span><?php echo htmlspecialchars($property['bedrooms']); ?> bd</span>
                                <span><?php echo htmlspecialchars($property['bathrooms']); ?> ba</span>
                                <span><?php echo number_format($property['area_sqft']); ?> sqft</span>
                                <span>For <?php echo ucfirst($property['transaction_type']); ?></span>
                            </div>
                            <p class="property-address">
                                <?php echo htmlspecialchars($property['address']); ?>, 
                                <?php echo htmlspecialchars($property['city']); ?>, 
                                <?php echo htmlspecialchars($property['state']); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
