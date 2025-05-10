<?php
session_start();
require_once('../config/db_connect.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit;
}

// Fetch all products
$stmt = $conn->prepare("SELECT * FROM products ORDER BY category, name");
$stmt->execute();
$result = $stmt->get_result();
$products = array();

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
$stmt->close();

// Filter products by category if filter is set
$filterCategory = isset($_GET['category']) ? $_GET['category'] : '';
$filteredProducts = [];

if ($filterCategory) {
    foreach ($products as $product) {
        if ($product['category'] == $filterCategory) {
            $filteredProducts[] = $product;
        }
    }
} else {
    $filteredProducts = $products;
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
    <title>Manage Products - TEA GAP Admin</title>
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
        
        .btn-success {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-success:hover {
            background-color: #138a72;
            border-color: #138a72;
        }
        
        .dashboard-header {
            background-color: white;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #eaeaea;
            transition: transform 0.2s;
        }
        
        .product-image:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .action-btn {
            margin-right: 5px;
            border-radius: 6px;
            transition: all 0.3s;
        }
        
        .badge-category {
            font-size: 85%;
            padding: 6px 12px;
            border-radius: 30px;
        }
        
        .milk-tea {
            background-color: #E8F4FA;
            color: #0077B6;
        }
        
        .pizza {
            background-color: #FFEBEE;
            color: #C62828;
        }
        
        .coffee {
            background-color: #F3E5D7;
            color: #774936;
        }
        
        .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(22, 160, 133, 0.25);
        }
        
        .price-column {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .empty-state {
            padding: 40px 0;
            text-align: center;
        }
        
        .empty-state .icon {
            font-size: 3rem;
            color: #95a5a6;
            margin-bottom: 1rem;
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
            .action-wrapper {
                flex-direction: column;
            }
            .action-btn {
                margin-bottom: 5px;
                width: 100%;
                margin-right: 0;
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
                    </a>
                   
                    <div class="border-top my-3 border-secondary opacity-25"></div>
                    <a href="../logout.php" class="sidebar-link">
                        <i class="fas fa-sign-out-alt me-2"></i> <span>Logout</span>
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col content" id="content">
                <div class="dashboard-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0 fw-bold">Manage Products</h2>
                    <div>
                        <span class="badge bg-primary p-2">
                            <i class="fas fa-coffee me-1"></i> <?php echo count($products); ?> Total Products
                        </span>
                    </div>
                </div>
                
                <?php if(isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <div><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
                </div>
                <?php endif; ?>
                
                <?php if(isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
                </div>
                <?php endif; ?>
                
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold">
                                <?php if($filterCategory): ?>
                                    <?php echo ucfirst(str_replace('_', ' ', $filterCategory)); ?> Products
                                <?php else: ?>
                                    All Products
                                <?php endif; ?>
                            </h5>
                            <small class="text-muted">
                                <?php echo count($filteredProducts); ?> products found
                            </small>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-filter text-muted"></i>
                                    </span>
                                    <select id="categoryFilter" class="form-select border-start-0 ps-0" onchange="window.location.href='manage_products.php?category='+this.value">
                                        <option value="">All Categories</option>
                                        <option value="milk_tea" <?php if($filterCategory == 'milk_tea') echo 'selected'; ?>>Milk Tea</option>
                                        <option value="pizza" <?php if($filterCategory == 'pizza') echo 'selected'; ?>>Pizza</option>
                                        <option value="coffee" <?php if($filterCategory == 'coffee') echo 'selected'; ?>>Coffee</option>
                                    </select>
                                </div>
                            </div>
                            <a href="add_product.php" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i> Add Product
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">ID</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Description</th>
                                        <th class="text-end pe-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(count($filteredProducts) > 0): ?>
                                        <?php foreach ($filteredProducts as $product): ?>
                                        <tr>
                                            <td class="ps-3"><span class="badge bg-secondary"><?php echo $product['product_id']; ?></span></td>
                                            <td>
                                                <img src="../<?php echo $product['image_path']; ?>" class="product-image" alt="<?php echo $product['name']; ?>">
                                            </td>
                                            <td><strong><?php echo $product['name']; ?></strong></td>
                                            <td>
                                                <span class="badge badge-category <?php echo $product['category']; ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $product['category'])); ?>
                                                </span>
                                            </td>
                                            <td class="price-column">₱<?php echo number_format($product['price'], 2); ?></td>
                                            <td><small><?php echo substr($product['description'], 0, 50) . (strlen($product['description']) > 50 ? '...' : ''); ?></small></td>
                                            <td>
                                                <div class="d-flex justify-content-end action-wrapper pe-3">
                                                    <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-outline-primary action-btn" data-bs-toggle="tooltip" title="Edit Product">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a href="#" 
                                                       class="btn btn-sm btn-outline-danger action-btn" 
                                                       data-delete-product 
                                                       data-product-id="<?php echo $product['product_id']; ?>"
                                                       data-product-name="<?php echo htmlspecialchars($product['name']); ?>">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7">
                                                <div class="empty-state">
                                                    <div class="icon">
                                                        <i class="fas fa-box-open"></i>
                                                    </div>
                                                    <h4>No products found</h4>
                                                    <p class="text-muted">
                                                        <?php if($filterCategory): ?>
                                                            There are no products in the <?php echo ucfirst(str_replace('_', ' ', $filterCategory)); ?> category.
                                                        <?php else: ?>
                                                            There are no products in the database yet.
                                                        <?php endif; ?>
                                                    </p>
                                                    <a href="add_product.php" class="btn btn-outline-primary">
                                                        <i class="fas fa-plus me-2"></i> Add Your First Product
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between">
                        <div>
                            <small class="text-muted">Showing <?php echo count($filteredProducts); ?> of <?php echo count($products); ?> total products</small>
                        </div>
                        <a href="dashboard.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3.5rem;"></i>
                    </div>
                    <h5 class="mb-3">Are you sure you want to delete this product?</h5>
                    <p class="mb-1">Product: <span id="productName" class="fw-bold text-danger"></span></p>
                    <p class="mb-4">ID: #<span id="productId"></span></p>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        This action cannot be undone. The product will be permanently removed.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        Cancel
                    </button>
                    <a href="#" id="deleteProductBtn" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Delete Permanently
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete product modal functionality
            document.querySelectorAll('[data-delete-product]').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const productId = this.getAttribute('data-product-id');
                    const productName = this.getAttribute('data-product-name');
                    const deleteUrl = `delete_product.php?id=${productId}&confirm=yes`;
                    
                    // Set modal content
                    document.getElementById('productId').textContent = productId;
                    document.getElementById('productName').textContent = productName;
                    document.getElementById('deleteProductBtn').href = deleteUrl;
                    
                    // Show modal
                    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
                    deleteModal.show();
                });
            });
            
            // Sidebar toggle functionality
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
            
            // Enable tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
</body>
</html>
