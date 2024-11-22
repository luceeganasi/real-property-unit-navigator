<?php
// src/pages/rent.php
include '../includes/header.php';
require_once '../config/database.php';
?>

<h1>Rent a Property</h1>

<form method="GET" action="">
    <label for="transaction_type">Transaction Type:</label>
    <select name="transaction_type" id="transaction_type">
        <option value="sale" <?php echo (isset($_GET['transaction_type']) && $_GET['transaction_type'] == 'sale') ? 'selected' : ''; ?>>For Sale</option>
        <option value="rent" <?php echo (!isset($_GET['transaction_type']) || $_GET['transaction_type'] == 'rent') ? 'selected' : ''; ?>>For Rent</option>
    </select>

    <label for="property_type">Property Type:</label>
    <select name="property_type" id="property_type">
        <option value="house" <?php echo (isset($_GET['property_type']) && $_GET['property_type'] == 'house') ? 'selected' : ''; ?>>House</option>
        <option value="apartment" <?php echo (isset($_GET['property_type']) && $_GET['property_type'] == 'apartment') ? 'selected' : ''; ?>>Apartment</option>
        <option value="condo" <?php echo (isset($_GET['property_type']) && $_GET['property_type'] == 'condo') ? 'selected' : ''; ?>>Condo</option>
        <option value="land" <?php echo (isset($_GET['property_type']) && $_GET['property_type'] == 'land') ? 'selected' : ''; ?>>Land</option>
    </select>

    <label for="min_price">Min Price:</label>
    <input type="number" name="min_price" id="min_price" value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">

    <label for="max_price">Max Price:</label>
    <input type="number" name="max_price" id="max_price" value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">

    <label for="location">Location:</label>
    <input type="text" name="location" id="location" value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">

    <input type="submit" value="Search">
</form>

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

    $sql = "SELECT * FROM properties";
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
            echo "<div class='property'>";
            echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
            echo "<p>Price: $" . number_format($row['price']) . ($row['transaction_type'] == 'rent' ? " per month" : "") . "</p>";
            echo "<p>Type: " . htmlspecialchars($row['property_type']) . "</p>";
            echo "<p>Transaction: " . htmlspecialchars($row['transaction_type']) . "</p>";
            echo "</div>";
        }

        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<?php include '../includes/footer.php'; ?>