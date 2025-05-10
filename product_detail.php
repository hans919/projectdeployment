<?php
session_start();
require_once('config/db_connect.php');

// Disable caching for dynamic content
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Cache-busting timestamp
$timestamp = time();

// Check if product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$product_id = $_GET['id'];

// Fetch product details with prepared statement for security
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit;
}

$product = $result->fetch_assoc();

// Check if the ratings table exists, if not create it
$conn->query("CREATE TABLE IF NOT EXISTS product_ratings (
    rating_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT,
    username VARCHAR(255),
    rating INT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
)");

// Process rating submission
$rating_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_rating'])) {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
    
    // Basic validation
    if ($rating < 1 || $rating > 5) {
        $rating_message = '<div class="alert alert-danger">Please select a valid rating between 1 and 5.</div>';
    } else {
        $stmt = $conn->prepare("INSERT INTO product_ratings (product_id, user_id, username, rating, comment) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisis", $product_id, $user_id, $username, $rating, $comment);
        
        if ($stmt->execute()) {
            $rating_message = '<div class="alert alert-success">Thank you for your review!</div>';
        } else {
            $rating_message = '<div class="alert alert-danger">Error submitting your review. Please try again.</div>';
        }
        $stmt->close();
    }
}

// Fetch product ratings
$ratings = [];
$avg_rating = 0;
$total_ratings = 0;

$stmt = $conn->prepare("SELECT * FROM product_ratings WHERE product_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ratings[] = $row;
        $avg_rating += $row['rating'];
    }
    $total_ratings = count($ratings);
    $avg_rating = $avg_rating / $total_ratings;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <title><?php echo htmlspecialchars($product['name']); ?> - TEA GAP</title>
    <style>
        .product-image {
            max-height: 500px;
            object-fit: contain;
            transition: transform 0.3s;
        }
        
        .product-image:hover {
            transform: scale(1.02);
        }
        
        .product-price {
            color: #e74c3c;
            font-weight: bold;
            font-size: 2rem;
        }
        
        .category-badge {
            background-color: #f8f9fa;
            color: #2c3e50;
            border: 1px solid #e9ecef;
            border-radius: 30px;
            padding: 5px 15px;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .navbar-brand h1 {
            border-bottom: 4px solid #e74c3c;
        }
        
        .nav-link {
            font-weight: bold;
            border-bottom: 4px solid transparent;
        }
        
        .nav-link:hover, .nav-link.active {
            border-bottom: 4px solid #e74c3c;
        }
        
        .nav-link i {
            color: #e74c3c;
        }
        
        .order-btn {
            background-color: #e74c3c;
            color: white;
            border-radius: 30px;
            padding: 10px 25px;
            font-weight: 600;
            border: none;
            transition: all 0.3s;
        }
        
        .order-btn:hover {
            background-color: #c0392b;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .star-rating {
            color: #f1c40f;
            font-size: 1.2rem;
        }
        
        .review-card {
            margin-bottom: 1.5rem;
            border: none;
            border-left: 4px solid #e74c3c;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        
        .review-card:hover {
            transform: translateY(-3px);
        }
        
        .rating-input {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }
        
        .rating-input input {
            display: none;
        }
        
        .rating-input label {
            cursor: pointer;
            font-size: 2rem;
            color: #ddd;
            margin-right: 8px;
            transition: all 0.2s;
        }
        
        .rating-input label:hover,
        .rating-input label:hover ~ label,
        .rating-input input:checked ~ label {
            color: #f1c40f;
        }
        
        .tab-content {
            padding: 20px;
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 0.25rem 0.25rem;
        }
        
        .product-features {
            list-style-type: none;
            padding-left: 0;
        }
        
        .product-features li {
            padding: 8px 0;
            border-bottom: 1px solid #f1f1f1;
        }
        
        .product-features li i {
            color: #e74c3c;
            margin-right: 10px;
        }
        
        .review-header {
            position: relative;
        }
        
        .review-header::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 60px;
            height: 3px;
            background-color: #e74c3c;
        }
        
        .review-stats {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }
        
        .progress {
            height: 10px;
        }
        
        .progress-bar {
            background-color: #e74c3c;
        }
        
        @media (max-width: 768px) {
            .product-image {
                max-height: 300px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php"><h1 class="m-0">TEA GAP</h1></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About Us</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo $_SESSION['username']; ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-shopping-cart"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb -->
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item">
                    <a href="products.php?category=<?php echo $product['category']; ?>" class="text-decoration-none">
                        <?php echo ucfirst(str_replace('_', ' ', $product['category'])); ?>
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['name']); ?></li>
            </ol>
        </nav>
    </div>

    <!-- Product Detail Section -->
    <div class="container my-4">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <img src="<?php echo htmlspecialchars($product['image_path'] . '?v=' . $timestamp); ?>" 
                            class="img-fluid product-image w-100" 
                            alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <span class="category-badge mb-2">
                    <?php echo ucfirst(str_replace('_', ' ', $product['category'])); ?>
                </span>
                
                <h2 class="mb-3 fw-bold"><?php echo htmlspecialchars($product['name']); ?></h2>
                
                <!-- Display average rating -->
                <div class="mb-3 star-rating">
                    <?php 
                    if ($total_ratings > 0) {
                        // Display stars based on rounded average
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= round($avg_rating)) {
                                echo '<i class="fas fa-star"></i>';
                            } elseif ($i - 0.5 <= $avg_rating) {
                                echo '<i class="fas fa-star-half-alt"></i>';
                            } else {
                                echo '<i class="far fa-star"></i>';
                            }
                        }
                        // Display numeric average with one decimal place
                        echo ' <span class="ms-2">(' . number_format($avg_rating, 1) . '/5 · ' . $total_ratings . ' reviews)</span>';
                    } else {
                        echo '<span class="text-muted"><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i> No reviews yet</span>';
                    }
                    ?>
                </div>
                
                <div class="mb-4">
                    <p class="product-price mb-2">₱<?php echo number_format($product['price'], 2); ?></p>
                    <p class="text-success mb-0"><i class="fas fa-check-circle me-1"></i> In Stock</p>
                </div>
                
                <p class="mb-4"><?php echo htmlspecialchars($product['description']); ?></p>
                
                <!-- Add to Cart Form -->
                <form action="add_to_cart.php" method="post" class="mb-4">
                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                    
                    <div class="row g-2">
                        <div class="col-md-4 col-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-light">Qty</span>
                                <input type="number" class="form-control" name="quantity" value="1" min="1" max="10">
                            </div>
                        </div>
                        <div class="col-md-8 col-6">
                            <button type="submit" class="btn order-btn w-100">
                                <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </form>
                
                <div class="d-flex align-items-center mb-3">
                    <a href="#" class="text-decoration-none text-dark me-4">
                        <i class="far fa-heart me-1"></i> Add to Wishlist
                    </a>
                    <a href="#" class="text-decoration-none text-dark">
                        <i class="fas fa-share-alt me-1"></i> Share
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Product Information Tabs -->
        <div class="row mt-5">
            <div class="col-12">
                <ul class="nav nav-tabs" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="true">Product Details</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">Reviews (<?php echo $total_ratings; ?>)</button>
                    </li>
                </ul>
                <div class="tab-content" id="productTabsContent">
                    <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-3">Features</h4>
                                <ul class="product-features">
                                    <li><i class="fas fa-check-circle"></i> Premium Quality</li>
                                    <li><i class="fas fa-check-circle"></i> Freshly Prepared Daily</li>
                                    <li><i class="fas fa-check-circle"></i> Customizable Options</li>
                                    <li><i class="fas fa-check-circle"></i> Locally Sourced Ingredients</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h4 class="mb-3">Specifications</h4>
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th scope="row">Category</th>
                                            <td><?php echo ucfirst(str_replace('_', ' ', $product['category'])); ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Size Options</th>
                                            <td>Small, Medium, Large</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                        <!-- Reviews and Ratings Section -->
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <div class="review-stats">
                                    <h4 class="mb-4">Customer Reviews</h4>
                                    <div class="text-center mb-4">
                                        <div class="display-4 fw-bold"><?php echo number_format($avg_rating, 1); ?></div>
                                        <div class="star-rating mb-2">
                                            <?php 
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= round($avg_rating)) {
                                                    echo '<i class="fas fa-star"></i>';
                                                } else {
                                                    echo '<i class="far fa-star"></i>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <div class="text-muted"><?php echo $total_ratings; ?> reviews</div>
                                    </div>
                                    
                                    <?php
                                    // Count ratings by stars
                                    $rating_counts = array_fill(1, 5, 0);
                                    foreach ($ratings as $r) {
                                        $rating_counts[$r['rating']]++;
                                    }
                                    
                                    // Display rating distribution
                                    for ($i = 5; $i >= 1; $i--):
                                        $percentage = $total_ratings > 0 ? ($rating_counts[$i] / $total_ratings) * 100 : 0;
                                    ?>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-2"><?php echo $i; ?> <i class="fas fa-star" style="font-size: 0.8rem;"></i></div>
                                        <div class="progress flex-grow-1 me-2">
                                            <div class="progress-bar" role="progressbar" style="width: <?php echo $percentage; ?>%" 
                                                aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="small"><?php echo $rating_counts[$i]; ?></div>
                                    </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                                
                            <div class="col-md-8">
                                <!-- Display rating message if any -->
                                <?php if ($rating_message): ?>
                                    <?php echo $rating_message; ?>
                                <?php endif; ?>
                                
                                <!-- Rating Form -->
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-body">
                                        <h4 class="fw-bold mb-3">Write a Review</h4>
                                        <form method="post" action="">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Your Rating</label>
                                                <div class="rating-input">
                                                    <input type="radio" id="star5" name="rating" value="5" required />
                                                    <label for="star5" title="5 stars"><i class="fas fa-star"></i></label>
                                                    <input type="radio" id="star4" name="rating" value="4" />
                                                    <label for="star4" title="4 stars"><i class="fas fa-star"></i></label>
                                                    <input type="radio" id="star3" name="rating" value="3" />
                                                    <label for="star3" title="3 stars"><i class="fas fa-star"></i></label>
                                                    <input type="radio" id="star2" name="rating" value="2" />
                                                    <label for="star2" title="2 stars"><i class="fas fa-star"></i></label>
                                                    <input type="radio" id="star1" name="rating" value="1" />
                                                    <label for="star1" title="1 star"><i class="fas fa-star"></i></label>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="comment" class="form-label fw-bold">Your Review</label>
                                                <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Share your experience with this product" required></textarea>
                                            </div>
                                            <button type="submit" name="submit_rating" class="btn order-btn">
                                                <i class="fas fa-paper-plane me-2"></i>Submit Review
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Display Reviews -->
                                <h4 class="review-header mb-4">Customer Reviews</h4>
                                <?php if ($total_ratings > 0): ?>
                                    <?php foreach ($ratings as $rating): ?>
                                        <div class="card review-card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h5 class="card-title mb-0 fw-bold"><?php echo htmlspecialchars($rating['username']); ?></h5>
                                                    <small class="text-muted"><?php echo date('F j, Y', strtotime($rating['created_at'])); ?></small>
                                                </div>
                                                <div class="star-rating mb-3">
                                                    <?php 
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        if ($i <= $rating['rating']) {
                                                            echo '<i class="fas fa-star"></i>';
                                                        } else {
                                                            echo '<i class="far fa-star"></i>';
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <p class="card-text"><?php echo htmlspecialchars($rating['comment']); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="alert alert-info p-4 text-center">
                                        <i class="fas fa-comment-dots mb-3" style="font-size: 3rem;"></i>
                                        <h5>No Reviews Yet</h5>
                                        <p class="mb-0">Be the first to share your thoughts about this product!</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <div class="container my-5">
        <h3 class="mb-4 fw-bold">You May Also Like</h3>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
            <?php
            // Fetch related products from same category
            $stmt = $conn->prepare("SELECT * FROM products WHERE category = ? AND product_id != ? LIMIT 4");
            $stmt->bind_param("si", $product['category'], $product_id);
            $stmt->execute();
            $related_result = $stmt->get_result();
            
            while ($related = $related_result->fetch_assoc()):
            ?>
                <div class="col">
                    <div class="card h-100 shadow-sm product-card">
                        <a href="product_detail.php?id=<?php echo $related['product_id']; ?>" class="text-decoration-none">
                            <img src="<?php echo htmlspecialchars($related['image_path'] . '?v=' . $timestamp); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($related['name']); ?>" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title text-dark"><?php echo htmlspecialchars($related['name']); ?></h5>
                                <p class="product-price">₱<?php echo number_format($related['price'], 2); ?></p>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-5 mt-5" style="background-color: #2c3e50; color: white;">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="mb-4">
                        <h3 class="fs-4 fw-bold mb-3">
                            <span style="border-bottom: 3px solid #e74c3c; padding-bottom: 5px;">
                                TEA GAP
                            </span>
                        </h3>
                        <p class="mb-3">Share Your Tea, Share Your Story</p>
                        <div class="d-flex gap-3">
                            <a href="#" class="text-white fs-5">
                                <i class="fab fa-facebook-square"></i>
                            </a>
                            <a href="#" class="text-white fs-5">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="text-white fs-5">
                                <i class="fab fa-twitter"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-8 col-md-6">
                    <div class="row">
                        <div class="col-md-4">
                            <h5 class="fw-bold mb-3">Quick Links</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <a href="index.php" class="text-decoration-none" style="color: #ecf0f1;">Home</a>
                                </li>
                                <li class="mb-2">
                                    <a href="products.php" class="text-decoration-none" style="color: #ecf0f1;">Products</a>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="col-md-4">
                            <h5 class="fw-bold mb-3">Contact Us</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-envelope me-2" style="color: #e74c3c;"></i> 
                                    info@teagap.com
                                </li>
                            </ul>
                        </div>
                        
                        <div class="col-md-4">
                            <h5 class="fw-bold mb-3">Legal</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <a href="#" class="text-decoration-none" style="color: #ecf0f1;">Privacy Policy</a>
                                </li>
                                <li class="mb-2">
                                    <a href="#" class="text-decoration-none" style="color: #ecf0f1;">Terms of Service</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="border-top border-secondary pt-4 mt-4">
                <div class="row">
                    <div class="col-12 text-center">
                        <p class="mb-0">&copy; <?php echo date('Y'); ?> Tea Gap. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
