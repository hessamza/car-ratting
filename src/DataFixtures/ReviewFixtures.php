<?php

namespace App\DataFixtures;

use App\Entity\Review;
use App\Repository\CarRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ReviewFixtures extends Fixture
{


    public function __construct(private readonly CarRepository $carRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $cars = $this->carRepository->findAll();

        foreach ($cars as $car) {
            $reviews = $this->getLatestReviews($car, 5, 6);

            foreach ($reviews as $review) {
                $manager->persist($review);
            }
        }

        $manager->flush();
    }

    private function getLatestReviews($car, $limit, $minRating): array
    {
        $reviews = $car->getReviews()->toArray();

        $filteredReviews = array_filter($reviews, function ($review) use ($minRating) {
            return $review->getStarRating() > $minRating;
        });

        $sortedReviews = array_slice(array_reverse($filteredReviews), 0, $limit);

        // If the number of filtered reviews is less than the limit, add additional reviews
        if (count($sortedReviews) < $limit) {
            $additionalReviews = $this->generateAdditionalReviews($car, $limit - count($sortedReviews), $minRating);
            $sortedReviews = array_merge($sortedReviews, $additionalReviews);
        }

        return $sortedReviews;
    }

    private function generateAdditionalReviews($car, $count, $minRating): array
    {
        $additionalReviews = [];

        for ($i = 0; $i < $count; $i++) {
            $review = new Review();
            $review->setStarRating(mt_rand($minRating + 1, 10));
            $review->setReviewText('Lorem ipsum dolor sit amet.');
            $review->setCar($car);

            $additionalReviews[] = $review;
        }

        return $additionalReviews;
    }
}
