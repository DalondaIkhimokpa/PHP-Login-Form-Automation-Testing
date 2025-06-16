<?php
namespace Tests\Browser;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;
use Exception;

class BrowserTest extends TestCase
{
    protected $webDriver;

    protected function setUp(): void
    {
        // ⚠️ SET EXPLICIT TIMEOUTS (60 SECONDS)
        $host = 'http://localhost:9515'; // ChromeDriver's default port
        $capabilities = DesiredCapabilities::chrome();
        $this->webDriver = RemoteWebDriver::create($host, $capabilities, [
            'connectionTimeout' => 60000, // Connection timeout (60 sec)
            'requestTimeout' => 60000     // Request timeout (60 sec)
        ]);
    }

    public function testGoogle()
    {
        try {
            $this->webDriver->get('https://www.google.com');
            $this->assertStringContainsString('Google', $this->webDriver->getTitle());
        } catch (Exception $e) {
            $this->fail("Test failed: " . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        if ($this->webDriver) {
            $this->webDriver->quit(); // Force-close browser
        }
    }
}