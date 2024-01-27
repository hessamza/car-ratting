<?php

namespace App\Action\Car;

use App\Entity\Car;
use App\Entity\Review;
use App\Form\Type\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * Action to add a review to a car.
 */
#[AsController]
class AddReviewToCarAction
{
    private FormFactoryInterface $formFactory;
    private EntityManagerInterface $entityManager;

    /**
     * Constructor.
     *
     * @param FormFactoryInterface $formFactory The form factory
     * @param EntityManagerInterface $entityManager The entity manager
     */
    public function __construct(FormFactoryInterface $formFactory, EntityManagerInterface $entityManager)
    {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * Adds a review to the specified car.
     *
     * @param Request $request The request object
     * @param Car $car The car entity
     *
     * @return JsonResponse The JSON response indicating the success or validation errors
     */
    public function __invoke(Request $request, Car $car): JsonResponse
    {
        // Decode the request content to get the review data
        $data = json_decode($request->getContent(), true);

        // Create a new review entity
        $review = new Review();

        // Create the review form and submit the data
        $form = $this->formFactory->create(ReviewType::class, $review);
        $form->submit($data);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Set the car for the review
            $review->setCar($car);

            // Persist the review in the database
            $this->entityManager->persist($review);
            $this->entityManager->flush();

            // Return a success response
            return new JsonResponse('Review added successfully', Response::HTTP_CREATED);
        }

        // Handle the validation errors
        $errors = $this->getErrorsFromForm($form);

        // Return a JSON response with the validation errors
        return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Retrieves the validation errors from a form.
     *
     * @param FormInterface $form The form
     *
     * @return array The validation errors
     */
    private function getErrorsFromForm(FormInterface $form): array
    {
        $errors = [];

        // Iterate over each form field and collect the errors
        foreach ($form->getErrors(true, true) as $error) {
            $errors[] = $error->getMessage();
        }

        return $errors;
    }
}