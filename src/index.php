<?php
include 'includes/header.php';
require_once 'config/database.php';
?>

<h1>Welcome to Real Estate App</h1>
<p>Find your dream home today!</p>

<h2>Featured Properties</h2>
<?php
if (isset($conn)) {
    $result = $conn->query("SELECT * FROM properties LIMIT 5");

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='property'>";
            echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
            echo "<p>Price: $" . number_format($row['price']) . "</p>";
            echo "<p>Type: " . htmlspecialchars($row['property_type']) . "</p>";
            echo "<p>Transaction: " . htmlspecialchars($row['transaction_type']) . "</p>";
            echo "</div>";
        }
        $result->free();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Database connection not established.";
}
?>

<?php include 'includes/footer.php'; ?>