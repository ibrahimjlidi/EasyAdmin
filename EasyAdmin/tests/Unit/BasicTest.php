<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Monolog\Logger;
class BasicTest extends TestCase
{
    public function testSomething(): void
    {
        $this->assertTrue(true);
        $logger = $this->createMock(Logger::class);
    }
}
