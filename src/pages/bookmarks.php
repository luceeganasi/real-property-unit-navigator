<?php
include "../session.php" ;
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch bookmarked properties
$stmt = $conn->prepare("
    SELECT p.* 
    FROM properties p
    JOIN bookmarks b ON p.property_id = b.property_id
    WHERE b.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$bookmarked_properties = $result->fetch_all(MYSQLI_ASSOC);

include '../includes/header.php';
?>

<div class="container">
    <h1>Your Bookmarked Properties</h1>
    <?php if (empty($bookmarked_properties)): ?>
        <p>You haven't bookmarked any properties yet.</p>
    <?php else: ?>
        <div class="property-grid">
            <?php foreach ($bookmarked_properties as $property): ?>
                <div class="property-card">
                    <img src="<?php echo htmlspecialchars($property['image_url']); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>">
                    <h2><?php echo htmlspecialchars($property['title']); ?></h2>
                    <p>Price: $<?php echo number_format($property['price']); ?></p>
                    <p><?php echo htmlspecialchars($property['address']); ?>, <?php echo htmlspecialchars($property['city']); ?>, <?php echo htmlspecialchars($property['state']); ?> <?php echo htmlspecialchars($property['zip_code']); ?></p>
                    <a href="/pages/property.php?id=<?php echo $property['property_id']; ?>" class="btn">View Details</a>
                    <form action="/pages/remove_bookmark.php" method="POST">
                        <input type="hidden" name="property_id" value="<?php echo $property['property_id']; ?>">
                        <button type="submit" class="btn btn-secondary">Remove Bookmark</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>

