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
    if (isset($_POST['remove_phone'])) {
        $index = $_POST['phone_index'];
        unset($phone_numbers[$index]);
        $phone_numbers = array_values($phone_numbers);  // Reindex the array after removal
        $updated_phones = implode(',', $phone_numbers);
        $update_stmt = $conn->prepare("UPDATE users SET phone_number = ? WHERE user_id = ?");
        $update_stmt->bind_param("si", $updated_phones, $user_id);
        $update_stmt->execute();
    } elseif (isset($_POST['add_phone'])) {
        $new_phone = $_POST['new_phone'];
        if (count($phone_numbers) < 2 && !empty($new_phone)) {
            $phone_numbers[] = $new_phone;
            $updated_phones = implode(',', $phone_numbers);
            $update_stmt = $conn->prepare("UPDATE users SET phone_number = ? WHERE user_id = ?");
            $update_stmt->bind_param("si", $updated_phones, $user_id);
            $update_stmt->execute();
        }
    } elseif (isset($_POST['remove_facebook'])) {
        $update_stmt = $conn->prepare("UPDATE users SET facebook_profile = NULL WHERE user_id = ?");
        $update_stmt->bind_param("i", $user_id);
        $update_stmt->execute();
        $user['facebook_profile'] = ''; // Clear the profile from the session variable
    } elseif (isset($_POST['add_facebook'])) {
        $facebook_profile = $_POST['facebook_profile'];
        $update_stmt = $conn->prepare("UPDATE users SET facebook_profile = ? WHERE user_id = ?");
        $update_stmt->bind_param("si", $facebook_profile, $user_id);
        $update_stmt->execute();
        
        // Refetch the updated user data after adding the Facebook profile
        $stmt = $conn->prepare("SELECT username, email, phone_number, facebook_profile FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc(); // Update the user variable with the latest data
    }
}

?>

<?php include "../includes/header.php"; ?>
<main class="profile-container">
    <div class="profile-content">
        <div class="profile-header">
            
            <h1><?php echo htmlspecialchars($user['username']); ?></h1>
            <p class="profile-email"><?php echo htmlspecialchars($user['email']); ?></p>
        </div>

        <div class="profile-section">
            <div class="section-header">
                <span class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                </span>
                <?php if (!empty($phone_numbers)): ?>
                    <?php foreach ($phone_numbers as $index => $phone): ?>
                        <div class="phone-number">
                            Tel : <?php echo htmlspecialchars($phone); ?>
                            <form action="profile.php" method="POST" class="inline-form">
                                <input type="hidden" name="phone_index" value="<?php echo $index; ?>">
                                <button type="submit" name="remove_phone" class="remove-btn">remove</button>
                            </form>
                        </div>
                        
                    <?php endforeach; ?>
                    <?php else: ?>
                         <p>No phone number added.</p>
                    <?php endif; ?>
            </div>

            <?php if (count($phone_numbers) < 2): ?>
                <form action="profile.php" method="POST" class="add-form">
                    <input 
                        type="text" 
                        name="new_phone" 
                        placeholder="Enter phone number" 
                        class="input-field"
                        required
                    >
                    <button type="submit" name="add_phone" class="add-btn">
                        Add
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <div class="profile-section">
            <div class="section-header">
                <span class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                </span>
                <?php if (!empty($user['facebook_profile'])): ?>
                    <div class="facebook-profile">
                        <?php echo htmlspecialchars($user['facebook_profile']); ?>
                        <form action="profile.php" method="POST" class="inline-form">
                            <button type="submit" name="remove_facebook" class="remove-btn">remove</button>
                        </form>
                    </div>
                <?php else: ?>
                    <p>No Facebook profile added.</p>
                <?php endif; ?>
            </div>

            <?php if (empty($user['facebook_profile'])): ?>
                <form action="profile.php" method="POST" class="add-form">
                    <input 
                        type="text" 
                        name="facebook_profile" 
                        placeholder="Enter facebook profile" 
                        class="input-field"
                        required
                    >
                    <button type="submit" name="add_facebook" class="add-btn">
                        Add
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <a href="logout.php" class="logout-btn">Log Out</a>
    </div>
</main>
<?php include '../includes/footer.php'; ?>
