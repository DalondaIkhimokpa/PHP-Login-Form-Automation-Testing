<?php

use PHPUnit\Framework\TestCase;

class AdminTest extends TestCase
{
    private $pdo;
    private $testDbName = 'php_login_test_db'; // Make sure this matches your test DB

    protected function setUp(): void
    {
        $host = 'localhost';
        $username = 'xxxx';
        $password = 'xxxxxx';
    
        try {
            $this->pdo = new PDO(
                "mysql:host=$host",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
    
            $this->pdo->exec("USE `{$this->testDbName}`");
            $this->initializeTestSchema();
    
            // Ensure a clean slate
            $this->pdo->exec("DELETE FROM users WHERE username = 'xxxx'");
    
            // Insert admin user for every test
            $hashedPassword = password_hash('xxxxx', PASSWORD_DEFAULT);
            $insert = $this->pdo->prepare("
                INSERT INTO users (username, password, email, is_admin)
                VALUES (:username, :password, :email, :is_admin)
            ");
            $insert->execute([
                ':username' => 'xxxx',
                ':password' => $hashedPassword,
                ':email'    => 'xxxx@demo.com',
                ':is_admin' => 1
            ]);
    
        } catch (PDOException $e) {
            $this->fail("Connection failed: " . $e->getMessage());
        }
    }
    

private function initializeTestSchema(): void
{
    $this->pdo->exec("
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `email` VARCHAR(100),
            `is_admin` TINYINT(1) DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
}

public function testAdminUserCreation(): void
{
    // Check if admin user already exists
    $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'xxxx'");
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        $hashedPassword = password_hash('xxxxx', PASSWORD_DEFAULT);
        $insert = $this->pdo->prepare("
            INSERT INTO users (username, password, email, is_admin)
            VALUES (:username, :password, :email, :is_admin)
        ");
        $success = $insert->execute([
            ':username' => 'xxxx',
            ':password' => $hashedPassword,
            ':email'    => 'xxxxx@demo.com',
            ':is_admin' => 1
        ]);

        $this->assertTrue($success, "Admin user should be created successfully");
    }

    // Re-check that admin exists
    $check = $this->pdo->prepare("SELECT * FROM users WHERE username = 'admin123' LIMIT 1");
    $check->execute();
    $adminData = $check->fetch();

    $this->assertIsArray($adminData, "Admin user should exist");
    $this->assertSame('admin123', $adminData['username']);
    $this->assertSame('admin123@demo.com', $adminData['email']);
    $this->assertSame(1, (int)$adminData['is_admin'], "Admin user should have admin privileges");
}

public function testAdminHasEmail(): void
{
    $stmt = $this->pdo->prepare("SELECT email FROM users WHERE username = 'xxxx'");
    $stmt->execute();
    $email = $stmt->fetchColumn();

    $this->assertNotEmpty($email);
    $this->assertSame('xxxx@demo.com', $email);
}

    
    protected function tearDown(): void
    {
        // Clean up after test
        if ($this->pdo) {
            $this->pdo->exec("DELETE FROM users WHERE username='xxxxx'");
            $this->pdo = null;
        }
    }
}

