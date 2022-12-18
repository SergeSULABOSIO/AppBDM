<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $tauxarca = null;

    #[ORM\Column]
    private ?bool $isobligatoire = null;

    #[ORM\Column]
    private ?bool $isabonnement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTauxarca(): ?string
    {
        return $this->tauxarca;
    }

    public function setTauxarca(string $tauxarca): self
    {
        $this->tauxarca = $tauxarca;

        return $this;
    }

    public function isIsobligatoire(): ?bool
    {
        return $this->isobligatoire;
    }

    public function setIsobligatoire(bool $isobligatoire): self
    {
        $this->isobligatoire = $isobligatoire;

        return $this;
    }

    public function isIsabonnement(): ?bool
    {
        return $this->isabonnement;
    }

    public function setIsabonnement(bool $isabonnement): self
    {
        $this->isabonnement = $isabonnement;

        return $this;
    }
}