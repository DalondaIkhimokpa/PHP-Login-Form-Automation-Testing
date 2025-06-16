<?php
use PHPUnit\Framework\TestCase;

class UserAuthTest extends TestCase
{
    private $pdo;
    private $testDbName = 'php_login_test_db';

    public function testExample()
    {
        $this->assertTrue(true);
    }

    protected function setUp(): void
    {
        $host = 'localhost';
        $username = 'xxxx';
        $password = 'xxxxxxxxxx';

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

            // Clean any test user
            $this->pdo->exec("DELETE FROM users WHERE username='testuser'");

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

    public function testUserCreationAndPasswordVerification(): void
    {
        // Create test user
        $hashedPassword = password_hash('testpassword', PASSWORD_DEFAULT);
        $insert = $this->pdo->prepare("
            INSERT INTO users (username, password, email)
            VALUES (:username, :password, :email)
        ");
        $success = $insert->execute([
            ':username' => 'testuser',
            ':password' => $hashedPassword,
            ':email'    => 'testuser@example.com'
        ]);

        $this->assertTrue($success, "User should be created successfully");

        // Verify password hash
        $stmt = $this->pdo->prepare("SELECT password FROM users WHERE username='testuser'");
        $stmt->execute();
        $storedHash = $stmt->fetchColumn();

        $this->assertTrue(password_verify('testpassword', $storedHash), "Password should verify correctly");
    }

    protected function tearDown(): void
    {
        if ($this->pdo) {
            $this->pdo->exec("DELETE FROM users WHERE username='testuser'");
            $this->pdo = null;
        }
    }
}
