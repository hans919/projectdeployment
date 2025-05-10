<?php
session_start();
require_once('../config/db_connect.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit;
}

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    // Handle file upload for image
    $imagePath = "";
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/";
        $fileExtension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $newFileName = uniqid() . '.' . $fileExtension;
        $target_file = $target_dir . $newFileName;
        
        // Check if image file is an actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check !== false) {
            // Try to upload file
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $imagePath = "assets/" . $newFileName;
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        } else {
            $error = "File is not an image.";
        }
    } else {
        // No image uploaded, use default
        $imagePath = "assets/sample_product.jpg";
    }
    
    if(empty($error)) {
        $stmt = $conn->prepare("INSERT INTO products (name, category, price, description, image_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $name, $category, $price, $description, $imagePath);
        
        if($stmt->execute()) {
            $success = "Product added successfully!";
        } else {
            $error = "Error adding product: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Add Product - TEA GAP Admin</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #16a085;
            --light-bg: #f8f9fa;
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            height: 100vh;
            position: fixed;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            z-index: 1000;
            width: 250px;
        }
        
        .sidebar.collapsed {
            width: 70px;
        }
        
        .brand-wrapper {
            padding: 1.5rem 1rem;
            background-color: rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .sidebar-toggle {
            color: white;
            background: transparent;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0;
            transition: all 0.3s;
        }
        
        .sidebar-toggle:hover {
            color: var(--accent-color);
        }
        
        .sidebar-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            transition: all 0.3s;
            border-left: 4px solid transparent;
            margin: 5px 0;
            white-space: nowrap;
            overflow: hidden;
        }
        
        .sidebar-link:hover, .sidebar-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 4px solid var(--accent-color);
        }
        
        .content {
            margin-left: 250px;
            padding: 25px;
            transition: all 0.3s;
        }
        
        .content.expanded {
            margin-left: 70px;
        }
        
        .sidebar.collapsed .brand-text,
        .sidebar.collapsed .sidebar-link span {
            display: none;
        }
        
        .sidebar.collapsed .sidebar-link {
            text-align: center;
            padding: 15px 5px;
        }
        
        .sidebar.collapsed .sidebar-link i {
            font-size: 1.5rem;
            margin: 0;
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: none;
            margin-bottom: 25px;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            border-top-left-radius: 10px !important;
            border-top-right-radius: 10px !important;
            padding: 1rem 1.5rem;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(22, 160, 133, 0.25);
        }
        
        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            padding: 10px 24px;
            border-radius: 8px;
        }
        
        .btn-primary:hover {
            background-color: #138a72;
            border-color: #138a72;
        }
        
        .btn-secondary {
            background-color: #95a5a6;
            border-color: #95a5a6;
            padding: 10px 24px;
            border-radius: 8px;
        }
        
        .btn-secondary:hover {
            background-color: #7f8c8d;
            border-color: #7f8c8d;
        }
        
        .dashboard-header {
            background-color: white;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .form-label {
            font-weight: 500;
            color: #2c3e50;
        }
        
        .preview-image {
            max-height: 200px;
            border-radius: 8px;
            border: 2px dashed #e0e0e0;
            padding: 5px;
            margin-top: 10px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
            }
            .sidebar .brand-text {
                display: none;
            }
            .content {
                margin-left: 80px;
            }
            .sidebar-link span {
                display: none;
            }
            .sidebar-link {
                text-align: center;
                padding: 15px 5px;
            }
            .sidebar-link i {
                font-size: 1.5rem;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-auto sidebar" id="sidebar">
                <div class="brand-wrapper">
                    <h4 class="text-white mb-0 fw-bold brand-text"><i class="fas fa-leaf me-2"></i>TEA GAP</h4>
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <div class="pt-3">
                    <a href="dashboard.php" class="sidebar-link">
                        <i class="fas fa-tachometer-alt me-2"></i> <span>Dashboard</span>
                    </a>
                    <a href="manage_products.php" class="sidebar-link active">
                        <i class="fas fa-coffee me-2"></i> <span>Products</span>
                    
                    <div class="border-top my-3 border-secondary opacity-25"></div>
                    <a href="../logout.php" class="sidebar-link">
                        <i class="fas fa-sign-out-alt me-2"></i> <span>Logout</span>
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col content" id="content">
                <div class="dashboard-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0 fw-bold">Add New Product</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="manage_products.php" class="text-decoration-none">Products</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Product</li>
                        </ol>
                    </nav>
                </div>
                
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
                
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">Product Details</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" id="productForm">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-4">
                                        <label for="name" class="form-label">Product Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required placeholder="Enter product name">
                                    </div>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <label for="category" class="form-label">Category</label>
                                            <select class="form-select" id="category" name="category" required>
                                                <option value="" disabled selected>Select a category</option>
                                                <option value="milk_tea">Milk Tea</option>
                                                <option value="pizza">Pizza</option>
                                                <option value="coffee">Coffee</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="price" class="form-label">Price</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₱</span>
                                                <input type="number" step="0.01" class="form-control" id="price" name="price" required placeholder="0.00">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="4" required placeholder="Enter product description"></textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <label for="image" class="form-label">Product Image</label>
                                        <div class="input-group mb-3">
                                            <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                                        </div>
                                        <div class="text-center">
                                            <img id="imagePreview" class="img-fluid preview-image d-none" alt="Image Preview">
                                            <p class="text-muted small mt-2">Upload image (optional). Leave blank to use default image.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <a href="dashboard.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-2"></i> Add Product
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(event) {
            const imagePreview = document.getElementById('imagePreview');
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('d-none');
                }
                
                reader.readAsDataURL(event.target.files[0]);
            } else {
                imagePreview.src = '';
                imagePreview.classList.add('d-none');
            }
        }
        
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            
            // Check if sidebar collapsed state is stored in localStorage
            const sidebarState = localStorage.getItem('sidebarCollapsed');
            
            // Apply stored state if it exists
            if (sidebarState === 'true') {
                sidebar.classList.add('collapsed');
                content.classList.add('expanded');
            }
            
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                content.classList.toggle('expanded');
                
                // Store sidebar state in localStorage
                const isCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);
            });
        });
    </script>
</body>
</html>
