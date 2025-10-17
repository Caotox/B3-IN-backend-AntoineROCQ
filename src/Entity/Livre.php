<?php

namespace App\Entity;

use App\Repository\LivreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LivreRepository::class)]
class Livre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column]
    private ?\DateTime $datePublication = null;

    #[ORM\Column]
    private ?bool $disponible = null;

    #[ORM\ManyToOne(inversedBy: 'auteur_id')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Auteur $id_auteur = null;

    #[ORM\ManyToOne(inversedBy: 'livre_id')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\OneToOne(mappedBy: 'id_livre', cascade: ['persist', 'remove'])]
    private ?Emprunt $emprunt = null;

    #[ORM\OneToOne(mappedBy: 'disponible', cascade: ['persist', 'remove'])]
    private ?Emprunt $emprunt_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDatePublication(): ?\DateTime
    {
        return $this->datePublication;
    }

    public function setDatePublication(\DateTime $datePublication): static
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    public function isDisponible(): ?bool
    {
        return $this->disponible;
    }

    public function setDisponible(bool $disponible): static
    {
        $this->disponible = $disponible;

        return $this;
    }

    public function getIdAuteur(): ?Auteur
    {
        return $this->id_auteur;
    }

    public function setIdAuteur(?Auteur $id_auteur): static
    {
        $this->id_auteur = $id_auteur;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getEmprunt(): ?Emprunt
    {
        return $this->emprunt;
    }

    public function setEmprunt(?Emprunt $emprunt): static
    {
        // unset the owning side of the relation if necessary
        if ($emprunt === null && $this->emprunt !== null) {
            $this->emprunt->setIdLivre(null);
        }

        // set the owning side of the relation if necessary
        if ($emprunt !== null && $emprunt->getIdLivre() !== $this) {
            $emprunt->setIdLivre($this);
        }

        $this->emprunt = $emprunt;

        return $this;
    }

    public function getEmpruntId(): ?Emprunt
    {
        return $this->emprunt_id;
    }

    public function setEmpruntId(?Emprunt $emprunt_id): static
    {
        // unset the owning side of the relation if necessary
        if ($emprunt_id === null && $this->emprunt_id !== null) {
            $this->emprunt_id->setDisponible(null);
        }

        // set the owning side of the relation if necessary
        if ($emprunt_id !== null && $emprunt_id->getDisponible() !== $this) {
            $emprunt_id->setDisponible($this);
        }

        $this->emprunt_id = $emprunt_id;

        return $this;
    }
}
