<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BrandFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $brand1 = new Brand();
        $brand1->setName('ASUS');
        $manager->persist($brand1);

        $brand2 = new Brand();
        $brand2->setName('Logitech');
        $manager->persist($brand2);

        $brand3 = new Brand();
        $brand3->setName('Western Digital');
        $manager->persist($brand3);

        $brand4 = new Brand();
        $brand4->setName('Kingston');
        $manager->persist($brand4);

        $manager->flush();

        $this->addReference('brand_1', $brand1);
        $this->addReference('brand_2', $brand2);
        $this->addReference('brand_3', $brand3);
        $this->addReference('brand_4', $brand4);
    }
}
