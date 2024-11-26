<?php
// src/pages/rent.php
include "../session.php" ;
include '../includes/header.php';
require_once '../config/database.php';
?>

<div class="search-container">
    <div class="search-bar">
        <input type="text" name="location" id="location" placeholder="Enter an address, neighborhood, city, or ZIP code" class="search-input" value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">
        <button type="submit" class="search-button" form="search-form">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </button>
    </div>
    
    <form id="search-form" method="GET" action="" class="filters">
        <div class="dropdown">
            <select name="transaction_type" id="transaction_type" class="dropdown-toggle">
                <option value="sale" <?php echo (isset($_GET['transaction_type']) && $_GET['transaction_type'] == 'sale') ? 'selected' : ''; ?>>For Sale</option>
                <option value="rent" <?php echo (!isset($_GET['transaction_type']) || $_GET['transaction_type'] == 'rent') ? 'selected' : ''; ?>>For Rent</option>
            </select>
        </div>

        <div class="dropdown">
            <select name="property_type" id="property_type" class="dropdown-toggle">
                <option value="">Home Type</option>
                <option value="house" <?php echo (isset($_GET['property_type']) && $_GET['property_type'] == 'house') ? 'selected' : ''; ?>>House</option>
                <option value="apartment" <?php echo (isset($_GET['property_type']) && $_GET['property_type'] == 'apartment') ? 'selected' : ''; ?>>Apartment</option>
                <option value="condo" <?php echo (isset($_GET['property_type']) && $_GET['property_type'] == 'condo') ? 'selected' : ''; ?>>Condo</option>
                <option value="land" <?php echo (isset($_GET['property_type']) && $_GET['property_type'] == 'land') ? 'selected' : ''; ?>>Land</option>
            </select>
        </div>

        <div class="dropdown">
            <button type="button" class="dropdown-toggle">Price</button>
            <div class="dropdown-menu">
                <input type="number" name="min_price" id="min_price" placeholder="Min Price" value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">
                <input type="number" name="max_price" id="max_price" placeholder="Max Price" value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
            </div>
        </div>

        <button type="submit" class="search-submit">Search</button>
    </form>
</div>

<div class="properties-grid">
<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_GET)) {
    $where = [];
    $params = [];

    if (!empty($_GET['transaction_type'])) {
        $where[] = "transaction_type = ?";
        $params[] = $_GET['transaction_type'];
    } else {
        // If transaction_type is not set, default to 'rent'
        $where[] = "transaction_type = ?";
        $params[] = 'rent';
    }

    if (!empty($_GET['property_type'])) {
        $where[] = "property_type = ?";
        $params[] = $_GET['property_type'];
    }

    if (!empty($_GET['min_price'])) {
        $where[] = "price >= ?";
        $params[] = $_GET['min_price'];
    }

    if (!empty($_GET['max_price'])) {
        $where[] = "price <= ?";
        $params[] = $_GET['max_price'];
    }

    if (!empty($_GET['location'])) {
        $where[] = "(city LIKE ? OR state LIKE ? OR zip_code LIKE ?)";
        $params[] = "%{$_GET['location']}%";
        $params[] = "%{$_GET['location']}%";
        $params[] = "%{$_GET['location']}%";
    }

    $sql = "SELECT p.*, 
        (SELECT image_url FROM property_images WHERE property_id = p.property_id AND is_primary = 1 LIMIT 1) as image_url 
        FROM properties p";
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            ?>
            <div class="property-card">
                <div class="property-image">
                    <img src="<?php echo htmlspecialchars($row['image_url'] ?? '/placeholder.jpg'); ?>" alt="Property Image">
                </div>
                <div class="property-details">
                    <h3 class="property-price">Php <?php echo number_format($row['price'], 2); ?><?php echo $row['transaction_type'] == 'rent' ? '/mo' : ''; ?></h3>
                    <div class="property-specs">
                        <span><?php echo htmlspecialchars($row['bedrooms']); ?> bd</span>
                        <span><?php echo htmlspecialchars($row['bathrooms']); ?> ba</span>
                        <span><?php echo number_format($row['area_sqft']); ?> sqft</span>
                        <span><?php echo ucfirst(htmlspecialchars($row['property_type'])); ?> for <?php echo ucfirst(htmlspecialchars($row['transaction_type'])); ?></span>
                    </div>
                    <p class="property-address">
                        <?php echo htmlspecialchars($row['address']); ?>, 
                        <?php echo htmlspecialchars($row['city']); ?>, 
                        <?php echo htmlspecialchars($row['state']); ?>
                    </p>
                </div>
            </div>
            <?php
        }

        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
</div>

<div class="pagination">
    <a href="#" class="pagination-arrow">&lt;</a>
    
    <a href="#" class="pagination-arrow">&gt;</a>
</div>

<?php include '../includes/footer.php'; ?>

