<?php
include "../session.php";
require_once '../config/database.php';

$success_message = $error_message = '';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission
    $title = $_POST['title'];
    $description = $_POST['description'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip_code = $_POST['zip_code'];
    $transaction_type = $_POST['transaction_type'];
    $property_type = $_POST['property_type'];
    $bedrooms = $_POST['bedrooms'];
    $bathrooms = $_POST['bathrooms'];
    $area_sqft = $_POST['area_sqft'];

    // Handle transaction-specific fields
    if ($transaction_type === 'sale') {
        $price = $_POST['price'];
        $down_payment = $_POST['down_payment'];
        $monthly_payment = null;
    } else {
        $price = $_POST['monthly_payment'];
        $down_payment = null;
        $monthly_payment = $_POST['monthly_payment'];
    }

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO properties (user_id, title, description, price, down_payment, monthly_payment, address, city, state, zip_code, transaction_type, property_type, bedrooms, bathrooms, area_sqft) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdddssssssiii", $user_id, $title, $description, $price, $down_payment, $monthly_payment, $address, $city, $state, $zip_code, $transaction_type, $property_type, $bedrooms, $bathrooms, $area_sqft);
    
    if ($stmt->execute()) {
        $property_id = $stmt->insert_id;

        // Handle image uploads
        if (!empty($_FILES['images']['name'][0])) {
            $upload_dir = '../uploads/properties/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                if (!mkdir($upload_dir, 0755, true)) {
                    $error_message = "Failed to create upload directory.";
                }
            }
            
            // Check if directory is writable
            if (!is_writable($upload_dir)) {
                $error_message = "Upload directory is not writable.";
            }
            
            if (empty($error_message)) {
                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    $file_name = $_FILES['images']['name'][$key];
                    $file_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($tmp_name, $file_path)) {
                        // Insert image path into the database
                        $image_stmt = $conn->prepare("INSERT INTO property_images (property_id, image_url) VALUES (?, ?)");
                        $image_stmt->bind_param("is", $property_id, $file_path);
                        $image_stmt->execute();
                    } else {
                        $error_message = "Failed to move uploaded file: " . $_FILES['images']['error'][$key];
                        break;
                    }
                }
            }
        }

        if (empty($error_message)) {
            $success_message = "Property listed successfully!";
        }
    } else {
        $error_message = "Error: " . $stmt->error;
    }
}

include '../includes/header.php';
?>

<main class="list-property-page">
    <div class="container">
        <h1 class="page-title">List Your Property</h1>
        <?php
        if ($success_message) {
            echo "<p class='success-message'>" . htmlspecialchars($success_message) . "</p>";
        } elseif ($error_message) {
            echo "<p class='error-message'>" . htmlspecialchars($error_message) . "</p>";
        }
        ?>
        <form action="list-property.php" method="POST" enctype="multipart/form-data" class="property-form">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="transaction_type">Transaction Type</label>
                <select id="transaction_type" name="transaction_type" required>
                    <option value="sale">For Sale</option>
                    <option value="rent">For Rent</option>
                </select>
            </div>
            <div id="sale-fields" class="transaction-fields">
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" id="price" name="price" step="0.01">
                </div>
                <div class="form-group">
                    <label for="down_payment">Down Payment</label>
                    <input type="number" id="down_payment" name="down_payment" step="0.01">
                </div>
            </div>
            <div id="rent-fields" class="transaction-fields" style="display: none;">
                <div class="form-group">
                    <label for="monthly_payment">Monthly Payment</label>
                    <input type="number" id="monthly_payment" name="monthly_payment" step="0.01">
                </div>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" required>
            </div>
            <div class="form-group">
                <label for="state">State</label>
                <input type="text" id="state" name="state" required>
            </div>
            <div class="form-group">
                <label for="zip_code">ZIP Code</label>
                <input type="text" id="zip_code" name="zip_code" required>
            </div>
            <div class="form-group">
                <label for="property_type">Property Type</label>
                <select id="property_type" name="property_type" required>
                    <option value="house">House</option>
                    <option value="apartment">Apartment</option>
                    <option value="condo">Condo</option>
                    <option value="land">Land</option>
                </select>
            </div>
            <div class="form-group">
                <label for="bedrooms">Bedrooms</label>
                <input type="number" id="bedrooms" name="bedrooms" required>
            </div>
            <div class="form-group">
                <label for="bathrooms">Bathrooms</label>
                <input type="number" id="bathrooms" name="bathrooms" required>
            </div>
            <div class="form-group">
                <label for="area_sqft">Area (sq ft)</label>
                <input type="number" id="area_sqft" name="area_sqft" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="images">Images</label>
                <input type="file" id="images" name="images[]" accept="image/*" multiple>
            </div>
            <button type="submit" class="submit-button">List Property</button>
        </form>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const transactionType = document.getElementById('transaction_type');
    const saleFields = document.getElementById('sale-fields');
    const rentFields = document.getElementById('rent-fields');

    function toggleFields() {
        if (transactionType.value === 'sale') {
            saleFields.style.display = 'block';
            rentFields.style.display = 'none';
        } else {
            saleFields.style.display = 'none';
            rentFields.style.display = 'block';
        }
    }

    transactionType.addEventListener('change', toggleFields);
    toggleFields(); // Call once to set initial state
});
</script>

<?php include '../includes/footer.php'; ?>

