<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    /**
     * @var Collection<int, Livre>
     */
    #[ORM\OneToMany(targetEntity: Livre::class, mappedBy: 'categorie')]
    private Collection $livre_id;

    public function __construct()
    {
        $this->livre_id = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Livre>
     */
    public function getLivreId(): Collection
    {
        return $this->livre_id;
    }

    public function addLivreId(Livre $livreId): static
    {
        if (!$this->livre_id->contains($livreId)) {
            $this->livre_id->add($livreId);
            $livreId->setCategorie($this);
        }

        return $this;
    }

    public function removeLivreId(Livre $livreId): static
    {
        if ($this->livre_id->removeElement($livreId)) {
            // set the owning side to null (unless already changed)
            if ($livreId->getCategorie() === $this) {
                $livreId->setCategorie(null);
            }
        }

        return $this;
    }
}
