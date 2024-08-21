<?php

namespace App\DataFixtures;

use App\Entity\Discount;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DiscountFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
         $brandAsus = $this->getReference('brand_1'); 
         $brandLogitech = $this->getReference('brand_2'); 
 
         $discount1 = new Discount();
         $discount1->setWhole(1000); 
         $manager->persist($discount1);
         $this->addReference('discount_1', $discount1);
 
         $discount2 = new Discount();
         $discount2->setPercentage(0.15);
         $manager->persist($discount2);
         $this->addReference('discount_2', $discount2);
 
         $discount3 = new Discount();
         $discount3->setPercentage(0.05);
         $discount3->setBrand($brandAsus);
         $manager->persist($discount3);
         $this->addReference('discount_3', $discount3);
 
         $discount4 = new Discount();
         $discount4->setFreeItemTreshold(3); 
         $discount4->setBrand($brandLogitech); 
         $manager->persist($discount4);
         $this->addReference('discount_4', $discount4);
 
         $manager->flush();
        
    }
}
