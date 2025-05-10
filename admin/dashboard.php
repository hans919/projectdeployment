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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Admin Dashboard - TEA GAP</title>
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
        
        .table {
            margin-bottom: 0;
        }
        
        .btn-success {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-success:hover {
            background-color: #138a72;
            border-color: #138a72;
        }
        
        .action-btn {
            margin-right: 5px;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .dashboard-header {
            background-color: white;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
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
                    <a href="dashboard.php" class="sidebar-link active">
                        <i class="fas fa-tachometer-alt me-2"></i> <span>Dashboard</span>
                    </a>
                    <a href="manage_products.php" class="sidebar-link">
                        <i class="fas fa-coffee me-2"></i> <span>Products</span>
                    </a>
                    
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
                    <h2 class="mb-0 fw-bold">Admin Dashboard</h2>
                    <div>
                        <span class="badge bg-primary p-2">
                            <i class="fas fa-user me-1"></i> <?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin'; ?>
                        </span>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center p-4">
                                <div class="display-4 text-primary mb-3">
                                    <i class="fas fa-coffee"></i>
                                </div>
                                <h4 class="fw-bold"><?php echo count($products); ?></h4>
                                <p class="text-muted">Total Products</p>
                                <a href="manage_products.php" class="btn btn-sm btn-outline-primary">Manage</a>
                            </div>
                        </div>
                    </div>
                    
                  
                
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white">
                        <h5 class="mb-0 fw-bold">Product Inventory</h5>
                        <a href="add_product.php" class="btn btn-success">
                            <i class="fas fa-plus me-1"></i> Add New Product
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><span class="badge bg-secondary"><?php echo $product['product_id']; ?></span></td>
                                        <td><strong><?php echo $product['name']; ?></strong></td>
                                        <td><span class="badge bg-info text-dark"><?php echo ucfirst(str_replace('_', ' ', $product['category'])); ?></span></td>
                                        <td>₱<?php echo number_format($product['price'], 2); ?></td>
                                        <td><?php echo substr($product['description'], 0, 50) . (strlen($product['description']) > 50 ? '...' : ''); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-outline-primary action-btn">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="#" 
                                                   class="btn btn-sm btn-outline-danger action-btn" 
                                                   data-delete-product 
                                                   data-product-id="<?php echo $product['product_id']; ?>"
                                                   data-product-name="<?php echo htmlspecialchars($product['name']); ?>">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
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
        // Delete product modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Get all delete buttons
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
