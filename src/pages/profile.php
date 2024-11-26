<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $conn->prepare("SELECT username, email, phone_number, facebook_profile FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$phone_numbers = $user['phone_number'] ? explode(',', $user['phone_number']) : [];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_phone'])) {
        $new_phone = $_POST['new_phone'];
        if (count($phone_numbers) < 2 && !empty($new_phone)) {
            $phone_numbers[] = $new_phone;
            $updated_phones = implode(',', $phone_numbers);
            $update_stmt = $conn->prepare("UPDATE users SET phone_number = ? WHERE user_id = ?");
            $update_stmt->bind_param("si", $updated_phones, $user_id);
            $update_stmt->execute();
        }
    } elseif (isset($_POST['add_facebook'])) {
        $facebook_profile = $_POST['facebook_profile'];
        $update_stmt = $conn->prepare("UPDATE users SET facebook_profile = ? WHERE user_id = ?");
        $update_stmt->bind_param("si", $facebook_profile, $user_id);
        $update_stmt->execute();
        $user['facebook_profile'] = $facebook_profile;
    }
}

?>

<?php include "../includes/header.php"; ?>
    <div class="container">
        <h1>User Profile</h1>
        <div class="profile-info">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            
            <h2>Phone Numbers</h2>
            <?php if (!empty($phone_numbers)): ?>
                <ul>
                    <?php foreach ($phone_numbers as $phone): ?>
                        <li><?php echo htmlspecialchars($phone); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No phone numbers added.</p>
            <?php endif; ?>

            <?php if (count($phone_numbers) < 2): ?>
                <form action="profile.php" method="POST" class="add-form">
                    <input type="text" name="new_phone" placeholder="Enter phone number" required>
                    <button type="submit" name="add_phone">Add Phone Number</button>
                </form>
            <?php endif; ?>

            <h2>Facebook Profile</h2>
            <?php if (!empty($user['facebook_profile'])): ?>
                <p><?php echo htmlspecialchars($user['facebook_profile']); ?></p>
            <?php else: ?>
                <p>No Facebook profile added.</p>
            <?php endif; ?>

            <?php if (empty($user['facebook_profile'])): ?>
                <form action="profile.php" method="POST" class="add-form">
                    <input type="text" name="facebook_profile" placeholder="Enter Facebook profile URL" required>
                    <button type="submit" name="add_facebook">Add Facebook Profile</button>
                </form>
            <?php endif; ?>
        </div>

        <a href="logout.php" class="logout-btn">Log Out</a>
    </div>
<?php include '../includes/footer.php'; ?>

