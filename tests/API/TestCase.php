<?php

namespace App\Tests\API;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Brand;
use App\Entity\Car;
use App\Entity\Color;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

abstract class TestCase extends ApiTestCase
{

    protected AbstractDatabaseTool $databaseTool;

    protected function setUp(): void
    {
        parent::setUp();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    }


    protected function getBrand(): ?Brand
    {
        $entityManager = $this->getEntityManager();
        $brandRepository = $entityManager->getRepository(Brand::class);
        return $brandRepository->findOneBy([], ['id' => 'ASC']);
    }


    protected function getColor(): ?Color
    {
        $entityManager = $this->getEntityManager();
        $colorRepository = $entityManager->getRepository(Color::class);
        return $colorRepository->findOneBy([], ['id' => 'ASC']);
    }
    protected function getCar(): ?Car
    {
        $entityManager = $this->getEntityManager();
        $carRepository = $entityManager->getRepository(Car::class);
        return $carRepository->findOneBy([], ['id' => 'ASC']);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get('doctrine')->getManager();
    }
}
