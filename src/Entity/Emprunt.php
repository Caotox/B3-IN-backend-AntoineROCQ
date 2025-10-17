<?php

namespace App\Entity;

use App\Repository\EmpruntRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmpruntRepository::class)]
class Emprunt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'emprunt', cascade: ['persist', 'remove'])]
    private ?Livre $id_livre = null;

    #[ORM\OneToOne(inversedBy: 'emprunt_id', cascade: ['persist', 'remove'])]
    private ?Livre $disponible = null;

    #[ORM\ManyToOne(inversedBy: 'emprunts')]
    private ?Utilisateur $utilisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getIdLivre(): ?Livre
    {
        return $this->id_livre;
    }

    public function setIdLivre(?Livre $id_livre): static
    {
        $this->id_livre = $id_livre;

        return $this;
    }

    public function getDisponible(): ?Livre
    {
        return $this->disponible;
    }

    public function setDisponible(?Livre $disponible): static
    {
        $this->disponible = $disponible;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}
