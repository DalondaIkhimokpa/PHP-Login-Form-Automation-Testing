<?php

namespace Test\Browser;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;

class SeleniumLoginTest extends TestCase
{
    protected $driver;
    protected $config;

    protected function setUp(): void
    {
        $this->config = require __DIR__ . '/../../config.php';

        $options = new ChromeOptions();
        $options->setBinary('/Applications/Google Chrome.app/Contents/MacOS/Google Chrome');
        $options->addArguments(['--start-maximized']);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        $this->driver = RemoteWebDriver::create('http://localhost:9515', $capabilities);
    }

    public function testLoginPageLoads(): void
    {
        $this->driver->get('http://localhost/php-login-system/log-in.php');
        sleep(2);

        $title = $this->driver->getTitle();
        $this->assertStringContainsString('Login', $title);
    }

    public function testAdminLoginSuccess(): void
    {
        $this->driver->get('http://localhost/php-login-system/log-in.php');
        sleep(1);

        $this->driver->findElement(WebDriverBy::name('username'))->sendKeys($this->config['admin_username']);
        $this->driver->findElement(WebDriverBy::name('password'))->sendKeys($this->config['admin_password']);
        sleep(1);

        $this->driver->findElement(WebDriverBy::cssSelector('form'))->submit();
        sleep(2);

        $body = $this->driver->getPageSource();
        $this->assertStringContainsString('Dashboard', $body);
    }

    protected function tearDown(): void
    {
        $this->driver->quit();
    }
}


