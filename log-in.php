<?php
// ==============================================
// STEP 3: LOGIN (log-in.php)
// ==============================================
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
header("X-XSS-Protection: 1; mode=block");

// Connect to database
require 'db.php';

// Redirect to dashboard if already logged in
if (isset($_SESSION['username'])) {
    header("Location: dash-board.php");
    exit();
}

// Notices
if (isset($_SESSION['admin_redirect'])) {
    echo '<div class="notice" style="padding:10px;background:#fff3cd;margin-bottom:15px;">Please login as admin to continue</div>';
    unset($_SESSION['admin_redirect']);
}

if (isset($_SESSION['logout_message'])) {
    echo '<div class="logout-message" style="padding:10px;background:#d4edda;margin-bottom:15px;">'.htmlspecialchars($_SESSION['logout_message']).'</div>';
    unset($_SESSION['logout_message']);
}

// Process login form
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {
                // ✅ Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = (bool)$user['is_admin'];

                if ($_SESSION['is_admin']) {
                    $_SESSION['admin_login_success'] = "Admin login successful! Welcome back administrator.";
                    header("Location: dash-board.php");
                } else {
                    $_SESSION['user_login_success'] = "Login successful! Welcome back " . htmlspecialchars($user['username']);
                    header("Location: dash-board.php");
                }
                exit();
            } else {
                $error = "Invalid username or password";
            }
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 400px; 
            margin: 30px auto; 
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .login-container {
            background: white;
            padding: 25px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        h2 { 
            text-align: center; 
            color: #333;
            margin-top: 0;
        }
        .error { 
            color: #dc3545;
            background: #f8d7da;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #0056b3;
        }
        .register-link {
            text-align: center;
            margin-top: 15px;
        }
        a {
            color: #007BFF;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <div class="register-link">
            Don't have an account? <a href="registers.php">Register here</a>
        </div>
    </div>
</body>
</html>
    <!-- 
    LEARNING NOTES:
    1. This page handles both login and auto-registration
    2. Passwords are verified against hashed versions
    3. Successful login sets session variables
    4. Error messages appear above the form
    5. Now properly handles admin/regular user login messages
    -->
</body>
</html>
