<?php
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    private $pdo;
    private $testDbName = 'php_login_test_db'; // Use separate test database

    protected function setUp(): void
    {
        // Load test configuration
        $host = 'localhost';
        $username = 'xxxx'; // Use privileged user for test setup
        $password = 'xxxxxxx';     // Default XAMPP blank password
        
        try {
            // Connect to MySQL (without specifying database)
            $this->pdo = new PDO(
                "mysql:host=$host",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );

            // Create test database if not exists
            $this->pdo->exec("CREATE DATABASE IF NOT EXISTS `{$this->testDbName}`");
            $this->pdo->exec("USE `{$this->testDbName}`");

            // Initialize test schema
            $this->initializeTestSchema();
            
        } catch (PDOException $e) {
            $this->fail("Test setup failed: " . $e->getMessage());
        }
    }

    private function initializeTestSchema(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS `users` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `username` VARCHAR(50) NOT NULL UNIQUE,
                `password` VARCHAR(255) NOT NULL,
                `is_admin` TINYINT(1) DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Insert test data
        $stmt = $this->pdo->prepare("
            INSERT INTO `users` (username, password, is_admin) 
            VALUES (:username, :password, :is_admin)
        ");

        // Add test admin user
        $stmt->execute([
            ':username' => 'testadmin',
            ':password' => password_hash('Test@1234', PASSWORD_DEFAULT),
            ':is_admin' => 1
        ]);

        // Add test regular user
        $stmt->execute([
            ':username' => 'testuser',
            ':password' => password_hash('User@1234', PASSWORD_DEFAULT),
            ':is_admin' => 0
        ]);
    }

    protected function tearDown(): void
    {
        // Clean up test database
        if ($this->pdo) {
            $this->pdo->exec("DROP DATABASE IF EXISTS `{$this->testDbName}`");
            $this->pdo = null;
        }
    }

    public function testDatabaseConnection()
    {
        $this->assertInstanceOf(PDO::class, $this->pdo);
    }

    public function testUsersTableExists()
    {
        $stmt = $this->pdo->query("SHOW TABLES LIKE 'users'");
        $this->assertEquals(1, $stmt->rowCount());
    }

    public function testAdminUserExists()
    {
        $stmt = $this->pdo->query("SELECT 1 FROM users WHERE is_admin = 1 LIMIT 1");
        $this->assertEquals(1, $stmt->rowCount());
    }

    public function testPasswordHashing()
    {
        $stmt = $this->pdo->query("SELECT password FROM users");
        $passwords = $stmt->fetchAll();
        
        foreach ($passwords as $row) {
            $this->assertTrue(
                password_verify('Test@1234', $row['password']) || 
                password_verify('User@1234', $row['password']),
                "Password hash verification failed"
            );
        }
    }

    public function testNoPlainTextPasswords()
    {
        $stmt = $this->pdo->query("SELECT password FROM users WHERE LENGTH(password) < 60");
        $this->assertEquals(0, $stmt->rowCount());
    }
}