<?php
// DEBUGGING ON
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_errors', 1);

// 👇 Add this to support running one test at a time:
$runTest = isset($_GET['test']) ? intval($_GET['test']) : 0;

// === DB CREDENTIALS ===
$host = 'localhost';
$username = 'xxxx'; // CHANGE THIS
$password = 'xxxx'; // CHANGE THIS
$database = 'php_login_db';// CHANGE THIS

// === CONNECT TO MYSQL ===
$conn = mysqli_connect($host, $username, $password);
if (!$conn) {
    die("❌ Connection failed: " . mysqli_connect_error());
}

// === HELPER: Heading Printer ===
function heading($title) {
    echo "<hr><h2>$title</h2>";
}

// === TEST 1: Create Database ===
heading("Test 1: Create Database");
$sql = "CREATE DATABASE IF NOT EXISTS php_login_db";
if (mysqli_query($conn, $sql)) {
    echo "✅ Database created or already exists.<br>";
} else {
    echo "❌ Error creating database: " . mysqli_error($conn) . "<br>";
}

//EXPECTED RESULTS:
// ✅ MySQLi Connection successful.
// ACTUTAL RESULTS:
// ✅ Database created or already exists.

// // === CONNECT TO SPECIFIC DATABASE ===

mysqli_select_db($conn, $database);

// // === TEST 2: Create 'users' Table ===
heading("Test 2: Create 'users' Table");
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    is_admin BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (mysqli_query($conn, $sql)) {
    echo "✅ Table 'users' created successfully.<br>";
} else {
    echo "❌ Error creating table: " . mysqli_error($conn) . "<br>";
}

// 2.1 Example: Create a backup table
heading("Test 2: Create 'users_two' Table");
    $sql = "CREATE TABLE IF NOT EXISTS users_backup LIKE users";
    if (mysqli_query($conn, $sql)) {
        echo "✅ Created backup table structure 'users_backup'<br>";
        
 // Copy data
        $sql = "TRUNCATE TABLE users_backup";
        mysqli_query($conn, $sql);
        
        $sql = "INSERT INTO users_backup SELECT * FROM users";
        if (mysqli_query($conn, $sql)) {
            echo "✅ Copied data to backup table<br>";
        } else {
            echo "❌ Error copying data: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "❌ Error creating backup table: " . mysqli_error($conn) . "<br>";
    }
    // Expeted output:
    // === TEST 2: Create 'users_two'
    // Actual output:
    // === TEST 2: Create 'users_two' Table

// === TEST 3: Insert Admin User ===
heading("Test 3: Insert Admin User");
$username = 'admin';
$password = password_hash('password', PASSWORD_DEFAULT);
$check = mysqli_query($conn, "SELECT * FROM users WHERE username='admin'");
if (mysqli_num_rows($check) === 0) {
    $sql = "INSERT INTO users (username, password, is_admin) VALUES ('admin', '$password', 1)";
    if (mysqli_query($conn, $sql)) {
        echo "✅ Admin user inserted.<br>";
    } else {
        echo "❌ Insert error: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "ℹ️ Admin already exists.<br>";
}
//Expected output:
// === TEST 3: Insert Admin User ===
// Actual output:
// === TEST 3: Insert Admin User ===

// === TEST 4: Display Users Table ===
heading("Test 4: Display Users Table");
$result = mysqli_query($conn, "SELECT id, username, email, is_admin, created_at FROM users");
if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Username</th><th>Email</th><th>Admin</th><th>Created At</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td><td>{$row['username']}</td><td>{$row['email']}</td><td>" . ($row['is_admin'] ? 'Yes' : 'No') . "</td><td>{$row['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table><br>";
} else {
    echo "❌ No users found or query failed: " . mysqli_error($conn) . "<br>";
}
// Expected output:
// ❌ No users found or query failed: Query execution failed: Unknown database 'php_login_db'
// users found in the databas with the database name php_login_db.
// Actual output:
// ❌ Users found in the databas with the database name php_login_db.

// === TEST 5: Show Users Table Structure ===
heading("Test 5: Users Table Structure");
$result = mysqli_query($conn, "DESCRIBE users");
if ($result) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $val) {
            echo "<td>$val</td>";
        }
        echo "</tr>";
    }
    echo "</table><br>";
} else {
    echo "❌ Error describing table: " . mysqli_error($conn) . "<br>";
}
// Expected output:
// ❌ No table structure found or query failed:
//Actual output:
// ❌ Table structure found in query

// === TEST 6: Count Total Users ===

heading("Test 6: Count Total Users");
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo "✅ Total users: <strong>{$row['total']}</strong><br>";
} else {
    echo "❌ Count query failed: " . mysqli_error($conn) . "<br>";
}
// Expected output:
// ✅ Total users: <strong>5
//Actual output:
// ✅ Total users: <strong>5

// === TEST 7: Show Latest Users ===

heading("Test 7: Show Newest Users");
$result = mysqli_query($conn, "SELECT username, created_at FROM users ORDER BY created_at DESC LIMIT 5");
if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1'><tr><th>Username</th><th>Created At</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>" . htmlspecialchars($row['username']) . "</td><td>{$row['created_at']}</td></tr>";
    }
    echo "</table><br>";
} else {
    echo "❌ No recent users or query failed: " . mysqli_error($conn) . "<br>";
}
// Expected output:
// ✅ No recent users or query failed
// Actual output:
// ✅ Showed newest 5 users

// // === TEST 8: Check for XSS Prevention (Simulated) ===
heading("Test 8: XSS Protection Test (Simulated Input)");
if (($runTest = $_GET['test'] ?? 0)) { 
    echo "<h2>🧪 Test 8: XSS Injection Protection</h2>";
    $xss_payload = "<script>alert('XSS')</script>";
    $safe_payload = htmlspecialchars($xss_payload, ENT_QUOTES, 'UTF-8');
    $email = "xss@example.com"; // Added valid email

    $sql = "INSERT INTO users (username, password, email) VALUES ('$safe_payload', 'xsspass', '$email')";

    if (mysqli_query($conn, $sql)) {
        echo "✅ XSS test user inserted safely (escaped).<br>";
        echo "Inserted username: <pre>" . $safe_payload . "</pre>";
    } else {
        echo "❌ XSS insertion failed: " . mysqli_error($conn) . "<br>";
    }
}
// // Expected output:
// ✅ XSS test user inserted safely (
// Actual output:
// ✅ XSS test user inserted safely (

// // === TEST 9: Check for Duplicate User Handling ===
heading("Test 9: Duplicate Username Check");
$sql = "INSERT INTO users (username, password, email) VALUES ('newuser', 'somepassword', 'user9@example.com')";
if (!mysqli_query($conn, $sql)) {
    echo "✅ Duplicate username properly blocked: " . mysqli_error($conn) . "<br>";
} else {
    echo "❌ Duplicate username was inserted, validation failed!<br>";
}
// Expected output:
// ✅ Duplicate username properly blocked:
// ❌ Duplicate username was inserted, validation failed!
//Actual output:
// ✅ Duplicate username properly blocked:

/*
// // === TEST 10: Clean Up Test Users (Optional) ===
// // heading("Test 10: Cleanup Test Users");
// // $sql = "DELETE FROM users WHERE username LIKE '%<script%' OR username = 'test'";
// // if (mysqli_query($conn, $sql)) {
// //     echo "✅ Test users cleaned up.<br>"
// // } else {
// //     echo "❌ Cleanup failed: " . mysqli_error($conn) . "<br>";
// // }
// // */
// */
?>