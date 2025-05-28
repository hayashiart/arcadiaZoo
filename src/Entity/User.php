<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: "email_unique", columns: ["email"])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private string $email;

    #[ORM\Column(length: 255)]
    private string $password;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTime $created_at;

    /**
     * @var Collection<int, Feeding>
     */
    #[ORM\OneToMany(targetEntity: Feeding::class, mappedBy: 'employee')]
    private Collection $feedings;

    /**
     * @var Collection<int, HabitatComment>
     */
    #[ORM\OneToMany(targetEntity: HabitatComment::class, mappedBy: 'vet')]
    private Collection $habitatComments;

    /**
     * @var Collection<int, Report>
     */
    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'vet')]
    private Collection $reports;

    public function __construct()
    {
        $this->feedings = new ArrayCollection();
        $this->habitatComments = new ArrayCollection();
        $this->reports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // Garantir au moins un rôle par défaut
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
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
            $feeding->setEmployee($this);
        }
        return $this;
    }

    public function removeFeeding(Feeding $feeding): static
    {
        if ($this->feedings->removeElement($feeding)) {
            if ($feeding->getEmployee() === $this) {
                $feeding->setEmployee(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, HabitatComment>
     */
    public function getHabitatComments(): Collection
    {
        return $this->habitatComments;
    }

    public function addHabitatComment(HabitatComment $habitatComment): static
    {
        if (!$this->habitatComments->contains($habitatComment)) {
            $this->habitatComments->add($habitatComment);
            $habitatComment->setVet($this);
        }
        return $this;
    }

    public function removeHabitatComment(HabitatComment $habitatComment): static
    {
        if ($this->habitatComments->removeElement($habitatComment)) {
            if ($habitatComment->getVet() === $this) {
                $habitatComment->setVet(null);
            }
        }
        return $this;
    }

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
            $report->setVet($this);
        }
        return $this;
    }

    public function removeReport(Report $report): static
    {
        if ($this->reports->removeElement($report)) {
            if ($report->getVet() === $this) {
                $report->setVet(null);
            }
        }
        return $this;
    }

    // Méthodes requises par UserInterface
    public function eraseCredentials(): void
    {
        // Effacer les données sensibles si nécessaire (non utilisé ici)
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}