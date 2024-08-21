<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $brandAsus = $this->getReference('brand_1'); 
        $brandLogitech = $this->getReference('brand_2'); 
        $brandWesternDigital = $this->getReference('brand_3'); 
        $brandKingston = $this->getReference('brand_4'); 

        $discount1 = $this->getReference('discount_1'); 
        $discount2 = $this->getReference('discount_2');

        $product1 = new Product();
        $product1->setProductId(3001);
        $product1->setName('ASUS ROG STRIX Z690-A GAMING WIFI D4');
        $product1->setPrice(136590);
        $product1->setPngPath('img/png/1844981_asus-rog-strix-z690-a-gaming-wifi-transformed.webp');
        $product1->setImgPath('img/pic/1844981_asus-rog-strix-z690-a-gaming-wifi.webp');
        $product1->setBrand($brandAsus);
        $manager->persist($product1);
        $this->addReference('product_3001', $product1);

        $product2 = new Product();
        $product2->setProductId(3002);
        $product2->setName('LOGITECH HD Pro Webcam C920');
        $product2->setPrice(28990);
        $product2->setPngPath('img/png/JS038a1-transformed.webp');
        $product2->setImgPath('img/pic/JS038a1.webp');
        $product2->setBrand($brandLogitech);
        $product2->setDiscount($discount2); 
        $manager->persist($product2);

        $product3 = new Product();
        $product3->setProductId(3003);
        $product3->setName('WD Blue 1TB 3.5” 7200rpm 64MB SATA WD10EZEX');
        $product3->setPrice(12990);
        $product3->setPngPath('img/png/1151004_wd_blue_1tb_35_5400_rpm_64_mb_sata_3_wd10ezrz-transformed.webp');
        $product3->setImgPath('img/pic/1151004_wd_blue_1tb_35_5400_rpm_64_mb_sata_3_wd10ezrz.webp');
        $product3->setBrand($brandWesternDigital);
        $manager->persist($product3);

        $product4 = new Product();
        $product4->setProductId(3004);
        $product4->setName('GeForce GTX 1660 Ti 6GB GDDR6 TUF Gaming Evo PCIE');
        $product4->setPrice(208690);
        $product4->setPngPath('img/png/821403-8ab1d.png');
        $product4->setImgPath('img/pic/821403-8ab1d.png');
        $product4->setBrand($brandAsus);
        $product4->setDiscount($discount1);
        $manager->persist($product4);
       

        $product5 = new Product();
        $product5->setProductId(3005);
        $product5->setName('WD Red Plus 2TB 3.5” 5400rpm 128MB SATA WD20EFZX');
        $product5->setPrice(25190);
        $product5->setPngPath('img/png/843449_1-transformed.webp');
        $product5->setImgPath('img/pic/843449_1.webp');
        $product5->setBrand($brandWesternDigital);
        $manager->persist($product5);

        $product6 = new Product();
        $product6->setProductId(3006);
        $product6->setName('Kingston Fury 32GB Beast DDR4 3200MHz CL16 KF432C16BB/32');
        $product6->setPrice(48990);
        $product6->setPngPath('img/png/609003_2-transformed.webp');
        $product6->setImgPath('img/pic/609003_2.webp');
        $product6->setBrand($brandKingston);
        $manager->persist($product6);

        $product7 = new Product();
        $product7->setProductId(3007);
        $product7->setName('LOGITECH K120 Magyar fekete OEM');
        $product7->setPrice(5790);
        $product7->setPngPath('img/png/25921-transformed.webp');
        $product7->setImgPath('img/pic/25921.webp');
        $product7->setBrand($brandLogitech);
        $manager->persist($product7);

        $product8 = new Product();
        $product8->setProductId(3008);
        $product8->setName('LOGITECH B100 fekete OEM');
        $product8->setPrice(3590);
        $product8->setPngPath('img/png/78832_1-transformed.webp');
        $product8->setImgPath('img/pic/78832_1.webp');
        $product8->setBrand($brandLogitech);
        $manager->persist($product8);

        $manager->flush();
    }
}
