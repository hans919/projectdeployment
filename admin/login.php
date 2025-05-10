<?php
session_start();
require_once('../config/db_connect.php');

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['user_id'] = $admin['admin_id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['is_admin'] = true;
            
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Invalid username";
    }
    
    $stmt->close();
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Admin Login - TEA GAP</title>
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
            background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            max-width: 450px;
            width: 100%;
            margin: 0 auto;
        }
        
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border-bottom: none;
            padding: 25px 20px;
            text-align: center;
            position: relative;
        }
        
        .card-header h3 {
            color: white;
            font-weight: 600;
            margin: 0;
            font-size: 1.8rem;
        }
        
        .card-body {
            padding: 35px 30px;
        }
        
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
            background-color: #f8f9fa;
        }
        
        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(22, 160, 133, 0.25);
            background-color: #fff;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 8px;
        }
        
        .btn-login {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background-color: #138a72;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(22, 160, 133, 0.3);
        }
        
        .password-container {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-light);
            transition: all 0.3s;
        }
        
        .password-toggle:hover {
            color: var(--accent-color);
        }
        
        .alert-danger {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
            border: none;
            border-radius: 8px;
            padding: 15px;
        }
        
        .back-to-site {
            color: var(--accent-color);
            transition: all 0.3s;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
        }
        
        .back-to-site:hover {
            color: #138a72;
        }
        
        .admin-icon {
            position: absolute;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .admin-icon i {
            font-size: 30px;
            color: var(--accent-color);
        }
        
        .brand-logo {
            margin-bottom: 15px;
        }
        
        .brand-logo h2 {
            color: white;
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0;
        }
        
        .secure-badge {
            display: flex;
            align-items: center;
            font-size: 0.85rem;
            color: #6c757d;
            justify-content: center;
            margin-top: 20px;
        }
        
        .secure-badge i {
            margin-right: 5px;
            color: var(--accent-color);
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="text-center brand-logo">
            <h2>TEA GAP ADMINISTRATION</h2>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="admin-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3>Admin Login</h3>
            </div>
            <div class="card-body">
                <?php if($error): ?>
                    <div class="alert alert-danger d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div><?php echo $error; ?></div>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-user text-muted"></i>
                            </span>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter admin username" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-container">
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter admin password" required>
                                <span class="password-toggle" onclick="togglePassword()" style="z-index: 10; right: 15px;">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i> Access Admin Panel
                    </button>
                </form>
                
                <div class="mt-4 text-center">
                    <a href="../index.php" class="back-to-site">
                        <i class="fas fa-arrow-left me-2"></i> Back to Homepage
                    </a>
                </div>
                
                <div class="secure-badge">
                    <i class="fas fa-shield-alt"></i> Secure administrator access
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
