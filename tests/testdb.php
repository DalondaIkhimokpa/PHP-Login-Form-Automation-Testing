<?php
// Test database connection
require 'db.php';
if ($conn) {
    echo "Database connection successful!";
} else {
    echo "Database connection failed.";
}
// Fix database name - should be php_login_db not php_login_system
$connection = mysqli_connect('localhost', 'xxxx', 'xxxx', 'php_login_db');
?>


<?php echo "PHP is working!"; ?>
