<?php
/**
 * User Registration Page
 */
require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('products.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = sanitizeInput($_POST['first_name'] ?? '');
    $lastName = sanitizeInput($_POST['last_name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email address is already registered';
        } else {
            // Create user account
            try {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    INSERT INTO users (email, password, first_name, last_name, phone)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$email, $hashedPassword, $firstName, $lastName, $phone]);
                
                $success = 'Account created successfully! You can now log in.';
                
                // Auto-login
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = "$firstName $lastName";
                $_SESSION['is_admin'] = false;
                
                setFlashMessage('Welcome to ' . SITE_NAME . '!', 'success');
                redirect('products.php');
                
            } catch (PDOException $e) {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }
        .register-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .register-header h2 {
            color: #333;
            font-weight: 600;
        }
        .form-control {
            border-radius: 5px;
            padding: 12px;
        }
        .btn-register {
            width: 100%;
            padding: 12px;
            background: #333;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            transition: background 0.3s;
        }
        .btn-register:hover {
            background: #007bff;
            color: white;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h2><?php echo SITE_NAME; ?></h2>
            <p class="text-muted">Create your account</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>First Name *</label>
                        <input type="text" name="first_name" class="form-control" required
                               value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Last Name *</label>
                        <input type="text" name="last_name" class="form-control" required
                               value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" class="form-control" required
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Password * (minimum 6 characters)</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>

            <div class="form-group">
                <label>Confirm Password *</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-register">
                <i class="fa fa-user-plus"></i> Create Account
            </button>
        </form>

        <div class="login-link">
            <p>Already have an account? <a href="login.php">Log in here</a></p>
            <p><a href="products.php">Continue as guest</a></p>
        </div>
    </div>
</body>
</html>
