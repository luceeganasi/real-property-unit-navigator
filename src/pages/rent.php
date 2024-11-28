<?php
session_start();
include '../config/database.php';

// Pagination
$items_per_page = 12;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $items_per_page;

// Base query for fetching properties
$query = "SELECT p.*, 
    (SELECT image_url FROM property_images WHERE property_id = p.property_id AND is_primary = 0 LIMIT 1) as image_url 
    FROM properties p 
    WHERE transaction_type = 'rent'";

$params = [];
$types = "";

// Search functionality
if (!empty($_GET['search'])) {
    $search = "%{$_GET['search']}%";
    $query .= " AND (p.title LIKE ? OR p.address LIKE ? OR p.city LIKE ? OR p.state LIKE ? OR p.zip_code LIKE ?)";
    $params = array_merge($params, [$search, $search, $search, $search, $search]);
    $types .= "sssss";
}

// Filter functionality
if (!empty($_GET['home_type'])) {
    $query .= " AND p.property_type = ?";
    $params[] = $_GET['home_type'];
    $types .= "s";
}

if (!empty($_GET['price_range'])) {
    list($min_price, $max_price) = explode('-', $_GET['price_range']);
    $query .= " AND p.price >= ? AND p.price <= ?";
    $params[] = $min_price;
    $params[] = $max_price;
    $types .= "dd";
}

// Build the count query separately
$count_query = "SELECT COUNT(*) as total FROM properties p WHERE transaction_type = 'rent'";

// Reuse dynamic filters for count query
if (!empty($_GET['search'])) {
    $count_query .= " AND (p.title LIKE ? OR p.address LIKE ? OR p.city LIKE ? OR p.state LIKE ? OR p.zip_code LIKE ?)";
}

if (!empty($_GET['home_type'])) {
    $count_query .= " AND p.property_type = ?";
}

if (!empty($_GET['price_range'])) {
    $count_query .= " AND p.price >= ? AND p.price <= ?";
}

// Prepare and execute the count query
$stmt = $conn->prepare($count_query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row && isset($row['total'])) {
    $total_results = (int)$row['total'];
} else {
    // Handle the error - log it and set a default value
    echo "Unable to fetch total results from the database";
    error_log("Error: Unable to fetch total results from the database.");
    $total_results = 0;
}

$total_pages = ($total_results > 0) ? ceil($total_results / $items_per_page) : 1;

// Add pagination to the main query
$query .= " LIMIT ? OFFSET ?";
$params[] = $items_per_page;
$params[] = $offset;
$types .= "ii";

// Execute the main query
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$properties = $result->fetch_all(MYSQLI_ASSOC);

// Check if we have results
if (empty($properties)) {
    echo "<p>No properties found matching your criteria.</p>";
}

include '../includes/header.php';
?>

<div class="main-content">
    <div class="search-container">
        <form action="" method="GET" class="search-for">
            <div class="search-bar">
                <input type="text" name="search" placeholder="Enter an address, neighborhood, city, or ZIP code" class="search-input-buy-rent" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="search-button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>
            </div>
            
            <div class="filters">
                <div class="dropdown">
                    <select name="home_type" class="dropdown-toggle">
                        <option value="">Home Type</option>
                        <option value="house" <?php echo (isset($_GET['home_type']) && $_GET['home_type'] == 'house') ? 'selected' : ''; ?>>House</option>
                        <option value="apartment" <?php echo (isset($_GET['home_type']) && $_GET['home_type'] == 'apartment') ? 'selected' : ''; ?>>Apartment</option>
                        <option value="condo" <?php echo (isset($_GET['home_type']) && $_GET['home_type'] == 'condo') ? 'selected' : ''; ?>>Condo</option>
                        <option value="land" <?php echo (isset($_GET['home_type']) && $_GET['home_type'] == 'land') ? 'selected' : ''; ?>>Land</option>
                    </select>
                </div>

                <div class="dropdown">
                    <select name="price_range" class="dropdown-toggle">
                        <option value="">Price Range</option>
                        <option value="0-100000" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == '0-100000') ? 'selected' : ''; ?>>₱0 - ₱100,000</option>
                        <option value="100000-500000" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == '100000-500000') ? 'selected' : ''; ?>>₱100,000 - ₱500,000</option>
                        <option value="500000-1000000" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == '500000-1000000') ? 'selected' : ''; ?>>₱500,000 - ₱1,000,000</option>
                        <option value="1000000-5000000" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == '1000000-5000000') ? 'selected' : ''; ?>>₱1,000,000 - ₱5,000,000</option>
                        <option value="5000000-10000000" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == '5000000-10000000') ? 'selected' : ''; ?>>₱5,000,000 - ₱10,000,000</option>
                        <option value="10000000-100000000" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == '10000000-100000000') ? 'selected' : ''; ?>>₱10,000,000+</option>
                    </select>
                </div>

                <button type="submit" class="filter-button">Apply Filters</button>
            </div>
        </form>
    </div>

    <div class="map-container">
        <h2>Rental Property Locations</h2>
        <div id="property-map"></div>
    </div>

    <div class="properties-grid">
        <?php foreach ($properties as $property): ?>
            <div class="property-card">
                <div class="property-image">
                    <img src="<?php echo htmlspecialchars($property['image_url'] ?? '/placeholder.jpg'); ?>" alt="Property Image">
                </div>
                <div class="property-details">
                    <h3 class="property-price">₱<?php echo number_format($property['price'], 2); ?>/month</h3>
                    <div class="property-specs">
                        <span><?php echo htmlspecialchars($property['bedrooms']); ?> bd</span>
                        <span><?php echo htmlspecialchars($property['bathrooms']); ?> ba</span>
                        <span><?php echo number_format($property['area_sqft']); ?> sqft</span>
                        <span>For Rent</span>
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
    
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="pagination-arrow">
                &laquo;
            </a>
        <?php else: ?>
            <span class="pagination-arrow disabled">&laquo;</span>
        <?php endif; ?>

        <span class="pagination-info">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>

        <?php if ($page < $total_pages): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="pagination-arrow">
                &raquo;
            </a>
        <?php else: ?>
            <span class="pagination-arrow disabled">&raquo;</span>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('property-map').setView([14.5995, 120.9842], 10); // Default view centered on Manila

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    <?php foreach ($properties as $property): ?>
        <?php if (!empty($property['latitude']) && !empty($property['longitude'])): ?>
            L.marker([<?php echo $property['latitude']; ?>, <?php echo $property['longitude']; ?>])
                .addTo(map)
                .bindPopup(`
                    <strong><?php echo htmlspecialchars($property['title']); ?></strong><br>
                    Price: ₱<?php echo number_format($property['price'], 2); ?>/month<br>
                    <a href="/pages/property.php?id=<?php echo $property['property_id']; ?>">View Details</a>
                `);
        <?php endif; ?>
    <?php endforeach; ?>
});
</script>

<?php include '../includes/footer.php'; ?>

