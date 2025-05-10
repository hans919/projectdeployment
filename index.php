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
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <title>TEA GAP</title>
    <style>
        :root {
            --primary-color: #e74c3c;
            --secondary-color: #2c3e50;
            --light-bg: #f8f9fa;
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
            font-weight: 600;
            border-bottom: 4px solid transparent;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            border-bottom: 4px solid var(--primary-color);
        }
        
        .nav-link i {
            color: var(--primary-color);
        }
        
        .hero-section {
            background-image: url(pic2.jpg);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
        }
        
        .hero-content {
            padding-top: 150px;
        }
        
        .hero-image {
            max-height: 400px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
            border-radius: 10px;
            transition: transform 0.5s;
        }
        
        .hero-image:hover {
            transform: scale(1.03);
        }
        
        .product-card {
            transition: all 0.3s;
            border: none;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
        
        .product-price {
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .order-btn {
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            border-radius: 30px;
            transition: all 0.3s;
        }
        
        .order-btn:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .section-heading {
            position: relative;
            margin: 50px 0;
            padding-bottom: 15px;
            font-weight: 600;
        }
        
        .section-heading::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--primary-color);
        }
        
        .page-header {
            background: linear-gradient(rgba(44, 62, 80, 0.7), rgba(44, 62, 80, 0.7)), url('assets/header-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 120px 0 40px;
            margin-bottom: 60px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#"><h1 class="m-0">TEA GAP</h1></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Products</a></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact Us</a></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About Us</a></a>
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container hero-content">
            <div class="row">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold">Tea Gap</h1>
                    <p class="fs-4 mb-4">Share Your Tea, Share Your Story</p>
                    <button class="btn btn-warning fw-bold px-4 py-2">Order Now</button>
                </div>
                <div class="col-md-6 text-center">
                    <img src="assets/681eb4af2099a.jpeg" alt="Featured Product" class="img-fluid rounded hero-image" style="max-height: 400px; box-shadow: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23);">
                </div>
            </div>
        </div>
    </section>

    <!-- Milk Tea Section -->
    <div class="container">
        <h2 class="text-center section-heading">Milk Tea</h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            
            <!-- Tea product cards -->
            <?php 
            foreach ($products as $product): 
                if ($product['category'] == 'milk_tea'):
            ?>
            <div class="col">
                <div class="card h-100 shadow product-card">
                    <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="text-decoration-none">
                        <img src="<?php echo htmlspecialchars($product['image_path'] . '?v=' . $timestamp); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price fs-5">₱<?php echo number_format($product['price'], 2); ?></span>
                                <form action="add_to_cart.php" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                    <button type="submit" class="btn order-btn">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <?php 
                endif; 
            endforeach;
            ?>

        </div>
    </div>

    <!-- Pizza Section -->
    <div class="container">
        <h2 class="text-center section-heading">Pizza</h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            
            <!-- Pizza product cards -->
            <?php 
            foreach ($products as $product): 
                if ($product['category'] == 'pizza'):
            ?>
            <div class="col">
                <div class="card h-100 shadow product-card">
                    <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="text-decoration-none">
                        <img src="<?php echo htmlspecialchars($product['image_path'] . '?v=' . $timestamp); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price fs-5">₱<?php echo number_format($product['price'], 2); ?></span>
                                <form action="add_to_cart.php" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                    <button type="submit" class="btn order-btn">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <?php 
                endif; 
            endforeach;
            ?>

        </div>
    </div>

    <!-- Coffee Section -->
    <div class="container mb-5">
        <h2 class="text-center section-heading">Primo Coffee</h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            
            <!-- Coffee product cards -->
            <?php 
            foreach ($products as $product): 
                if ($product['category'] == 'coffee'):
            ?>
            <div class="col">
                <div class="card h-100 shadow product-card">
                    <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="text-decoration-none">
                        <img src="<?php echo htmlspecialchars($product['image_path'] . '?v=' . $timestamp); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price fs-5">₱<?php echo number_format($product['price'], 2); ?></span>
                                <form action="add_to_cart.php" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                    <button type="submit" class="btn order-btn">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <?php 
                endif; 
            endforeach;
            ?>

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
                
                <div class="col-lg-2 col-md-6">
                    <h5 class="fw-bold mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="index.php" class="text-decoration-none" style="color: #ecf0f1;">Home</a>
                        </li>
                        <li class="mb-2">
                            <a href="products.php" class="text-decoration-none" style="color: #ecf0f1;">Products</a>
                        </li>
                        <li class="mb-2">
                            <a href="about.php" class="text-decoration-none" style="color: #ecf0f1;">About Us</a>
                        </li>
                        <li class="mb-2">
                            <a href="contact.php" class="text-decoration-none" style="color: #ecf0f1;">Contact</a>
                        </li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5 class="fw-bold mb-3">Contact Us</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt me-2" style="color: #e74c3c;"></i> 
                            San Jose Baggao, Cagayan Philippines
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-phone me-2" style="color: #e74c3c;"></i> 
                            +63 912 345 6789
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-envelope me-2" style="color: #e74c3c;"></i> 
                            info@teagap.com
                        </li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5 class="fw-bold mb-3">Newsletter</h5>
                    <p class="mb-3">Subscribe to get updates on new products and promotions</p>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Your Email" aria-label="Your Email" aria-describedby="button-newsletter">
                        <button class="btn btn-warning" type="button" id="button-newsletter">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="border-top border-secondary pt-4 mt-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0">&copy; <?php echo date('Y'); ?> Tea Gap. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="#" class="text-decoration-none me-3" style="color: #ecf0f1;">Privacy Policy</a>
                        <a href="#" class="text-decoration-none" style="color: #ecf0f1;">Terms of Service</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Back to top button -->
    <button type="button" class="btn btn-warning btn-floating btn-lg" id="btn-back-to-top" style="position: fixed; bottom: 20px; right: 20px; display: none; border-radius: 50%; width: 50px; height: 50px;">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- Product Ratings Script -->
    <script src="js/product_ratings.js"></script>
    <script>
        // Back to top button functionality
        const myButton = document.getElementById("btn-back-to-top");
        window.onscroll = function() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                myButton.style.display = "flex";
                myButton.style.alignItems = "center";
                myButton.style.justifyContent = "center";
            } else {
                myButton.style.display = "none";
            }
        };
        
        myButton.addEventListener("click", function() {
            document.body.scrollTop = 0; // For Safari
            document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
        });
        
        // Add smooth scrolling to all links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>