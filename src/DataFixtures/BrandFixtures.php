<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use Doctrine\Persistence\ObjectManager;

class BrandFixtures extends BaseFixture
{

    protected function loadData(ObjectManager $manager): void
    {
        $this->createMany(Brand::class, 10, function (Brand $brand) {
            $brand->setName($this->faker->company());
        });
        $manager->flush();
    }
}
