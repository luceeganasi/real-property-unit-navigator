<?php
include "../session.php" ;
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['property_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$property_id = $_POST['property_id'];

$stmt = $conn->prepare("DELETE FROM bookmarks WHERE user_id = ? AND property_id = ?");
$stmt->bind_param("ii", $user_id, $property_id);
$stmt->execute();

header("Location: bookmarks.php");
exit();

