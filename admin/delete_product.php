<?php
session_start();
require_once('../config/db_connect.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit;
}

// Check if product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$product_id = $_GET['id'];
$product_name = '';
$is_confirmed = isset($_GET['confirm']) && $_GET['confirm'] == 'yes';

// Get the product details
$stmt = $conn->prepare("SELECT product_id, name, image_path FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    $product_name = $product['name'];
    
    // If confirmed, delete the product
    if ($is_confirmed) {
        // Delete the product
        $deleteStmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        $deleteStmt->bind_param("i", $product_id);
        
        if ($deleteStmt->execute()) {
            // Delete image file if it's not the default image
            if ($product['image_path'] != "assets/sample_product.jpg") {
                @unlink("../" . $product['image_path']);
            }
            
            $_SESSION['success_message'] = "Product deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Error deleting product: " . $conn->error;
        }
        
        $deleteStmt->close();
        
        // Redirect back to the referring page or dashboard if not available
        $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'dashboard.php';
        header("Location: $redirect_url");
        exit;
    }
} else {
    $_SESSION['error_message'] = "Product not found.";
    header("Location: dashboard.php");
    exit;
}
$stmt->close();

// If we get here, we're showing the confirmation page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Delete Product - TEA GAP Admin</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #16a085;
            --danger-color: #e74c3c;
            --light-bg: #f8f9fa;
            --text-dark: #2c3e50;
            --text-light: #7f8c8d;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .delete-card {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
            border-top: 4px solid var(--danger-color);
        }
        
        .card-header {
            background-color: #fff;
            padding: 25px 30px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .card-footer {
            background-color: #fff;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
        }
        
        .btn-back {
            background-color: #95a5a6;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .btn-back:hover {
            background-color: #7f8c8d;
            color: white;
        }
        
        .btn-delete {
            background-color: var(--danger-color);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .btn-delete:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }
        
        .warning-icon {
            font-size: 3.5rem;
            color: var(--danger-color);
            margin-bottom: 20px;
        }
        
        .product-name {
            font-weight: 600;
            color: var(--danger-color);
        }
    </style>
</head>
<body>
    <div class="delete-card">
        <div class="card-header">
            <h4 class="mb-0 fw-bold">Confirm Deletion</h4>
        </div>
        <div class="card-body text-center">
            <div class="warning-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h5 class="mb-3">Are you sure you want to delete this product?</h5>
            <p class="mb-1">Product: <span class="product-name"><?php echo htmlspecialchars($product_name); ?></span></p>
            <p class="mb-4">ID: #<?php echo $product_id; ?></p>
            <div class="alert alert-warning">
                <i class="fas fa-info-circle me-2"></i>
                This action cannot be undone. The product will be permanently removed from the system.
            </div>
        </div>
        <div class="card-footer">
            <?php 
            // Get the referring page or default to dashboard
            $return_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'dashboard.php';
            ?>
            <a href="<?php echo $return_url; ?>" class="btn btn-back">
                <i class="fas fa-arrow-left me-2"></i> Cancel
            </a>
            <a href="delete_product.php?id=<?php echo $product_id; ?>&confirm=yes" class="btn btn-delete">
                <i class="fas fa-trash me-2"></i> Delete Permanently
            </a>
        </div>
    </div>
</body>
</html>
