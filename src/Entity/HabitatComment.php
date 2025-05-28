<?php

namespace App\Entity;

use App\Repository\HabitatCommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HabitatCommentRepository::class)]
class HabitatComment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Habitat $habitat = null;

    #[ORM\Column(type: Types::TEXT)]
    private string $comment;

    #[ORM\Column]
    private \DateTime $created_at;

    #[ORM\ManyToOne(inversedBy: 'habitatComments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $vet = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getVet(): ?User
    {
        return $this->vet;
    }

    public function setVet(?User $vet): static
    {
        $this->vet = $vet;

        return $this;
    }
}