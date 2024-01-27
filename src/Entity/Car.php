<?php

namespace App\Entity;

use AllowDynamicProperties;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use App\Action\Car\AddReviewToCarAction;
use App\Action\Car\LatestHighRatedReviewsAction;
use App\Repository\CarRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

#[AllowDynamicProperties] #[ORM\Entity(repositoryClass: CarRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Patch(),
        new Put(),
        new Delete(),
        new Post(validationContext: ['groups' => ['car:post']]),
        new Get(uriTemplate: '/cars/{id}/reviews/latest-high-rated', controller: LatestHighRatedReviewsAction::class),
        new Post(uriTemplate: '/cars/{id}/reviews', controller: AddReviewToCarAction::class),
    ],
    normalizationContext: [
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
    #[Groups(['car:read', 'car:write','car:update'])]
    #[Assert\NotBlank(groups: ['car:post'])]
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

    #[ORM\Column(length: 255)]
    #[Groups(['car:read', 'car:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $model = null;
    #[ORM\OneToMany(mappedBy: 'car', targetEntity: Review::class)]
    private Collection $reviews;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    #[Groups(['car:read', 'car:write'])]
    public function getBrand(): ?Brand
    {
        return $this->Brand;
    }
    /**
     * @Groups({"car:write"})
     */
    public function setBrand(?Brand $Brand): static
    {
        $this->Brand = $Brand;

        return $this;
    }
    #[Groups(['car:read', 'car:write'])]
    public function getColor(): ?Color
    {
        return $this->Color;
    }
    /**
     * @Groups({"car:write"})
     */
    public function setColor(?Color $Color): static
    {
        $this->Color = $Color;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }
}
