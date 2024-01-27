<?php

namespace App\Tests\API;

use App\DataFixtures\BrandFixtures;
use App\DataFixtures\CarFixtures;
use App\DataFixtures\ColorFixtures;
use App\DataFixtures\ReviewFixtures;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ApiTest extends TestCase
{

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetCars(): void
    {

        $this->databaseTool->loadFixtures([
            BrandFixtures::class,
            ColorFixtures::class,
            CarFixtures::class,
        ]);
        $client = self::createClient();
        $response=$client->request('GET', '/api/cars', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [],
        ]);

        $json = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('hydra:totalItems', $json);
        $this->assertEquals(10, $json['hydra:totalItems']);
        $this->assertArrayHasKey('hydra:member', $json);
        $this->assertCount(10, $json['hydra:member']);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testCreateACar(): void
    {
        $brand= $this->getBrand();
        $color= $this->getColor();

        $data = [
            'name' => 'best car',
            'model' => '1992',
            'Brand' => '/api/brands/'.$brand->getId(),
            'Color' => '/api/colors/'.$color->getId(),
            'created_at'=>new \DateTime()
        ];

        $client = self::createClient();
        $response=$client->request('POST', '/api/cars', [
            'json' =>$data,
        ]);
        $json = json_decode($response->getContent(), true);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertArrayHasKey('name', $json);
        $this->assertEquals('best car', $json['name']);
    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testUpdateNameInCar(): void
    {

        $car= $this->getCar();
        $data = [
            'name' => 'best car 2',
            'Brand'=>'/api/brands/'.$car->getBrand()->getId(),
            'Color'=>'/api/colors/'.$car->getColor()->getId(),
            'model'=>$car->getModel(),
        ];
        $client = self::createClient();
        $response=$client->request('PATCH', '/api/cars/'.$car->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' =>$data,
        ]);
        $json = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('name', $json);
        $this->assertEquals('best car 2', $json['name']);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testDeleteACar(): void
    {

        $car= $this->getCar();
        $client = self::createClient();
        $response=$client->request('DELETE', '/api/cars/'.$car->getId(), [
            'headers' => ['Content-Type' => 'application/json'],
        ]);
        $this->assertEquals(204, $response->getStatusCode());
    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testAddReviewToACar(): void
    {
        $car= $this->getCar();

        $data = [
            'reviewText' => 'best car',
            'starRating' =>4,
        ];
        $client = self::createClient();
        $response=$client->request('POST', '/api/cars/'.$car->getId().'/reviews', [
            'json' =>$data,
        ]);
        $json = json_decode($response->getContent(), true);
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testHighRatedReviewACar(): void
    {
        $this->databaseTool->loadFixtures([
            BrandFixtures::class,
            ColorFixtures::class,
            CarFixtures::class,
            ReviewFixtures::class,
        ]);
        $car= $this->getCar();
        $client = self::createClient();
        $response=$client->request('GET', '/api/cars/'.$car->getId().'/reviews/latest-high-rated');
        $json = json_decode($response->getContent(), true);
        $this->assertCount(5, $json);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
