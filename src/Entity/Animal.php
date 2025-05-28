<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private string $name;

    #[ORM\Column(length: 100)]
    private string $breed;

    #[ORM\ManyToOne(inversedBy: 'animals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Habitat $habitat = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $images = null;

    /**
     * @var Collection<int, Report>
     */
    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'animal')]
    private Collection $reports;

    /**
     * @var Collection<int, Feeding>
     */
    #[ORM\OneToMany(targetEntity: Feeding::class, mappedBy: 'animal')]
    private Collection $feedings;

    public function __construct()
    {
        $this->reports = new ArrayCollection();
        $this->feedings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBreed(): string
    {
        return $this->breed;
    }

    public function setBreed(string $breed): static
    {
        $this->breed = $breed;

        return $this;
    }

    public function getHabitat(): ?Habitat
    {
        return $this->habitat;
    }

    public function setHabitat(?Habitat $habitat): static
    {
        $this->habitat = $habitat;

        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): static
    {
        $this->images = $images;

        return $this;
    }

    /*
     * The following method was removed because the 'id' is an auto-incremented integer
     * managed by MySQL, and should not be manually set. The original method incorrectly
     * used 'string' instead of 'int'.
     *
     * public function setId(string $id): static
     * {
     *     $this->id = $id;
     *     return $this;
     * }
     */

    /**
     * @return Collection<int, Report>
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): static
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->setAnimal($this);
        }

        return $this;
    }

    public function removeReport(Report $report): static
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getAnimal() === $this) {
                $report->setAnimal(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Feeding>
     */
    public function getFeedings(): Collection
    {
        return $this->feedings;
    }

    public function addFeeding(Feeding $feeding): static
    {
        if (!$this->feedings->contains($feeding)) {
            $this->feedings->add($feeding);
            $feeding->setAnimal($this);
        }

        return $this;
    }

    public function removeFeeding(Feeding $feeding): static
    {
        if ($this->feedings->removeElement($feeding)) {
            // set the owning side to null (unless already changed)
            if ($feeding->getAnimal() === $this) {
                $feeding->setAnimal(null);
            }
        }

        return $this;
    }
}