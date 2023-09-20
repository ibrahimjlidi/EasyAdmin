
<?php

use PHPUnit\Framework\TestCase;
use App\Service\MyService; 

class MyServiceTest extends TestCase
{
    public function testSomeMethod()
    {
        $myService = new MyService(); 

        $result = $myService->someMethodToTest(); 

        $this->assertEquals('expected_result', $result);
    }
}
