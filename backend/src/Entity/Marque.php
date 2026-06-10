<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\MarqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MarqueRepository::class)]
#[ORM\Table(name: 'marque')]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Put(),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['marque:read']],
    denormalizationContext: ['groups' => ['marque:write']],
)]
class Marque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['marque:read', 'voiture:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Groups(['marque:read', 'marque:write', 'voiture:read'])]
    private ?string $nom = null;

    #[ORM\Column(name: 'annee_creation')]
    #[Assert\NotNull]
    #[Assert\Range(min: 1800, max: 2100)]
    #[Groups(['marque:read', 'marque:write', 'voiture:read'])]
    private ?int $anneeCreation = null;

    #[ORM\Column(name: 'pays_origine', length: 80)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 80)]
    #[Groups(['marque:read', 'marque:write', 'voiture:read'])]
    private ?string $paysOrigine = null;

    /** @var Collection<int, Voiture> */
    #[ORM\OneToMany(targetEntity: Voiture::class, mappedBy: 'marque', orphanRemoval: true, cascade: ['persist'])]
    #[Groups(['marque:read'])]
    private Collection $voitures;

    public function __construct()
    {
        $this->voitures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAnneeCreation(): ?int
    {
        return $this->anneeCreation;
    }

    public function setAnneeCreation(int $anneeCreation): static
    {
        $this->anneeCreation = $anneeCreation;

        return $this;
    }

    public function getPaysOrigine(): ?string
    {
        return $this->paysOrigine;
    }

    public function setPaysOrigine(string $paysOrigine): static
    {
        $this->paysOrigine = $paysOrigine;

        return $this;
    }

    /** @return Collection<int, Voiture> */
    public function getVoitures(): Collection
    {
        return $this->voitures;
    }

    public function addVoiture(Voiture $voiture): static
    {
        if (!$this->voitures->contains($voiture)) {
            $this->voitures->add($voiture);
            $voiture->setMarque($this);
        }

        return $this;
    }

    public function removeVoiture(Voiture $voiture): static
    {
        if ($this->voitures->removeElement($voiture)) {
            if ($voiture->getMarque() === $this) {
                $voiture->setMarque(null);
            }
        }

        return $this;
    }
}
