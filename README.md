## Code Challenge

This project is a code-challenge implementation that includes the following features:

| Challenge                                                                                                            | Description                             | Solution                      |
|----------------------------------------------------------------------------------------------------------------------|-----------------------------------------|-------------------------------|
| **Car Entity**: Create the Car entity with the following fields: brand, model, and color.                            | [ description](#car_entity)             | [Solution](http://github.com) |
| **Reviews Entity**: Create the Reviews entity with the following fields: star rating (from 1 to 10) and review text. | [ description](#reviews_entity)         | [Solution](http://github.com) |
| **RESTful API**: Implement a simple RESTful API using Symfony 6 and API Platform 3.                                  | [ description](#restful_api)            | [Solution](http://github.com) |
| **PostgreSQL DB**: Utilize PostgreSQL DB as the database.                                                            | [ description](#postgresql_db)          | [Solution](http://github.com) |
| **PHP 8**: Use PHP 8 as the programming language.                                                                    | [ description](#php_8)                  | [Solution](http://github.com) |
| **Dockerized Environment**: Create a dockerized environment for the app.                                             | [ description](#dockerized_environment) | [Solution](http://github.com) |

To get started with the project, follow the steps below:

``` 
git clone 
cd deployment/setting-developement
bash first_setup.sh
```


 Visit `http://localhost:8002` in your browser to access the application.

If you have any questions or need further assistance, feel free to contact me at [hessamvfx@gmail.com](mailto:hessamvfx@gmail.com).


- [CAR](#car_entity)

1. Create a new file called `Car.php` in the appropriate directory (e.g., `src/Entity`).
2. Define the `Car` class and annotate it with the `@ORM\Entity` annotation to mark it as an entity for the ORM (Object-Relational Mapping) system.
3. I implement Brand and Color entities that every car must have brand and color
4. add Fixtures  to populate the database with initial data for testing or development purposes.In this case, there might be fixtures for the Car, Brand, and Color entities to create sample data for testing the application.
``` 
#[ORM\Entity(repositoryClass: CarRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Put(),
        new Delete(),
        new Get(uriTemplate: '/cars/{id}/reviews/latest-high-rated',controller: LatestHighRatedReviewsAction::class),
        new Post(),
    ],    normalizationContext: [
        'groups' => ['car:read'],
    ]
)]
class Car
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['car:read', 'car:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['car:read', 'car:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'cars')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Brand $Brand = null;

    #[ORM\ManyToOne(inversedBy: 'cars')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Color $Color = null;
```

- [REVIEWS ENTITY](#reviews_entity)

1. Create a new file called `Review.php` in the appropriate directory (e.g., `src/Entity`).
2. Define the `Review` class and annotate it with the `@ORM\Entity` .
3. create ReviewFixtures , The` getLatestReviews` method in the `ReviewFixtures` class retrieves the latest reviews for a given car by filtering and sorting the reviews based on a minimum rating. It returns an array of reviews limited to a specified count, and if the number of filtered reviews is less than the limit, additional reviews are generated using the `generateAdditionalReviews` method.
   The `generateAdditionalReviews` method in the `ReviewFixtures` class generates additional reviews for a given car by creating new instances of the Review entity. It generates the specified count of reviews with random star ratings between the minimum rating + 1 and 10, sets a fixed review text, and associates each review with the car. The generated reviews are returned as an array.
``` 
#[ORM\Entity]
#[ApiResource(
    normalizationContext: ['groups' => ['review:read']],
    denormalizationContext: ['groups' => ['review:write']],
)]
class Review
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['review:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\Range(min: 1, max: 10)]
    #[Groups(['review:read', 'review:write'])]
    private ?int $starRating = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['review:read', 'review:write'])]
    private ?string $reviewText = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['review:read', 'review:write'])]
    private ?Car $car = null;
``` 

- [RESTful API](#restful_api)

  API Platform simplifies the creation of RESTful APIs by automatically generating CRUD (Create, Read, Update, Delete) operations based on the entity definitions. It also provides features like pagination, filtering, sorting, and validation out of the box.
  
- The` Car` entity is annotated with @ApiResource to expose it as a resource in the API. This annotation specifies the available operations for the Car resource, such as GET, POST, PUT, and DELETE. It also defines the normalization and denormalization contexts for serialization/deserialization.
  
  The API endpoints for managing cars and reviews can be accessed using the appropriate HTTP methods and URLs. For example:
  
      - To get a collection of cars: GET /cars
      - To get a specific car: GET /cars/{id}
      - To create a new car: POST /cars
      - To update a car: PUT /cars/{id}
      - To delete a car: DELETE /cars/{id}

Similarly, the endpoints for managing reviews follow a similar pattern.

API Platform also supports custom actions and routes. For example, the provided code includes a custom action to get the latest high-rated reviews for a specific car: GET `/cars/{id}/reviews/latest-high-rated`.

The `LatestHighRatedReviewsAction` class is a Symfony controller class that handles the request to retrieve the latest high-rated reviews for a specific car. It is located in the App\Action\Car namespace.

Here is a breakdown of the important aspects of the` LatestHighRatedReviewsAction` class:
1. `Class Definition`:
  - The LatestHighRatedReviewsAction class is defined as a readonly class, indicating that its properties cannot be modified after instantiation.
  - It is annotated with #[AsController], which is a Symfony attribute that marks the class as a controller.
2. `Constructor`:
- The class has a constructor that accepts an instance of the ReviewRepository class as a parameter.
- The ReviewRepository is a service responsible for retrieving review data from the database.
- The constructor uses PHP 8's property promotion feature to automatically assign the ReviewRepository instance to a private property.
3.  `__invoke Method`:
- The __invoke method is the entry point of the controller and is invoked when the corresponding route is accessed.
- It takes two parameters: a Request object and a Car object.
- The Request object represents the HTTP request made to the API, and the Car object represents the car for which the reviews are requested.
- The method returns a Response object, which represents the HTTP response to be sent back to the client.
4. `Retrieving Latest High-Rated Reviews`:
- Inside the __invoke method, the findLatestHighRatedReviews method of the ReviewRepository is called to retrieve the latest high-rated reviews for the given car.
- The findLatestHighRatedReviews method takes two parameters: the Car object and a limit for the number of reviews to retrieve.
- The method returns an array of review objects.
5. `Creating the Response:`
- After retrieving the reviews, the method creates a response array to hold the review data.
- It iterates over each review and extracts the relevant information such as the review ID, star rating, and review text.
- The extracted information is added to the response array.
6. `Returning the Response`:
- Finally, the method returns a new Response object with the JSON-encoded response array.
- The response has an HTTP status code of 200 (OK) and a content type of application/json.
- The LatestHighRatedReviewsAction class serves as a controller that handles the request to retrieve the latest high-rated reviews for a specific car. It utilizes the ReviewRepository to fetch the reviews from the database, constructs a response array with the relevant review information, and returns a JSON-encoded response to the client.

API Platform also supports custom actions and routes. For example, the provided code includes a custom action to get the latest high-rated reviews for a specific car: GET /cars/{id}/reviews/latest-high-rated.

The LatestHighRatedReviewsAction class is a Symfony controller class that handles the request to retrieve the latest high-rated reviews for a specific car. It is located in the App\Action\Car namespace.

Post `/cars/{id}/reviews`

The AddReviewToCarAction is a class that handles the process of adding a review to a car in a Symfony API Platform application. It is responsible for receiving a request, validating the data, associating the review with the specified car, and persisting the review in the database.

- 'Request Handling:' The method starts by decoding the JSON content of the request using $request->getContent() and json_decode(). This allows the review data to be extracted from the request payload.
- `Form Creation and Submission`: The method creates a new instance of the Review entity and a form using the form factory. The form is created using the ReviewType form type, which defines the structure and validation rules for the review data. The form is then submitted with the decoded data using $form->submit($data).
- `Form Validation`: The method checks if the form is submitted and valid using $form->isSubmitted() and $form->isValid(). If the form is valid, the review is associated with the specified car entity using $review->setCar($car).
- `Database Persistence`: If the form is valid, the review entity is persisted in the database using the entity manager. The review is added to the entity manager's persistence queue using $this->entityManager->persist($review), and the changes are flushed to the database using $this->entityManager->flush().
- `Response Handling`: Depending on the outcome of the form validation, the method returns an appropriate response. If the form is valid, a success response with a status code of 201 Created is returned. If the form is invalid, a JSON response with the validation errors and a status code of 400 Bad Request is returned.


- [POSTGRESQL DB](#postgresql_db)
  DATABASE_URL="postgresql://rating_user:123456@rating-postgres:5432/rating_db?serverVersion=16&charset=utf8"
    yaml doctrine: dbal: # ... driver: 'pdo_pgsql'
To run test :
``` 
docker exec -it rating-php   ./vendor/bin/phpunit
```
---


## API Tests

The `ApiTest` class contains a set of test methods that verify the functionality of the API endpoints for the car review system. These tests are implemented using the Symfony testing framework.

### `testGetCars()`

This test method verifies the `GET /api/cars` endpoint, which retrieves a list of cars. It checks that the response status code is 200 (OK), and that the response body contains the expected keys and values.

### `testCreateACar()`

This test method verifies the `POST /api/cars` endpoint, which creates a new car. It sends a POST request with the car data and checks that the response status code is 201 (Created), and that the response body contains the expected keys and values.

### `testUpdateNameInCar()`

This test method verifies the `PATCH /api/cars/{carId}` endpoint, which updates the name of a car. It sends a PATCH request with the updated car data and checks that the response status code is 200 (OK), and that the response body contains the updated name.

### `testDeleteACar()`

This test method verifies the `DELETE /api/cars/{carId}` endpoint, which deletes a car. It sends a DELETE request and checks that the response status code is 204 (No Content), indicating a successful deletion.

### `testAddReviewToACar()`

This test method verifies the `POST /api/cars/{carId}/reviews` endpoint, which adds a review to a car. It sends a POST request with the review data and checks that the response status code is 201 (Created), indicating a successful addition.

### `testHighRatedReviewACar()`

This test method verifies the `GET /api/cars/{carId}/reviews/latest-high-rated` endpoint, which retrieves the latest high-rated reviews for a car. It checks that the response status code is 200 (OK), and that the response body contains the expected number of reviews.

These test methods ensure that the API endpoints for the car review system are functioning correctly and returning the expected results. They can be run using the Symfony testing framework to validate the system's behavior.


