<?php
use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function testBasicMath() {
        $this->assertEquals(2, 1+1);
    }
}