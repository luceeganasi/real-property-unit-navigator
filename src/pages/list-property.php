<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../pages/login.php");
    exit();
}

// Include necessary files
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
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
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
    $stmt = $conn->prepare("INSERT INTO properties (user_id, title, description, price, down_payment, monthly_payment, address, city, state, zip_code, latitude, longitude, transaction_type, property_type, bedrooms, bathrooms, area_sqft) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdddssssddssiii", $user_id, $title, $description, $price, $down_payment, $monthly_payment, $address, $city, $state, $zip_code, $latitude, $longitude, $transaction_type, $property_type, $bedrooms, $bathrooms, $area_sqft);
    
    if ($stmt->execute()) {
        $property_id = $stmt->insert_id;

        // Handle image uploads
        if (!empty($_FILES['images']['name'][0])) {
            $upload_dir = '../uploads/properties/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                if (!mkdir($upload_dir, 0755, true)) {
                    $error_message = "Failed to create upload directory. Error: " . error_get_last()['message'];
                }
            }
            
            // Check if directory is writable
            if (!is_writable($upload_dir)) {
                $error_message = "Upload directory is not writable. Please check permissions.";
            }
            
            if (empty($error_message)) {
                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    $file_name = $_FILES['images']['name'][$key];
                    $file_size = $_FILES['images']['size'][$key];
                    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    
                    // Validate file type
                    $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
                    if (!in_array($file_extension, $allowed_extensions)) {
                        $error_message = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
                        break;
                    }
                    
                    // Check file size (limit to 10MB)
                    $max_file_size = 10 * 1024 * 1024; // 10MB in bytes
                    if ($file_size > $max_file_size) {
                        $error_message = "File is too large. Maximum file size is 10MB.";
                        break;
                    }
                    
                    // Generate a unique file name
                    $unique_file_name = uniqid() . '_' . $file_name;
                    $file_path = $upload_dir . $unique_file_name;
                    
                    if (!move_uploaded_file($tmp_name, $file_path)) {
                        $error_message = "Failed to move uploaded file: " . $_FILES['images']['error'][$key];
                        $php_error = error_get_last();
                        if ($php_error) {
                            $error_message .= " PHP Error: " . $php_error['message'];
                        }
                        break;
                    } else {
                        // Insert image path into the database
                        $image_stmt = $conn->prepare("INSERT INTO property_images (property_id, image_url) VALUES (?, ?)");
                        $image_stmt->bind_param("is", $property_id, $file_path);
                        $image_stmt->execute();
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Your Property</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main class="list-property-container">
        <div class="list-property-content">
            <h1>List Your Property</h1>
            <?php
            if ($success_message) {
                echo "<p class='success-message'>" . htmlspecialchars($success_message) . "</p>";
            } elseif ($error_message) {
                echo "<p class='error-message'>" . htmlspecialchars($error_message) . "</p>";
            }
            ?>
            <form action="list-property.php" method="POST" enctype="multipart/form-data" class="list-property-form">
                <div class="form-group">
                    <input type="text" id="title" name="title" placeholder="Title" required>
                </div>
                <div class="form-group">
                    <textarea id="description" name="description" placeholder="Description" required></textarea>
                </div>
                <div class="form-group">
                    <select id="transaction_type" name="transaction_type" required>
                        <option value="">Select Transaction Type</option>
                        <option value="sale">For Sale</option>
                        <option value="rent">For Rent</option>
                    </select>
                </div>
                <div id="sale-fields" class="transaction-fields">
                    <div class="form-group">
                        <input type="number" id="price" name="price" placeholder="Price" step="0.01">
                    </div>
                    <div class="form-group">
                        <input type="number" id="down_payment" name="down_payment" placeholder="Down Payment" step="0.01">
                    </div>
                </div>
                <div id="rent-fields" class="transaction-fields" style="display: none;">
                    <div class="form-group">
                        <input type="number" id="monthly_payment" name="monthly_payment" placeholder="Monthly Payment" step="0.01">
                    </div>
                </div>
                <div class="form-group">
                    <input type="text" id="address" name="address" placeholder="Address" required>
                </div>
                <div class="form-group">
                    <input type="text" id="city" name="city" placeholder="City" required>
                </div>
                <div class="form-group">
                    <input type="text" id="state" name="state" placeholder="State" required>
                </div>
                <div class="form-group">
                    <input type="text" id="zip_code" name="zip_code" placeholder="ZIP Code" required>
                </div>
                <div class="form-group">
                    <input type="number" id="latitude" name="latitude" placeholder="Latitude" step="any" required>
                </div>
                <div class="form-group">
                    <input type="number" id="longitude" name="longitude" placeholder="Longitude" step="any" required>
                </div>
                <div class="form-group">
                    <select id="property_type" name="property_type" required>
                        <option value="">Select Property Type</option>
                        <option value="house">House</option>
                        <option value="apartment">Apartment</option>
                        <option value="condo">Condo</option>
                        <option value="land">Land</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="number" id="bedrooms" name="bedrooms" placeholder="Bedrooms" required>
                </div>
                <div class="form-group">
                    <input type="number" id="bathrooms" name="bathrooms" placeholder="Bathrooms" required>
                </div>
                <div class="form-group">
                    <input type="number" id="area_sqft" name="area_sqft" placeholder="Area (sq ft)" step="0.01" required>
                </div>
                <div class="form-group">
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
            } else if (transactionType.value === 'rent') {
                saleFields.style.display = 'none';
                rentFields.style.display = 'block';
            } else {
                saleFields.style.display = 'none';
                rentFields.style.display = 'none';
            }
        }

        transactionType.addEventListener('change', toggleFields);
        toggleFields(); // Call once to set initial state
    });
    </script>
</body>
</html>

<?php include '../includes/footer.php'; ?>