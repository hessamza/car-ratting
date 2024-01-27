<?php

namespace App\DataFixtures;

use App\Entity\Color;
use Doctrine\Persistence\ObjectManager;

class ColorFixtures extends BaseFixture
{

    protected function loadData(ObjectManager $manager): void
    {
        $this->createMany(Color::class, 10, function (Color $color) {
            $color->setName($this->faker->colorName());
        });
        $manager->flush();
    }
}
