<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'report')]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Animal::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Animal $animal = null;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $status = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $food = null;

    #[ORM\Column(type: 'integer')]
    private ?int $food_quantity = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $visit_date = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $details = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'vet_id', referencedColumnName: 'id', nullable: false)]
    private ?User $vet = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnimal(): ?Animal
    {
        return $this->animal;
    }

    public function setAnimal(?Animal $animal): self
    {
        $this->animal = $animal;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getFood(): ?string
    {
        return $this->food;
    }

    public function setFood(string $food): self
    {
        $this->food = $food;
        return $this;
    }

    public function getFoodQuantity(): ?int
    {
        return $this->food_quantity;
    }

    public function setFoodQuantity(int $food_quantity): self
    {
        $this->food_quantity = $food_quantity;
        return $this;
    }

    public function getVisitDate(): ?\DateTimeInterface
    {
        return $this->visit_date;
    }

    public function setVisitDate(\DateTimeInterface $visit_date): self
    {
        $this->visit_date = $visit_date;
        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;
        return $this;
    }

    public function getVet(): ?User
    {
        return $this->vet;
    }

    public function setVet(?User $vet): self
    {
        $this->vet = $vet;
        return $this;
    }
}