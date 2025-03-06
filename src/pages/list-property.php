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
    try {
        // Validate and sanitize inputs
        $data = [
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'property_type' => $_POST['property_type'],
            'transaction_type' => $_POST['transaction_type'],
            'address' => trim($_POST['address']),
            'city' => trim($_POST['city']),
            'state' => trim($_POST['state']),
            'zip_code' => trim($_POST['zip_code']),
            'latitude' => floatval($_POST['latitude']),
            'longitude' => floatval($_POST['longitude']),
            'bedrooms' => intval($_POST['bedrooms']),
            'bathrooms' => intval($_POST['bathrooms']),
            'area_sqft' => floatval($_POST['area_sqft'])
        ];

        // Set price-related fields based on transaction type
        if ($data['transaction_type'] === 'sale') {
            $data['price'] = floatval($_POST['price'] ?? 0.00);
            $data['down_payment'] = floatval($_POST['down_payment'] ?? 0.00);
            $data['monthly_payment'] = null;
        } else {
            $data['price'] = 0.00;
            $data['down_payment'] = null;
            $data['monthly_payment'] = floatval($_POST['monthly_payment'] ?? 0.00);
        }

        // Insert property data
        $query = "INSERT INTO properties (
            user_id, title, description, property_type, transaction_type,
            price, bedrooms, bathrooms, area_sqft, address, city, state,
            zip_code, latitude, longitude, down_payment, monthly_payment
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Database prepare failed: " . $conn->error);
        }

        $stmt->bind_param("issssdiiissssdddd",
            $user_id,
            $data['title'],
            $data['description'],
            $data['property_type'],
            $data['transaction_type'],
            $data['price'],
            $data['bedrooms'],
            $data['bathrooms'],
            $data['area_sqft'],
            $data['address'],
            $data['city'],
            $data['state'],
            $data['zip_code'],
            $data['latitude'],
            $data['longitude'],
            $data['down_payment'],
            $data['monthly_payment']
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to save property: " . $stmt->error);
        }

        $property_id = $conn->insert_id;

        // Handle image uploads
        $upload_dir = '../uploads/properties/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Process thumbnail
        if (!empty($_FILES['thumbnail']['name'])) {
            $thumbnail = handleImageUpload($_FILES['thumbnail'], $upload_dir, true);
            savePropertyImage($conn, $property_id, $thumbnail, true);
        }

        // Process additional images
        if (!empty($_FILES['images']['name'][0])) {
            handleMultipleImages($conn, $_FILES['images'], $upload_dir, $property_id);
        }

        $success_message = "Property listed successfully!";
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        error_log("Error in list-property.php: " . $e->getMessage());
    }
}

// Helper functions
function handleImageUpload($file, $upload_dir, $is_thumbnail = false) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 10 * 1024 * 1024; // 10MB

    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed.');
    }

    if ($file['size'] > $max_size) {
        throw new Exception('File is too large. Maximum size is 10MB.');
    }

    $filename = uniqid() . ($is_thumbnail ? '_thumbnail_' : '_') . basename($file['name']);
    $filepath = $upload_dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to move uploaded file.');
    }

    return $filepath;
}

function handleMultipleImages($conn, $files, $upload_dir, $property_id) {
    $count = count($files['name']);
    if ($count > 30) {
        throw new Exception("Maximum of 30 images allowed.");
    }

    for ($i = 0; $i < $count; $i++) {
        $file = [
            'name' => $files['name'][$i],
            'type' => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error' => $files['error'][$i],
            'size' => $files['size'][$i]
        ];

        $filepath = handleImageUpload($file, $upload_dir);
        savePropertyImage($conn, $property_id, $filepath, false);
    }
}

function savePropertyImage($conn, $property_id, $filepath, $is_primary) {
    $stmt = $conn->prepare("INSERT INTO property_images (property_id, image_url, is_primary) VALUES (?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Failed to prepare image insert: " . $conn->error);
    }

    $is_primary = $is_primary ? 1 : 0;
    $stmt->bind_param("isi", $property_id, $filepath, $is_primary);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to save image: " . $stmt->error);
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
                    <label for="thumbnail">Thumbnail Image:</label>
                    <input type="file" id="thumbnail" name="thumbnail" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label for="images">Property Images (Max 30):</label>
                    <input type="file" id="images" name="images[]" accept="image/*" multiple>
                    <p id="image-count">0 images selected</p>
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

        const imagesInput = document.getElementById('images');
        const imageCount = document.getElementById('image-count');

        imagesInput.addEventListener('change', function() {
            const fileCount = this.files.length;
            imageCount.textContent = `${fileCount} image${fileCount !== 1 ? 's' : ''} selected`;

            if (fileCount > 30) {
                alert('You can only upload a maximum of 30 images. Please select fewer images.');
                this.value = ''; // Clear the input
                imageCount.textContent = '0 images selected';
            }
        });
    });
    </script>
</body>
</html>

<?php include '../includes/footer.php'; ?>