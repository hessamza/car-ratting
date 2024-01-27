<?php

namespace App\DataFixtures;

use App\Entity\Car;
use App\Repository\BrandRepository;
use App\Repository\ColorRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ObjectManager;

class CarFixtures extends BaseFixture
{


    public function __construct(
        private readonly ColorRepository $colorRepository,
        private readonly BrandRepository $brandRepository
    ) {
    }


    /**
     * @throws NonUniqueResultException
     */
    protected function loadData(ObjectManager $manager): void
    {
        $this->createMany(Car::class, 10, function (Car $car) {
            $car->setName($this->faker->name());
            $car->setModel($this->faker->year());
            $randomColor = $this->colorRepository->getRandom();
            $randomBrand = $this->brandRepository->getRandom();
            $car->setColor($randomColor);
            $car->setBrand($randomBrand);
        });
        $manager->flush();
    }
}
