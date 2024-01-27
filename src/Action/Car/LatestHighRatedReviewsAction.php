<?php

namespace App\Action\Car;

use App\Entity\Car;
use App\Repository\ReviewRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * Action to retrieve the latest high-rated reviews for a car.
 */
#[AsController]
readonly class LatestHighRatedReviewsAction
{
    private ReviewRepository $reviewRepository;

    /**
     * Constructor.
     *
     * @param ReviewRepository $reviewRepository The review repository
     */
    public function __construct(ReviewRepository $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * Retrieves the latest high-rated reviews for the given car.
     *
     * @param Request $request The request object
     * @param Car $data The car entity
     *
     * @return Response The JSON response containing the reviews
     */
    public function __invoke(Request $request, Car $data): Response
    {
        // Get the latest reviews of the given car with a rating above 6 stars
        $reviews = $this->reviewRepository->findLatestHighRatedReviews($data, 5);

        // Create the response
        $response = [];
        foreach ($reviews as $review) {
            $response[] = [
                'id' => $review->getId(),
                'rating' => $review->getStarRating(),
                'comment' => $review->getReviewText(),
            ];
        }

        // Return the response
        return new Response(json_encode($response), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}