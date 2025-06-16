<?php
// ==============================================
// STEP 5: DASHBOARD (dash-board.php)
// ==============================================
session_start();
require 'db.php';


if (!isset($_SESSION['username'])) {
    echo '<div class="error">You must be logged in to access the dashboard.</div>';
    exit(); // ⛔ Stop execution so the dashboard content isn't shown
}

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: log-in.php");
    exit();
}

// Display login success message if exists
$success_message = '';
if (isset($_SESSION['user_login_success'])) {
    $success_message = $_SESSION['user_login_success'];
    unset($_SESSION['user_login_success']);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash'] = "You must be logged in";
    header("Location: log-in.php");
    exit;
}


// Display registration success message if exists
if (isset($_SESSION['registration_success'])) {
    $success_message = "Registration successful! Welcome aboard.";
    unset($_SESSION['registration_success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
            color: #333;
        }
        .welcome-message {
            font-size: 2em;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #c3e6cb;
        }
        .admin-banner {
            background-color: #e2f0fd;
            color: #004085;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #bee5eb;
        }
        .dashboard-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .logout-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .logout-link:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="welcome-message">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
    </div>

    <?php if (!empty($success_message)): ?>
        <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <div class="dashboard-section">
        <h2>Your Dashboard</h2>
        <p>This is your personalized dashboard where you can access all the features available to your account.</p>
        
        <?php if ($_SESSION['is_admin']): ?>
            <div class="admin-banner">
                <h3>Administrator Access</h3>
                <p>You have administrator privileges. You can manage the system using the <a href="admins.php" style="color:#004085;font-weight:bold;">Admin Panel</a>.</p>
            </div>
        <?php endif; ?>
    </div>

    <a href="log-out.php" class="logout-link">Logout</a>
</body>
</html>