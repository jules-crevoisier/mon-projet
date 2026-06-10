<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\VoitureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VoitureRepository::class)]
#[ORM\Table(name: 'voiture')]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Put(),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['voiture:read']],
    denormalizationContext: ['groups' => ['voiture:write']],
)]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['voiture:read', 'marque:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    #[Groups(['voiture:read', 'voiture:write', 'marque:read'])]
    private ?string $modele = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Positive]
    #[Groups(['voiture:read', 'voiture:write', 'marque:read'])]
    private ?string $prix = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Positive]
    #[Groups(['voiture:read', 'voiture:write', 'marque:read'])]
    private ?int $puissance = null;

    #[ORM\Column(name: 'annee_sortie')]
    #[Assert\NotNull]
    #[Assert\Range(min: 1900, max: 2100)]
    #[Groups(['voiture:read', 'voiture:write', 'marque:read'])]
    private ?int $anneeSortie = null;

    #[ORM\Column(length: 500, nullable: true)]
    #[Assert\Url]
    #[Assert\Length(max: 500)]
    #[Groups(['voiture:read', 'voiture:write', 'marque:read'])]
    private ?string $photo = null;

    #[ORM\ManyToOne(inversedBy: 'voitures')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['voiture:read', 'voiture:write'])]
    private ?Marque $marque = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): static
    {
        $this->modele = $modele;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getPuissance(): ?int
    {
        return $this->puissance;
    }

    public function setPuissance(int $puissance): static
    {
        $this->puissance = $puissance;

        return $this;
    }

    public function getAnneeSortie(): ?int
    {
        return $this->anneeSortie;
    }

    public function setAnneeSortie(int $anneeSortie): static
    {
        $this->anneeSortie = $anneeSortie;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getMarque(): ?Marque
    {
        return $this->marque;
    }

    public function setMarque(?Marque $marque): static
    {
        $this->marque = $marque;

        return $this;
    }
}
