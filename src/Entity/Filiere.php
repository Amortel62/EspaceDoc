<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FiliereRepository")
 */
class Filiere
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
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="filiere")
     */
    private $users;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Theme", mappedBy="filiere")
     */
    private $themes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Departement", inversedBy="filieres")
     */
    private $departement;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeDiplome", inversedBy="filieres")
     */
    private $typediplome;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $libelle;
    

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->themes = new ArrayCollection();
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
      
    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setFiliere($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getFiliere() === $this) {
                $user->setFiliere(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Theme[]
     */
    public function getThemes(): Collection
    {
        return $this->themes;
    }

    public function addTheme(Theme $theme): self
    {
        if (!$this->themes->contains($theme)) {
            $this->themes[] = $theme;
            $theme->setFiliere($this);
        }

        return $this;
    }

    public function removeTheme(Theme $theme): self
    {
        if ($this->themes->contains($theme)) {
            $this->themes->removeElement($theme);
            // set the owning side to null (unless already changed)
            if ($theme->getFiliere() === $this) {
                $theme->setFiliere(null);
            }
        }

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    public function getTypediplome(): ?TypeDiplome
    {
        return $this->typediplome;
    }

    public function setTypediplome(?TypeDiplome $typediplome): self
    {
        $this->typediplome = $typediplome;

        return $this;
    }
}
