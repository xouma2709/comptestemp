<?php

namespace App\Entity;

use App\Repository\SoftsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SoftsRepository::class)
 */
class Softs
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
    private $LibelleSoft;

    /**
     * @ORM\OneToMany(targetEntity=Agents::class, mappedBy="Agents")
     */
    private $Agents;

    /**
     * @ORM\OneToMany(targetEntity=Comptes::class, mappedBy="Soft")
     */
    private $comptes;


    public function __construct()
    {
        $this->Agents = new ArrayCollection();
        $this->comptes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleSoft(): ?string
    {
        return $this->LibelleSoft;
    }

    public function setLibelleSoft(string $LibelleSoft): self
    {
        $this->LibelleSoft = $LibelleSoft;

        return $this;
    }

    /**
     * @return Collection<int, Agents>
     */
    public function getAgents(): Collection
    {
        return $this->Agents;
    }

    public function addAgents(Agents $Agents): self
    {
        if (!$this->Agents->contains($Agents)) {
            $this->Agents[] = $Agents;
            $Agents->setAgents($this);
        }

        return $this;
    }

    public function removeAgents(Agents $Agents): self
    {
        if ($this->Agents->removeElement($Agents)) {
            // set the owning side to null (unless already changed)
            if ($Agents->getAgents() === $this) {
                $Agents->setAgents(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comptes>
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Comptes $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes[] = $compte;
            $compte->setSoft($this);
        }

        return $this;
    }

    public function removeCompte(Comptes $compte): self
    {
        if ($this->comptes->removeElement($compte)) {
            // set the owning side to null (unless already changed)
            if ($compte->getSoft() === $this) {
                $compte->setSoft(null);
            }
        }

        return $this;
    }

}
