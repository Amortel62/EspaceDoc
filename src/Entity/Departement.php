<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DepartementRepository")
 */
class Departement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mail_contact;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Filiere", mappedBy="departement")
     */
    private $filieres;


    public function __construct()
    {
        $this->filieres = new ArrayCollection();
    }

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

    public function getMailContact(): ?string
    {
        return $this->mail_contact;
    }

    public function setMailContact(?string $mail_contact): self
    {
        $this->mail_contact = $mail_contact;

        return $this;
    }

    /**
     * @return Collection|Filiere[]
     */
    public function getFilieres(): Collection
    {
        return $this->filieres;
    }

    public function addFiliere(Filiere $filiere): self
    {
        if (!$this->filieres->contains($filiere)) {
            $this->filieres[] = $filiere;
            $filiere->setDepartement($this);
        }

        return $this;
    }

    public function removeFiliere(Filiere $filiere): self
    {
        if ($this->filieres->contains($filiere)) {
            $this->filieres->removeElement($filiere);
            // set the owning side to null (unless already changed)
            if ($filiere->getDepartement() === $this) {
                $filiere->setDepartement(null);
            }
        }

        return $this;
    }
}
