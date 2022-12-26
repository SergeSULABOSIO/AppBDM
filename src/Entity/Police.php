<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\PoliceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PoliceRepository::class)]
class Police
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message:"Veuillez préciser la date.")]
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateoperation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateemission = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateeffet = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateexpiration = null;

    #[Assert\NotBlank(message:"Veuillez fournir la référence de la police.")]
    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[Assert\NotBlank(message:"Veuillez préciser l'ID de cet avenant.")]
    #[ORM\Column]
    private ?int $idavenant = null;

    #[ORM\Column(length: 255)]
    private ?string $typeavenant = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $capital = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $primenette = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $fronting = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $arca = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $tva = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $fraisadmin = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $primetotale = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $discount = null;

    #[ORM\Column(length: 255)]
    private ?string $modepaiement = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $ricom = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $localcom = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $frontingcom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $remarques = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Entreprise $entreprise = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Monnaie $monnaie = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    #[ORM\ManyToMany(targetEntity: Assureur::class)]
    private Collection $assureurs;

    #[ORM\ManyToOne]
    private ?Partenaire $partenaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reassureurs = null;

    public function __construct()
    {
        $this->assureurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateoperation(): ?\DateTimeInterface
    {
        return $this->dateoperation;
    }

    public function setDateoperation(\DateTimeInterface $dateoperation): self
    {
        $this->dateoperation = $dateoperation;

        return $this;
    }

    public function getDateemission(): ?\DateTimeInterface
    {
        return $this->dateemission;
    }

    public function setDateemission(?\DateTimeInterface $dateemission): self
    {
        $this->dateemission = $dateemission;

        return $this;
    }

    public function getDateeffet(): ?\DateTimeInterface
    {
        return $this->dateeffet;
    }

    public function setDateeffet(?\DateTimeInterface $dateeffet): self
    {
        $this->dateeffet = $dateeffet;

        return $this;
    }

    public function getDateexpiration(): ?\DateTimeInterface
    {
        return $this->dateexpiration;
    }

    public function setDateexpiration(\DateTimeInterface $dateexpiration): self
    {
        $this->dateexpiration = $dateexpiration;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getIdavenant(): ?int
    {
        return $this->idavenant;
    }

    public function setIdavenant(int $idavenant): self
    {
        $this->idavenant = $idavenant;

        return $this;
    }

    public function getTypeavenant(): ?string
    {
        return $this->typeavenant;
    }

    public function setTypeavenant(string $typeavenant): self
    {
        $this->typeavenant = $typeavenant;

        return $this;
    }

    public function getCapital(): ?string
    {
        return $this->capital;
    }

    public function setCapital(string $capital): self
    {
        $this->capital = $capital;

        return $this;
    }

    public function getPrimenette(): ?string
    {
        return $this->primenette;
    }

    public function setPrimenette(string $primenette): self
    {
        $this->primenette = $primenette;

        return $this;
    }

    public function getFronting(): ?string
    {
        return $this->fronting;
    }

    public function setFronting(string $fronting): self
    {
        $this->fronting = $fronting;

        return $this;
    }

    public function getArca(): ?string
    {
        return $this->arca;
    }

    public function setArca(string $arca): self
    {
        $this->arca = $arca;

        return $this;
    }

    public function getTva(): ?string
    {
        return $this->tva;
    }

    public function setTva(string $tva): self
    {
        $this->tva = $tva;

        return $this;
    }

    public function getFraisadmin(): ?string
    {
        return $this->fraisadmin;
    }

    public function setFraisadmin(string $fraisadmin): self
    {
        $this->fraisadmin = $fraisadmin;

        return $this;
    }

    public function getPrimetotale(): ?string
    {
        return $this->primetotale;
    }

    public function setPrimetotale(string $primetotale): self
    {
        $this->primetotale = $primetotale;

        return $this;
    }

    public function getDiscount(): ?string
    {
        return $this->discount;
    }

    public function setDiscount(string $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function getModepaiement(): ?string
    {
        return $this->modepaiement;
    }

    public function setModepaiement(string $modepaiement): self
    {
        $this->modepaiement = $modepaiement;

        return $this;
    }

    public function getRicom(): ?string
    {
        return $this->ricom;
    }

    public function setRicom(string $ricom): self
    {
        $this->ricom = $ricom;

        return $this;
    }

    public function getLocalcom(): ?string
    {
        return $this->localcom;
    }

    public function setLocalcom(string $localcom): self
    {
        $this->localcom = $localcom;

        return $this;
    }

    public function getFrontingcom(): ?string
    {
        return $this->frontingcom;
    }

    public function setFrontingcom(string $frontingcom): self
    {
        $this->frontingcom = $frontingcom;

        return $this;
    }

    public function getRemarques(): ?string
    {
        return $this->remarques;
    }

    public function setRemarques(?string $remarques): self
    {
        $this->remarques = $remarques;

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

    public function getMonnaie(): ?Monnaie
    {
        return $this->monnaie;
    }

    public function setMonnaie(?Monnaie $monnaie): self
    {
        $this->monnaie = $monnaie;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    /**
     * @return Collection<int, Assureur>
     */
    public function getAssureurs(): Collection
    {
        return $this->assureurs;
    }

    public function addAssureur(Assureur $assureur): self
    {
        if (!$this->assureurs->contains($assureur)) {
            $this->assureurs->add($assureur);
        }

        return $this;
    }

    public function removeAssureur(Assureur $assureur): self
    {
        $this->assureurs->removeElement($assureur);

        return $this;
    }

    public function getPartenaire(): ?Partenaire
    {
        return $this->partenaire;
    }

    public function setPartenaire(?Partenaire $partenaire): self
    {
        $this->partenaire = $partenaire;

        return $this;
    }

    public function __toString()
    {
        return $this->reference;
    }

    public function getReassureurs(): ?string
    {
        return $this->reassureurs;
    }

    public function setReassureurs(?string $reassureurs): self
    {
        $this->reassureurs = $reassureurs;

        return $this;
    }
}
