<?php

namespace App\Tests\Unit;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Product;
class ProductTest extends KernelTestCase
{
    public function getEntity(): Product
    {
        return ( new Product() )->setName('Product')
        ->setActive(true)
        ->setCreatedAt(new \DateTimeImmutable())
        ->setUpdatedAt(new \DateTimeImmutable());

    }
    public function testEntityValid(): void
    {
        self::bootKernel();
        $container =  static ::getContainer();
        $product = $this->getEntity();
        $errors = $container->get('validator')->validate($product);

        $this->assertCount(0, $errors);
        // $routerService = static::getContainer()->get('router');
        // $myCustomService = static::getContainer()->get(CustomService::class);
    }
}
