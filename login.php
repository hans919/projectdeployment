<?php
session_start();
require_once('config/db_connect.php');

$errors = array();
$username = "";

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validate form data
    if (empty($username)) {
        $errors[] = "Username is required";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    }

    // If no validation errors, check user credentials
    if (empty($errors)) {
        // Get user from database
        $stmt = $conn->prepare("SELECT user_id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, start a new session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                
                // Redirect to home page
                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Invalid username or password";
            }
        } else {
            $errors[] = "Invalid username or password";
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Login - TEA GAP</title>
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
            background: linear-gradient(135deg, #f5f7fa 0%, #e8edf2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .form-container {
            max-width: 450px;
            width: 100%;
            margin: 50px auto;
            padding: 40px;
            background-color: white;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            border-radius: 12px;
            transition: transform 0.3s;
        }
        
        .form-container:hover {
            transform: translateY(-5px);
        }
        
        .form-title {
            color: var(--primary-color);
            margin-bottom: 25px;
            font-weight: 600;
            position: relative;
            padding-bottom: 15px;
            text-align: center;
        }
        
        .form-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background-color: var(--primary-color);
        }
        
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
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
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 12px 0;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #c0392b;
            border-color: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
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
            color: var(--primary-color);
        }
        
        .form-text {
            color: var(--text-light);
            font-size: 0.85rem;
        }
        
        .alert {
            border: none;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
        }
        
        .alert-danger {
            background-color: rgba(231, 76, 60, 0.1);
            color: #c0392b;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.1);
            color: #27ae60;
        }
        
        .register-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .register-link:hover {
            color: #c0392b;
            text-decoration: underline;
        }
        
        .brand-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .brand-logo h1 {
            font-size: 1.8rem;
            color: var(--primary-color);
            font-weight: 700;
            display: inline-block;
            position: relative;
        }
        
        .brand-logo h1::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--primary-color);
        }
        
        .welcome-text {
            text-align: center;
            margin-bottom: 20px;
            color: var(--text-light);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="brand-logo">
                <a href="index.php" class="text-decoration-none">
                    <h1>TEA GAP</h1>
                </a>
            </div>
            
            <p class="welcome-text">Welcome back! Please login to your account.</p>
            
            <?php if (isset($_SESSION['success_msg'])): ?>
                <div class="alert alert-success d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <div>
                        <?php 
                        echo $_SESSION['success_msg']; 
                        unset($_SESSION['success_msg']);
                        ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div>
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="mb-4">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required value="<?php echo $username; ?>">
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-container">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        <span class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>
                <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i> Login
                    </button>
                </div>
                <div class="text-center">
                    <p class="mb-0">Don't have an account? <a href="register.php" class="register-link">Register</a></p>
                </div>
            </form>
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
