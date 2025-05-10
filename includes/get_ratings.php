<?php
// This file fetches ratings for products to display on the homepage
header('Content-Type: application/json');
require_once('../config/db_connect.php');

// Ensure product_id is provided and is numeric
if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    echo json_encode(['error' => 'Invalid product ID']);
    exit;
}

$product_id = $_GET['product_id'];

// Check if ratings table exists
$table_check = $conn->query("SHOW TABLES LIKE 'product_ratings'");
if ($table_check->num_rows == 0) {
    // Table doesn't exist, return empty rating
    echo json_encode([
        'avg_rating' => 0,
        'total_ratings' => 0,
        'has_ratings' => false
    ]);
    exit;
}

// Fetch rating for the product
$stmt = $conn->prepare("SELECT AVG(rating) as avg_rating, COUNT(rating_id) as total_ratings 
                       FROM product_ratings WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$rating_data = $result->fetch_assoc();
$stmt->close();

// Return rating data
echo json_encode([
    'avg_rating' => $rating_data['avg_rating'] ? round($rating_data['avg_rating'], 1) : 0,
    'total_ratings' => (int)$rating_data['total_ratings'],
    'has_ratings' => (int)$rating_data['total_ratings'] > 0
]);
?>
