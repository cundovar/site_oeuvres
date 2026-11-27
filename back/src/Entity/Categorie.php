<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'categorie')]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private string $nomFr;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $nomEn = null;

    #[ORM\Column]
    private bool $dansFiltres = true;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    /** @var Collection<int, Oeuvre> */
    #[ORM\ManyToMany(mappedBy: 'categories', targetEntity: Oeuvre::class)]
    private Collection $oeuvres;

    public function __construct(string $nomFr)
    {
        $this->nomFr = $nomFr;
        $this->oeuvres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomFr(): string
    {
        return $this->nomFr;
    }

    public function setNomFr(string $nomFr): static
    {
        $this->nomFr = $nomFr;

        return $this;
    }

    public function getNomEn(): ?string
    {
        return $this->nomEn;
    }

    public function setNomEn(?string $nomEn): static
    {
        $this->nomEn = $nomEn;

        return $this;
    }

    public function isDansFiltres(): bool
    {
        return $this->dansFiltres;
    }

    public function setDansFiltres(bool $dansFiltres): static
    {
        $this->dansFiltres = $dansFiltres;

        return $this;
    }

    /** @return Collection<int, Oeuvre> */
    public function getOeuvres(): Collection
    {
        return $this->oeuvres;
    }

    public function ajouterOeuvre(Oeuvre $oeuvre): void
    {
        if (!$oeuvre->getCategories()->contains($this)) {
            $oeuvre->addCategorie($this);
        }
    }

    public function retirerOeuvre(Oeuvre $oeuvre): void
    {
        if ($oeuvre->getCategories()->contains($this)) {
            $oeuvre->removeCategorie($this);
        }
    }

    public function recevoirOeuvre(Oeuvre $oeuvre): void
    {
        if ($this->collectionModifiable() && !$this->oeuvres->contains($oeuvre)) {
            $this->oeuvres->add($oeuvre);
        }
    }

    public function oublierOeuvre(Oeuvre $oeuvre): void
    {
        if ($this->collectionModifiable()) {
            $this->oeuvres->removeElement($oeuvre);
        }
    }

    private function collectionModifiable(): bool
    {
        return !($this->oeuvres instanceof PersistentCollection && !$this->oeuvres->isInitialized());
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    public function horodaterCreation(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function horodaterModification(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
