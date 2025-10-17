<?php

namespace App\Entity;

use App\Repository\AuteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuteurRepository::class)]
class Auteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $biographie = null;

    #[ORM\Column]
    private ?\DateTime $dateNaissance = null;

    /**
     * @var Collection<int, Livre>
     */
    #[ORM\OneToMany(targetEntity: Livre::class, mappedBy: 'id_auteur')]
    private Collection $auteur_id;

    public function __construct()
    {
        $this->auteur_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    public function setBiographie(string $biographie): static
    {
        $this->biographie = $biographie;

        return $this;
    }

    public function getDateNaissance(): ?\DateTime
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTime $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * @return Collection<int, Livre>
     */

    // Ici je me suis un peu trompé dans le nom de mon champs, mais pour éviter de tout casser je le laisse comme ça
    // Il s'agit donc ici de la relation entre Auteur et Livre, plus précisemment du champs correspondant à livre dans l'Entité Auteur
    public function getAuteurId(): Collection
    {
        return $this->auteur_id;
    }

    public function addAuteurId(Livre $auteurId): static
    {
        if (!$this->auteur_id->contains($auteurId)) {
            $this->auteur_id->add($auteurId);
            $auteurId->setIdAuteur($this);
        }

        return $this;
    }

    public function removeAuteurId(Livre $auteurId): static
    {
        if ($this->auteur_id->removeElement($auteurId)) {
            // set the owning side to null (unless already changed)
            if ($auteurId->getIdAuteur() === $this) {
                $auteurId->setIdAuteur(null);
            }
        }

        return $this;
    }
}
