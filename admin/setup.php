<?php
require_once('../config/db_connect.php');

// Check if admins table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'admins'");
if ($tableCheck->num_rows == 0) {
    // Create admins table
    $createTable = "CREATE TABLE admins (
        admin_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($createTable) === TRUE) {
        echo "Admins table created successfully.<br>";
    } else {
        echo "Error creating admins table: " . $conn->error . "<br>";
        exit;
    }
}

// Add default admin user
$username = 'admin';
$password = password_hash('admin123', PASSWORD_DEFAULT); // Default password
$email = 'admin@example.com';

// Check if admin already exists
$checkAdmin = $conn->prepare("SELECT * FROM admins WHERE username = ?");
$checkAdmin->bind_param("s", $username);
$checkAdmin->execute();
$result = $checkAdmin->get_result();

if ($result->num_rows == 0) {
    $stmt = $conn->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $email);
    
    if ($stmt->execute()) {
        echo "Default admin user created successfully.<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
        echo "Please change the password after first login!<br>";
    } else {
        echo "Error creating admin user: " . $conn->error . "<br>";
    }
    $stmt->close();
} else {
    echo "Admin user already exists.<br>";
}
$checkAdmin->close();

// Check if products table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'products'");
if ($tableCheck->num_rows > 0) {
    // Table exists, check if it has the required columns
    $result = $conn->query("DESCRIBE products");
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }

    // Check if description column exists
    if (!in_array('description', $columns)) {
        $addDescColumn = "ALTER TABLE products ADD COLUMN description TEXT";
        if ($conn->query($addDescColumn) === TRUE) {
            echo "Description column added to products table.<br>";
        } else {
            echo "Error adding description column: " . $conn->error . "<br>";
        }
    }

    // Check if image_path column exists
    if (!in_array('image_path', $columns)) {
        $addImgColumn = "ALTER TABLE products ADD COLUMN image_path VARCHAR(255) DEFAULT 'assets/sample_product.jpg'";
        if ($conn->query($addImgColumn) === TRUE) {
            echo "Image path column added to products table.<br>";
        } else {
            echo "Error adding image path column: " . $conn->error . "<br>";
        }
    }
} else {
    echo "Warning: Products table does not exist. Please create it first.<br>";
}

echo "<p>Setup complete. <a href='login.php'>Go to Admin Login</a></p>";
?>
