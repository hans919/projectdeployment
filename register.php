<?php
session_start();
require_once('config/db_connect.php');

$errors = array();
$username = $email = $fullname = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $fullname = trim($_POST['fullname']);

    // Validate form data
    if (empty($username)) {
        $errors[] = "Username is required";
    }

    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    // Check if username already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $errors[] = "Username already exists";
    }
    $stmt->close();

    // Check if email already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $errors[] = "Email already exists";
    }
    $stmt->close();

    // If no errors, register the user
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user data into database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $hashed_password, $fullname);

        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Registration successful! Please log in.";
            header("Location: login.php");
            exit();
        } else {
            $errors[] = "Something went wrong. Please try again.";
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
    <title>Register - TEA GAP</title>
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
            padding: 20px 0;
        }
        
        .form-container {
            max-width: 550px;
            width: 100%;
            margin: 40px auto;
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
        
        .login-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .login-link:hover {
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
        
        .password-requirements {
            margin-top: 5px;
            color: var(--text-light);
            font-size: 0.8rem;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
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
            
            <p class="welcome-text">Create your account to join our community</p>
            
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
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="fullname" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter your full name" value="<?php echo $fullname; ?>">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Choose a username" required value="<?php echo $username; ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required value="<?php echo $email; ?>">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-container">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
                        <span class="password-toggle" onclick="togglePassword('password', 'toggleIcon1')">
                            <i class="fas fa-eye" id="toggleIcon1"></i>
                        </span>
                    </div>
                    <div class="password-requirements">
                        Password must be at least 6 characters long
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <div class="password-container">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                        <span class="password-toggle" onclick="togglePassword('confirm_password', 'toggleIcon2')">
                            <i class="fas fa-eye" id="toggleIcon2"></i>
                        </span>
                    </div>
                </div>
                
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="termsCheck" required>
                    <label class="form-check-label" for="termsCheck">
                        I agree to the <a href="#" class="login-link">Terms of Service</a> and <a href="#" class="login-link">Privacy Policy</a>
                    </label>
                </div>
                
                <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i> Create Account
                    </button>
                </div>
                
                <div class="text-center">
                    <p class="mb-0">Already have an account? <a href="login.php" class="login-link">Login</a></p>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId, iconId) {
            const passwordField = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
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
