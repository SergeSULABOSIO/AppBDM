<?php

namespace App\Entity;


use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\PaiementTaxeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementTaxeRepository::class)]
class PaiementTaxe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $exercice = null;

    #[Assert\NotBlank(message:"Ce champ ne peut pas être vide.")]
    #[ORM\Column(length: 255)]
    private ?string $refnotededebit = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Entreprise $entreprise = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getExercice(): ?string
    {
        return $this->exercice;
    }

    public function setExercice(?string $exercice): self
    {
        $this->exercice = $exercice;

        return $this;
    }

    public function getRefnotededebit(): ?string
    {
        return $this->refnotededebit;
    }

    public function setRefnotededebit(string $refnotededebit): self
    {
        $this->refnotededebit = $refnotededebit;

        return $this;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function __toString()
    {
        return $this->montant . " - " . $this->refnotededebit;
    }
}
