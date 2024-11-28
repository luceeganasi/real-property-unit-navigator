<?php
session_start();
include '../config/database.php';

// Check if property ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$property_id = intval($_GET['id']);

// Fetch property details
$stmt = $conn->prepare("SELECT * FROM properties WHERE property_id = ?");
$stmt->bind_param("i", $property_id);
$stmt->execute();
$result = $stmt->get_result();
$property = $result->fetch_assoc();

if (!$property) {
    header("Location: index.php");
    exit();
}

// Fetch property images
$stmt = $conn->prepare("SELECT image_url FROM property_images WHERE property_id = ?");
$stmt->bind_param("i", $property_id);
$stmt->execute();
$result = $stmt->get_result();
$images = $result->fetch_all(MYSQLI_ASSOC);

include '../includes/header.php';
?>
    <div class="property-details-container">
        <h1><?php echo htmlspecialchars($property['title']); ?></h1>
        
        <div class="p-property-images">
            <?php foreach ($images as $image): ?>
                <img src="<?php echo htmlspecialchars($image['image_url']); ?>" alt="Property Image" class="p-property-image">
            <?php endforeach; ?>
        </div>
        
        <div class="property-info">
            <p class="property-price">Price: ₱<?php echo number_format($property['price'], 2); ?></p>
            <p>Address: <?php echo htmlspecialchars($property['address']); ?>, <?php echo htmlspecialchars($property['city']); ?>, <?php echo htmlspecialchars($property['state']); ?> <?php echo htmlspecialchars($property['zip_code']); ?></p>
            <p>Bedrooms: <?php echo htmlspecialchars($property['bedrooms']); ?></p>
            <p>Bathrooms: <?php echo htmlspecialchars($property['bathrooms']); ?></p>
            <p>Area: <?php echo number_format($property['area_sqft']); ?> sqft</p>
            <p>Property Type: <?php echo htmlspecialchars($property['property_type']); ?></p>
            <p>Transaction Type: <?php echo htmlspecialchars($property['transaction_type']); ?></p>
            <?php if ($property['transaction_type'] === 'sale'): ?>
                <p>Down Payment: ₱<?php echo number_format($property['down_payment'], 2); ?></p>
            <?php else: ?>
                <p>Monthly Payment: ₱<?php echo number_format($property['monthly_payment'], 2); ?></p>
            <?php endif; ?>
        </div>
        
        <div class="property-description">
            <h2>Description</h2>
            <p><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
        </div>
        
        <div id="property-map" style="height: 400px; width: 100%;"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var map = L.map('property-map').setView([<?php echo $property['latitude']; ?>, <?php echo $property['longitude']; ?>], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            L.marker([<?php echo $property['latitude']; ?>, <?php echo $property['longitude']; ?>])
                .addTo(map)
                .bindPopup("<?php echo htmlspecialchars($property['title']); ?>");
        });
    </script>
<?php include '../includes/footer.php'; ?>