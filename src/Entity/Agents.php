<?php

namespace App\Entity;

use App\Repository\AgentsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AgentsRepository::class)
 */
class Agents
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Prenom;

    /**
     * @ORM\Column(type="date")
     */
    private $DateDebut;

    /**
     * @ORM\Column(type="date")
     */
    private $DateFin;

    /**
     * @ORM\ManyToOne(targetEntity=Softs::class, inversedBy="Softs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Softs;

    /**
     * @ORM\ManyToOne(targetEntity=Fonctions::class, inversedBy="agents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Fonction;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Secteur;

    /**
     * @ORM\OneToOne(targetEntity=Comptes::class, inversedBy="agents", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $Compte;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateDemande;

    /**
     * @ORM\OneToMany(targetEntity=Documents::class, mappedBy="Agent")
     */
    private $documents;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPJ;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Demandeur;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }

    public function setPrenom(string $Prenom): self
    {
        $this->Prenom = $Prenom;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->DateDebut;
    }

    public function setDateDebut(\DateTimeInterface $DateDebut): self
    {
        $this->DateDebut = $DateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->DateFin;
    }

    public function setDateFin(\DateTimeInterface $DateFin): self
    {
        $this->DateFin = $DateFin;

        return $this;
    }

    public function getSofts(): ?Softs
    {
        return $this->Softs;
    }

    public function setSofts(?Softs $Softs): self
    {
        $this->Softs = $Softs;

        return $this;
    }

    public function getFonction(): ?Fonctions
    {
        return $this->Fonction;
    }

    public function setFonction(?Fonctions $Fonction): self
    {
        $this->Fonction = $Fonction;

        return $this;
    }

    public function getSecteur(): ?string
    {
        return $this->Secteur;
    }

    public function setSecteur(?string $Secteur): self
    {
        $this->Secteur = $Secteur;

        return $this;
    }

    public function getCompte(): ?Comptes
    {
        return $this->Compte;
    }

    public function setCompte(Comptes $Compte): self
    {
        $this->Compte = $Compte;

        return $this;
    }

    public function getDateDemande(): ?\DateTimeInterface
    {
        return $this->dateDemande;
    }

    public function setDateDemande(\DateTimeInterface $dateDemande): self
    {
        $this->dateDemande = $dateDemande;

        return $this;
    }

    /**
     * @return Collection<int, Documents>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Documents $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setAgent($this);
        }

        return $this;
    }

    public function removeDocument(Documents $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getAgent() === $this) {
                $document->setAgent(null);
            }
        }

        return $this;
    }

    public function isIsPJ(): ?bool
    {
        return $this->isPJ;
    }

    public function setIsPJ(bool $isPJ): self
    {
        $this->isPJ = $isPJ;

        return $this;
    }

    public function getDemandeur(): ?string
    {
        return $this->Demandeur;
    }

    public function setDemandeur(string $Demandeur): self
    {
        $this->Demandeur = $Demandeur;

        return $this;
    }
}
