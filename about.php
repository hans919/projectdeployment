<?php
session_start();
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
    <title>About Us - TEA GAP</title>
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
        
        .page-header {
            background: linear-gradient(rgba(44, 62, 80, 0.7), rgba(44, 62, 80, 0.7)), url('assets/header-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 120px 0 40px;
            margin-bottom: 60px;
            text-align: center;
        }
        
        .page-header h1 {
            font-weight: 700;
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .breadcrumb-item a {
            color: white;
            text-decoration: none;
            opacity: 0.9;
        }
        
        .breadcrumb-item.active {
            color: white;
            opacity: 0.7;
        }
        
        .breadcrumb-item+.breadcrumb-item::before {
            color: white;
            opacity: 0.8;
        }
        
        .section-title {
            position: relative;
            margin-bottom: 30px;
            font-weight: 600;
            padding-bottom: 10px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 60px;
            height: 3px;
            background-color: var(--primary-color);
        }
        
        .section-title.text-center::after {
            left: 50%;
            transform: translateX(-50%);
        }
        
        .about-image {
            border-radius: 10px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
            transition: all 0.3s;
            overflow: hidden;
        }
        
        .about-image img {
            transition: transform 0.5s;
        }
        
        .about-image:hover img {
            transform: scale(1.03);
        }
        
        .team-member {
            text-align: center;
            margin-bottom: 40px;
            transition: all 0.3s;
        }
        
        .team-member:hover {
            transform: translateY(-10px);
        }
        
        .team-member .member-img {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .team-member img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .team-member:hover img {
            transform: scale(1.05);
        }
        
        .team-member .social-links {
            position: absolute;
            bottom: -40px;
            left: 0;
            right: 0;
            opacity: 0;
            transition: all 0.3s;
            background: rgba(231, 76, 60, 0.8);
            padding: 10px 0;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }
        
        .team-member:hover .social-links {
            bottom: 0;
            opacity: 1;
        }
        
        .team-member .social-links a {
            color: white;
            margin: 0 10px;
            font-size: 1.2rem;
            transition: all 0.3s;
        }
        
        .team-member .social-links a:hover {
            transform: scale(1.2);
        }
        
        .team-member h5 {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .team-member .position {
            color: var(--primary-color);
            font-weight: 500;
            margin-bottom: 10px;
        }
        
        .value-card {
            padding: 30px 25px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            height: 100%;
            transition: all 0.3s;
            border-top: 4px solid transparent;
        }
        
        .value-card:hover {
            transform: translateY(-10px);
            border-top: 4px solid var(--primary-color);
        }
        
        .value-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--primary-color);
            border-radius: 50%;
            font-size: 2rem;
            transition: all 0.3s;
        }
        
        .value-card:hover .value-icon {
            background-color: var(--primary-color);
            color: white;
        }
        
        .milestone-section {
            background-color: var(--secondary-color);
            padding: 80px 0;
            margin: 80px 0;
            color: white;
        }
        
        .milestone-counter {
            text-align: center;
        }
        
        .milestone-counter .number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .milestone-counter .label {
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        .testimonial-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            position: relative;
        }
        
        .testimonial-card::before {
            content: '\201C';
            font-size: 5rem;
            position: absolute;
            top: -20px;
            left: 20px;
            color: rgba(231, 76, 60, 0.1);
            font-family: Georgia, serif;
        }
        
        .testimonial-card .quote {
            font-style: italic;
            margin-bottom: 20px;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
        }
        
        .testimonial-author img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
        }
        
        .testimonial-author .author-info h5 {
            margin-bottom: 0;
            font-weight: 600;
        }
        
        .testimonial-author .author-info p {
            margin-bottom: 0;
            color: var(--text-light);
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
                        <a class="nav-link" href="products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="about.php">About Us</a>
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
            <h1 class="display-4 fw-bold">About Us</h1>
            <p class="lead">Learn about our story, values, and the team behind TEA GAP</p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">About</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Our Story Section -->
    <div class="container mb-5">
        <div class="row align-items-center mb-5 g-5">
            <div class="col-lg-6">
                <h2 class="section-title">Our Story</h2>
                <p class="lead mb-4">From a small café to a beloved establishment</p>
                <p>Tea Gap was founded in 2010 with a simple mission: to provide high-quality tea and pizza that brings people together. What started as a small café in Cebu has grown into a beloved establishment known for its unique blends and flavors.</p>
                <p>Our founder, a tea enthusiast and food lover, wanted to create a space where people could enjoy both traditional and innovative tea concoctions alongside delicious pizzas - an unusual but delightful combination that has become our signature.</p>
                <p class="mb-4">Over the years, we've expanded our menu to include a variety of milk teas, fruit teas, specialty coffees, and gourmet pizzas, always staying true to our commitment to quality and innovation.</p>
                <a href="contact.php" class="btn btn-lg px-4 py-2" style="background-color: #e74c3c; color: white;">
                    <i class="fas fa-handshake me-2"></i> Connect With Us
                </a>
            </div>
            <div class="col-lg-6">
                <div class="about-image">
                    <img src="assets/sample_product.jpg" alt="Tea Gap Story" class="img-fluid w-100">
                </div>
            </div>
        </div>
        
        <!-- Milestone Section -->
        <div class="milestone-section">
            <div class="container">
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-4 mb-md-0">
                        <div class="milestone-counter">
                            <div class="number">13</div>
                            <div class="label">Years of Excellence</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-4 mb-md-0">
                        <div class="milestone-counter">
                            <div class="number">15+</div>
                            <div class="label">Locations</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="milestone-counter">
                            <div class="number">50+</div>
                            <div class="label">Unique Products</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="milestone-counter">
                            <div class="number">10K+</div>
                            <div class="label">Happy Customers</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Our Values -->
        <h2 class="section-title text-center">Our Core Values</h2>
        <p class="text-center mb-5 w-75 mx-auto">At Tea Gap, our values guide everything we do - from sourcing ingredients to serving customers</p>
        
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h4 class="text-center mb-3">Quality</h4>
                    <p class="text-center">We source only the finest ingredients, from premium tea leaves to fresh pizza toppings, to ensure every item we serve exceeds expectations.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h4 class="text-center mb-3">Community</h4>
                    <p class="text-center">We believe in creating spaces where people can connect, share stories, and build memories while enjoying great food and drinks.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h4 class="text-center mb-3">Innovation</h4>
                    <p class="text-center">We're constantly exploring new flavors, techniques, and combinations to bring you unique and exciting menu offerings.</p>
                </div>
            </div>
        </div>
        
        <!-- Customer Testimonials -->
        <h2 class="section-title text-center">What Our Customers Say</h2>
        <p class="text-center mb-5 w-75 mx-auto">Don't just take our word for it - here's what our customers have to say about their experience with Tea Gap</p>
        
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="testimonial-card">
                    <p class="quote">The best milk tea I've ever had! The combination of milk tea and pizza is surprisingly amazing. This place has become my go-to spot for hangouts with friends.</p>
                    <div class="testimonial-author">
                        <img src="assets/sample_product.jpg" alt="Customer">
                        <div class="author-info">
                            <h5>Sarah Johnson</h5>
                            <p>Regular Customer</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="testimonial-card">
                    <p class="quote">Tea Gap has the perfect atmosphere for both work and relaxation. Their unique flavors keep me coming back, and the staff is always friendly and welcoming.</p>
                    <div class="testimonial-author">
                        <img src="assets/sample_product.jpg" alt="Customer">
                        <div class="author-info">
                            <h5>Michael Rodriguez</h5>
                            <p>Loyal Customer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Team Section -->
        <h2 class="section-title text-center">Meet Our Team</h2>
        <p class="text-center mb-5 w-75 mx-auto">The passionate individuals behind Tea Gap who work tirelessly to bring you the best experience</p>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="team-member">
                    <div class="member-img">
                        <img src="assets/sample_product.jpg" alt="Jane Doe">
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    <h5>Jane Doe</h5>
                    <div class="position">Founder & CEO</div>
                    <p class="small">Jane's passion for tea and her entrepreneurial spirit led to the creation of Tea Gap in 2010.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="team-member">
                    <div class="member-img">
                        <img src="assets/sample_product.jpg" alt="John Smith">
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    <h5>John Smith</h5>
                    <div class="position">Head Chef</div>
                    <p class="small">With over 15 years of culinary experience, John creates our delicious pizza recipes that pair perfectly with our drinks.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="team-member">
                    <div class="member-img">
                        <img src="assets/sample_product.jpg" alt="Maria Garcia">
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    <h5>Maria Garcia</h5>
                    <div class="position">Tea Specialist</div>
                    <p class="small">Maria travels the world to source the finest tea leaves and develop our signature blends and innovative recipes.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <section class="py-5 text-center" style="background-color: #f8f9fa;">
        <div class="container">
            <h2 class="fw-bold mb-4">Ready to Experience Tea Gap?</h2>
            <p class="lead mb-4 w-75 mx-auto">Visit us today and discover why our customers keep coming back for more.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="products.php" class="btn btn-lg px-4 py-2" style="background-color: #e74c3c; color: white;">
                    <i class="fas fa-mug-hot me-2"></i> Explore Our Menu
                </a>
                <a href="contact.php" class="btn btn-outline-secondary btn-lg px-4 py-2">
                    <i class="fas fa-map-marker-alt me-2"></i> Find Locations
                </a>
            </div>
        </div>
    </section>

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
</body>
</html>
