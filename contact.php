<?php
session_start();

// Process contact form submission
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        // In a real application, you'd send an email or save to database here
        $success = "Thank you for your message! We'll get back to you soon.";
    }
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
    <title>Contact Us - TEA GAP</title>
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
        
        .contact-card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            height: 100%;
            border: none;
            transition: transform 0.3s;
        }
        
        .contact-card:hover {
            transform: translateY(-5px);
        }
        
        .contact-icon-wrapper {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background-color: rgba(231, 76, 60, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .contact-card:hover .contact-icon-wrapper {
            background-color: var(--primary-color);
        }
        
        .contact-icon {
            color: var(--primary-color);
            font-size: 2.2rem;
            transition: all 0.3s;
        }
        
        .contact-card:hover .contact-icon {
            color: white;
        }
        
        .contact-info {
            color: var(--text-light);
        }
        
        .contact-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .contact-btn:hover {
            background-color: #c0392b;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
        }
        
        .form-control {
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 8px;
        }
        
        .contact-form {
            background-color: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        
        .map-wrapper {
            height: 100%;
            min-height: 350px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        
        .map-wrapper iframe {
            width: 100%;
            height: 100%;
            min-height: 350px;
            border: none;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        }
        
        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: var(--light-bg);
            color: var(--primary-color);
            border-radius: 50%;
            font-size: 1.1rem;
            transition: all 0.3s;
        }
        
        .social-links a:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-3px);
        }
        
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2.5rem;
            }
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
                        <a class="nav-link active" href="contact.php">Contact Us</a>
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
            <h1 class="display-4 fw-bold">Contact Us</h1>
            <p class="lead">We'd love to hear from you! Reach out with any questions or feedback.</p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Contact</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Contact Info Cards -->
    <div class="container mb-5">
        <div class="row g-4 mb-5">
            <div class="col-lg-3 col-md-6">
                <div class="contact-card text-center p-4">
                    <div class="contact-icon-wrapper">
                        <i class="fas fa-map-marker-alt contact-icon"></i>
                    </div>
                    <h5 class="mb-3">Our Location</h5>
                    <address class="contact-info">
                        San Jose<br>
                        Baggao, Cagayan<br>
                        Philippines
                    </address>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="contact-card text-center p-4">
                    <div class="contact-icon-wrapper">
                        <i class="fas fa-phone-alt contact-icon"></i>
                    </div>
                    <h5 class="mb-3">Phone Number</h5>
                    <p class="contact-info">
                        <a href="tel:+639123456789" class="text-decoration-none" style="color: inherit;">
                            +63 912 345 6789
                        </a>
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="contact-card text-center p-4">
                    <div class="contact-icon-wrapper">
                        <i class="fas fa-envelope contact-icon"></i>
                    </div>
                    <h5 class="mb-3">Email Address</h5>
                    <p class="contact-info">
                        <a href="mailto:info@teagap.com" class="text-decoration-none" style="color: inherit;">
                            info@teagap.com
                        </a>
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="contact-card text-center p-4">
                    <div class="contact-icon-wrapper">
                        <i class="fas fa-clock contact-icon"></i>
                    </div>
                    <h5 class="mb-3">Working Hours</h5>
                    <p class="contact-info">
                        Mon-Sat: 10AM - 8PM<br>
                        Sunday: Closed
                    </p>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Contact Form -->
            <div class="col-lg-6 mb-4">
                <h2 class="section-title">Get In Touch</h2>
                <p class="mb-4">Have a question or feedback? Fill out the form below and we'll respond as soon as possible.</p>
                
                <?php if($success): ?>
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <div><?php echo $success; ?></div>
                    </div>
                <?php endif; ?>
                
                <?php if($error): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <div><?php echo $error; ?></div>
                    </div>
                <?php endif; ?>
                
                <div class="contact-form">
                    <form method="post" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="john@example.com" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" placeholder="How can we help you?" required>
                        </div>
                        <div class="mb-4">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" placeholder="Your message here..." required></textarea>
                        </div>
                        <button type="submit" class="contact-btn">
                            <i class="fas fa-paper-plane me-2"></i> Send Message
                        </button>
                    </form>
                </div>
                
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            
            <!-- Map -->
            <div class="col-lg-6">
                <h2 class="section-title">Find Us</h2>
                <p class="mb-4">Visit our location to experience our products and services in person.</p>
                
                <div class="map-wrapper">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3759.0286256762147!2d121.86957!3d17.90478!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x338558ea599c4c3b%3A0x31a662e3a9151f2f!2sSan%20Jose%2C%20Baggao%2C%20Cagayan!5e0!3m2!1sen!2sph!4v1627974456784!5m2!1sen!2sph"
                        allowfullscreen="" 
                        loading="lazy"
                        title="Our Location">
                    </iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <section class="py-5" style="background-color: var(--light-bg);">
        <div class="container">
            <h2 class="text-center mb-5">Frequently Asked Questions</h2>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item mb-3 border-0 shadow-sm rounded">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Do you offer franchise opportunities?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, we do offer franchise opportunities for entrepreneurs interested in opening their own Tea Gap location. Please contact our business development team at <a href="mailto:franchise@teagap.com">franchise@teagap.com</a> for more information.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item mb-3 border-0 shadow-sm rounded">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Do you cater for events?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, we offer catering services for various events including corporate gatherings, birthdays, and weddings. Please fill out our contact form with your event details, and our team will get back to you with a customized quote.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item mb-3 border-0 shadow-sm rounded">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Do you offer delivery services?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, we offer delivery within a 5km radius of our locations. Orders can be placed through our website or by calling your nearest Tea Gap branch.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
