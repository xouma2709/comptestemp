<?php

namespace App\Entity;

use App\Repository\ComptesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ComptesRepository::class)
 */
class Comptes
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
    private $login;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity=Fonctions::class, inversedBy="comptes")
     */
    private $Fonction;

    /**
     * @ORM\ManyToOne(targetEntity=Softs::class, inversedBy="comptes")
     */
    private $Soft;

    /**
     * @ORM\Column(type="boolean")
     */
    private $IsUsed;

    /**
     * @ORM\OneToOne(targetEntity=Agents::class, mappedBy="Compte", cascade={"persist", "remove"})
     */
    private $agents;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    public function getSoft(): ?Softs
    {
        return $this->Soft;
    }

    public function setSoft(?Softs $Soft): self
    {
        $this->Soft = $Soft;

        return $this;
    }

    public function isIsUsed(): ?bool
    {
        return $this->IsUsed;
    }

    public function setIsUsed(bool $IsUsed): self
    {
        $this->IsUsed = $IsUsed;

        return $this;
    }

    public function getAgents(): ?Agents
    {
        return $this->agents;
    }

    public function setAgents(Agents $agents): self
    {
        // set the owning side of the relation if necessary
        if ($agents->getCompte() !== $this) {
            $agents->setCompte($this);
        }

        $this->agents = $agents;

        return $this;
    }
}
