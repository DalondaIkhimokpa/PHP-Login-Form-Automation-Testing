<?php
// ==============================================
// REGISTER PAGE (registers.php)
// ==============================================
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require 'db.php'; // ✅ defines $pdo

header("X-XSS-Protection: 1; mode=block");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $age = intval($_POST['age'] ?? 0);
    $gender = trim($_POST['gender'] ?? '');

    // Validation
    if (empty($username) || empty($password) || empty($email)) {
        $error = "Username, password, and email are required.";
    } elseif (strlen($username) < 4) {
        $error = "Username must be at least 4 characters.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = "Username cannot contain special characters.";
    } elseif (strlen($password) < 8 || !preg_match('/[0-9]/', $password)) {
        $error = "Password must be at least 8 characters and contain a number.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email.";
    }

    if ($error === '') {
        try {
            // Check if username exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);

            if ($stmt->rowCount() > 0) {
                $error = "Username already taken.";
            } else {
                // Hash password and insert
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert = $pdo->prepare("INSERT INTO users (username, password, email, age, gender, is_admin) VALUES (?, ?, ?, ?, ?, 0)");
                $insert->execute([$username, $hashed_password, $email, $age, $gender]);

                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['username'] = $username;
                $_SESSION['is_admin'] = false;
                $_SESSION['registration_success'] = true;

                header("Location: dash-board.php");
                exit();
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 0 auto; padding: 20px; }
        input { padding: 8px; margin-bottom: 10px; width: 100%; box-sizing: border-box; }
        .error { color: red; margin-bottom: 10px; padding: 10px; background: #ffeeee; border-radius: 4px; }
        form { display: flex; flex-direction: column; gap: 10px; }
        button { padding: 10px; background: #007BFF; color: white; border: none; cursor: pointer; border-radius: 4px; }
        button:hover { background: #0056b3; }
        a { color: #007BFF; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h2>Register</h2>
    <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post">
        <input name="username" placeholder="Username" required pattern="[a-zA-Z0-9_]+" title="Only letters, numbers, and underscores">  
        <input name="password" type="password" placeholder="Password" required pattern=".{8,}" title="At least 8 characters">   
        <input name="email" type="email" placeholder="Email" required>
        <input type="number" name="age" placeholder="Age (optional)" min="1" max="120">
        <input type="text" name="gender" placeholder="Gender (optional)">
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="log-in.php">Login here</a></p>
</body>
</html>

    <!-- 
    LEARNING NOTES:
    1. This page handles both form display and processing
    2. Password is hashed before storing in database
    3. Successful registration automatically logs user in
    4. Error messages appear above the form
    5. All database errors are now properly caught and displayed
    -->
</body>
</html>
