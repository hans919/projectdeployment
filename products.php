<?php
session_start();
require_once('config/db_connect.php');

// Disable caching for dynamic content
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Query products with cache-busting timestamp parameter
$timestamp = time();
$query = "SELECT * FROM products ORDER BY category, name";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
$stmt->close();

// Fetch product ratings from database
$product_ratings = [];
$ratings_query = "SELECT product_id, AVG(rating) as avg_rating, COUNT(rating_id) as total_ratings FROM product_ratings GROUP BY product_id";

// Check if the query executes successfully
try {
    $ratings_result = $conn->query($ratings_query);
    if ($ratings_result && $ratings_result->num_rows > 0) {
        while ($rating = $ratings_result->fetch_assoc()) {
            $product_ratings[$rating['product_id']] = $rating;
        }
    }
} catch (Exception $e) {
    // Silently handle the error - table might not exist yet
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Products - TEA GAP</title>
    <style>
        :root {
            --primary-color: #e74c3c;
            --secondary-color: #2c3e50;
            --accent-light: #f8f9fa;
            --text-dark: #2c3e50;
            --text-light: #7f8c8d;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
        }
        
        .navbar-brand h1 {
            border-bottom: 4px solid var(--primary-color);
        }
        
        .nav-link {
            font-weight: bold;
            border-bottom: 4px solid transparent;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            border-bottom: 4px solid var(--primary-color);
        }
        
        .nav-link i {
            color: var(--primary-color);
        }
        
        .product-card {
            transition: all 0.3s;
            height: 100%;
            border: none;
            overflow: hidden;
            border-radius: 12px;
        }
        
        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
        
        .product-img-wrapper {
            height: 220px;
            overflow: hidden;
            position: relative;
            background-color: #f8f9fa;
        }
        
        .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .product-card:hover .product-img {
            transform: scale(1.05);
        }
        
        .product-price {
            color: var(--primary-color);
            font-weight: bold;
            font-size: 1.25rem;
        }
        
        .category-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: rgba(255, 255, 255, 0.85);
            color: var(--secondary-color);
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            z-index: 2;
        }
        
        .order-btn {
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            border-radius: 30px;
            transition: all 0.3s;
            padding: 6px 16px;
            font-weight: 500;
        }
        
        .order-btn:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .section-heading {
            position: relative;
            display: inline-block;
            margin-bottom: 30px;
            padding-bottom: 10px;
        }
        
        .section-heading::after {
            content: '';
            position: absolute;
            width: 60px;
            height: 3px;
            background-color: var(--primary-color);
            bottom: 0;
            left: 0;
        }
        
        .page-header {
            background: linear-gradient(rgba(44, 62, 80, 0.7), rgba(44, 62, 80, 0.7)), url('assets/header-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            padding: 100px 0 40px;
            margin-bottom: 40px;
            text-align: center;
        }
        
        .star-rating {
            margin: 8px 0;
        }
        
        .star-rating i {
            color: #f1c40f;
            font-size: 0.9rem;
        }
        
        .star-rating .text-muted {
            font-size: 0.8rem;
        }
        
        .filter-container {
            background-color: var(--accent-light);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .filter-title {
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .filter-option {
            cursor: pointer;
            transition: all 0.2s;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 5px;
            display: block;
            text-decoration: none;
            color: var(--text-dark);
        }
        
        .filter-option:hover, .filter-option.active {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--primary-color);
        }
        
        .filter-option span {
            float: right;
            background-color: #e9ecef;
            border-radius: 20px;
            padding: 2px 10px;
            font-size: 0.8rem;
        }
        
        .product-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 8px;
            color: var(--secondary-color);
        }
        
        .card-description {
            font-size: 0.9rem;
            height: 60px;
            overflow: hidden;
            margin-bottom: 15px;
            color: #666;
        }
        
        .breadcrumb-item a {
            text-decoration: none;
            color: white;
            opacity: 0.9;
        }
        
        .breadcrumb-item a:hover {
            opacity: 1;
        }
        
        .breadcrumb-item.active {
            color: white;
            opacity: 0.7;
        }
        
        .breadcrumb-item+.breadcrumb-item::before {
            color: white;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
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
                        <a class="nav-link active" href="products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About Us</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo $_SESSION['username']; ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                    <li><a class="dropdown-item" href="admin/dashboard.php">Admin Dashboard</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin/login.php">Admin</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-shopping-cart"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">Our Products</h1>
            <p class="lead mb-4">Discover our handcrafted selection of premium beverages and food items</p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Products</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Products Section -->
    <div class="container mb-5">
        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3 mb-4">
                <div class="filter-container">
                    <h5 class="filter-title">Categories</h5>
                    <a href="products.php" class="filter-option active">
                        All Products <span><?php echo count($products); ?></span>
                    </a>
                    
                    <?php
                    // Count products by category
                    $categories = [];
                    foreach ($products as $product) {
                        $cat = $product['category'];
                        if (!isset($categories[$cat])) {
                            $categories[$cat] = 0;
                        }
                        $categories[$cat]++;
                    }
                    
                    // Display category filters
                    foreach ($categories as $cat => $count):
                    ?>
                        <a href="products.php?category=<?php echo $cat; ?>" class="filter-option">
                            <?php echo ucfirst(str_replace('_', ' ', $cat)); ?> <span><?php echo $count; ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
                
                <div class="filter-container">
                    <h5 class="filter-title">Price Range</h5>
                    <input type="range" class="form-range" min="0" max="500" step="50" id="priceRange">
                    <div class="d-flex justify-content-between mt-2">
                        <span>₱0</span>
                        <span id="priceValue">₱250</span>
                        <span>₱500</span>
                    </div>
                    <button class="btn btn-sm order-btn w-100 mt-3">Apply Filter</button>
                </div>
                
                <div class="filter-container">
                    <h5 class="filter-title">Average Rating</h5>
                    <a href="#" class="filter-option d-flex align-items-center">
                        <div class="star-rating me-2">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        5 Stars
                    </a>
                    <a href="#" class="filter-option d-flex align-items-center">
                        <div class="star-rating me-2">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                        4+ Stars
                    </a>
                    <a href="#" class="filter-option d-flex align-items-center">
                        <div class="star-rating me-2">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                        3+ Stars
                    </a>
                </div>
            </div>
            
            <!-- Product Listings -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="section-heading">All Products</h2>
                    <div class="d-flex align-items-center">
                        <label for="sort-select" class="me-2">Sort by:</label>
                        <select class="form-select form-select-sm" id="sort-select" style="width: auto;">
                            <option value="name">Name</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="rating">Customer Rating</option>
                        </select>
                    </div>
                </div>
                
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                    <?php 
                    // Get category filter if set
                    $filter_category = isset($_GET['category']) ? $_GET['category'] : '';
                    $filtered_products = $filter_category ? array_filter($products, function($p) use ($filter_category) {
                        return $p['category'] == $filter_category;
                    }) : $products;
                    
                    foreach ($filtered_products as $product): 
                    ?>
                    <div class="col">
                        <div class="card product-card shadow-sm h-100">
                            <div class="product-img-wrapper">
                                <span class="category-badge">
                                    <?php echo ucfirst(str_replace('_', ' ', $product['category'])); ?>
                                </span>
                                <img src="<?php echo htmlspecialchars($product['image_path'] . '?v=' . $timestamp); ?>" 
                                    class="product-img" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                
                                <!-- Display product rating -->
                                <div class="star-rating">
                                    <?php if (isset($product_ratings[$product['product_id']])): 
                                        $avg_rating = round($product_ratings[$product['product_id']]['avg_rating'], 1);
                                        $total = $product_ratings[$product['product_id']]['total_ratings'];
                                        
                                        // Display stars with half-star support
                                        for ($i = 1; $i <= 5; $i++):
                                            if ($i <= floor($avg_rating)):
                                                echo '<i class="fas fa-star"></i>';
                                            elseif ($i - 0.5 <= $avg_rating):
                                                echo '<i class="fas fa-star-half-alt"></i>';
                                            else:
                                                echo '<i class="far fa-star"></i>';
                                            endif;
                                        endfor;
                                        
                                        echo ' <span class="text-muted">(' . $avg_rating . ' · ' . $total . ' reviews)</span>';
                                    else:
                                        echo '<i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
                                        echo ' <span class="text-muted">No reviews</span>';
                                    endif; ?>
                                </div>
                                
                                <p class="card-description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . (strlen($product['description']) > 100 ? '...' : ''); ?></p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="product-price">₱<?php echo number_format($product['price'], 2); ?></span>
                                    <div class="d-flex">
                                        <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-outline-secondary me-2">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="add_to_cart.php" method="post">
                                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                            <button type="submit" class="btn order-btn">
                                                <i class="fas fa-cart-plus"></i> Add
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Empty State for No Products -->
                <?php if (count($filtered_products) == 0): ?>
                <div class="text-center py-5">
                    <i class="fas fa-search mb-3" style="font-size: 3rem; color: #ccc;"></i>
                    <h3>No Products Found</h3>
                    <p class="text-muted">Try adjusting your filters or check back later for new products.</p>
                    <a href="products.php" class="btn order-btn mt-3">View All Products</a>
                </div>
                <?php endif; ?>
            </div>
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
                            <a href="#" class="text-white fs-5">
                                <i class="fab fa-tiktok"></i>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Price range slider functionality
        const priceRange = document.getElementById('priceRange');
        const priceValue = document.getElementById('priceValue');
        
        priceRange.addEventListener('input', function() {
            priceValue.textContent = '₱' + this.value;
        });
        
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
</body>
</html>
