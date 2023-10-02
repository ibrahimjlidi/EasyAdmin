<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;
use App\Factory\CategoryFactory;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use App\Factory\StockFactory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    // {
    //     $categories=[];
    //     for ($i = 0; $i < 20; $i++) {
    //     $category = new Category();
    //     $category->setName('category'.$i);
    //     $category->setActive(true);
    //     $category->setCreatedAt(new \DateTimeImmutable());
    //     $manager->persist($category);
    //     }
    //     for ($i = 0; $i < 20; $i++) {
    //         $product = new Product();
    //         $product->setName('category'.$i);
    //         $product->setActive(true);
    //         $product->setCategory($categories[mt_rand(0,count($categories) - 1)]);
    //         $product->setCreatedAt(new \DateTimeImmutable());
    //         $manager->persist($category);
    //         }
    //     $manager->flush();
    // }
    {
        UserFactory::createMany(1 );
        ProductFactory::createMany(10);
        CategoryFactory::createMany(10);
        StockFactory::createMany(10);
    }
}
