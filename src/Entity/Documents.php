<?php

namespace App\Entity;

use App\Repository\DocumentsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DocumentsRepository::class)
 */
class Documents
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
    private $NomDocument;

    /**
     * @ORM\ManyToOne(targetEntity=Agents::class, inversedBy="documents")
     */
    private $Agent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Attachment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomDocument(): ?string
    {
        return $this->NomDocument;
    }

    public function setNomDocument(string $NomDocument): self
    {
        $this->NomDocument = $NomDocument;

        return $this;
    }

    public function getAgent(): ?Agents
    {
        return $this->Agent;
    }

    public function setAgent(?Agents $Agent): self
    {
        $this->Agent = $Agent;

        return $this;
    }

    public function getAttachment(): ?string
    {
        return $this->Attachment;
    }

    public function setAttachment(?string $Attachment): self
    {
        $this->Attachment = $Attachment;

        return $this;
    }
}
