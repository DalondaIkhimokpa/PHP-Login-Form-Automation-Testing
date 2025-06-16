<?php

namespace Tests\Browser;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;

class BrowserLoginTest extends TestCase
{
    protected static $driver;

    public static function setUpBeforeClass(): void
    {
        self::$driver = RemoteWebDriver::create(
            'http://localhost:9515',
            DesiredCapabilities::chrome()
        );
    }

    public function testPageTitle()
    {
        self::$driver->get('http://localhost/php-login-system/log-in.php');
        $this->assertStringContainsString('Login', self::$driver->getTitle());
    }

    public function testInvalidLoginFails()
    {
        self::$driver->get('http://localhost/php-login-system/log-in.php');

        self::$driver->findElement(WebDriverBy::name('username'))->sendKeys('wronguser');
        self::$driver->findElement(WebDriverBy::name('password'))->sendKeys('wrongpass');
        self::$driver->findElement(WebDriverBy::cssSelector('button[type=submit]'))->click();

        sleep(1); // Allow time for redirect/error message

        $bodyText = self::$driver->findElement(WebDriverBy::tagName('body'))->getText();
        $this->assertStringContainsString('Invalid username or password', $bodyText);
        

    }

    public function testUserRegistration()
    {
        self::$driver->get('http://localhost/php-login-system/registers.php');

        $randomUser = 'testuser' . rand(1000, 9999);
        self::$driver->findElement(WebDriverBy::name('username'))->sendKeys($randomUser);
        self::$driver->findElement(WebDriverBy::name('email'))->sendKeys($randomUser . '@test.com');
        self::$driver->findElement(WebDriverBy::name('password'))->sendKeys('password123');
        self::$driver->findElement(WebDriverBy::cssSelector('button[type=submit]'))->click();

        sleep(1);
        $bodyText = self::$driver->findElement(WebDriverBy::tagName('body'))->getText();
        $this->assertStringContainsString('Registration successful', $bodyText);
    }

    public function testAccessDeniedForGuests()
    {
        self::$driver->get('http://localhost/php-login-system/log-out.php'); // ensure logged out
        self::$driver->get('http://localhost/php-login-system/dash-board.php');

        $bodyText = self::$driver->findElement(WebDriverBy::tagName('body'))->getText();
        $this->assertStringContainsString('You must be logged in', $bodyText);
    }

    public static function tearDownAfterClass(): void
    {
        self::$driver->quit();
    }
}
