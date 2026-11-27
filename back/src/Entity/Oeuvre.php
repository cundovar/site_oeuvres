<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\StatutPublication;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'oeuvre')]
class Oeuvre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private string $slug;

    #[ORM\Column(length: 200)]
    private string $titreFr;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $titreEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $presentationFr = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $presentationEn = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee = null;

    #[ORM\ManyToOne(inversedBy: 'oeuvres')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Serie $serie = null;

    #[ORM\JoinTable(name: 'oeuvre_categorie')]
    #[ORM\JoinColumn(name: 'oeuvre_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'categorie_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'oeuvres')]
    private Collection $categories;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Media $media = null;

    #[ORM\Column(type: Types::STRING, enumType: StatutPublication::class, length: 20)]
    private StatutPublication $statut = StatutPublication::Brouillon;

    #[ORM\Column]
    private bool $visibleIndex = false;

    #[ORM\Column(nullable: true, unique: true)]
    private ?bool $miseEnAvant = null;

    #[ORM\Column]
    private int $ordreIndex = 0;

    #[ORM\Column]
    private int $ordreSerie = 0;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $procedeFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $procedeEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $iterations = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tirageFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tirageEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $format = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $edition = null;

    #[ORM\Column]
    private bool $prixSurDemande = false;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $prix = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombreTotal = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombreRestant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $seoTitreFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $seoTitreEn = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $seoDescriptionFr = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $seoDescriptionEn = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    public function __construct(string $slug, string $titreFr)
    {
        $this->slug = $slug;
        $this->titreFr = $titreFr;
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTitreFr(): string
    {
        return $this->titreFr;
    }

    public function setTitreFr(string $titreFr): static
    {
        $this->titreFr = $titreFr;

        return $this;
    }

    public function getTitreEn(): ?string
    {
        return $this->titreEn;
    }

    public function setTitreEn(?string $titreEn): static
    {
        $this->titreEn = $titreEn;

        return $this;
    }

    public function getPresentationFr(): ?string
    {
        return $this->presentationFr;
    }

    public function setPresentationFr(?string $presentationFr): static
    {
        $this->presentationFr = $presentationFr;

        return $this;
    }

    public function getPresentationEn(): ?string
    {
        return $this->presentationEn;
    }

    public function setPresentationEn(?string $presentationEn): static
    {
        $this->presentationEn = $presentationEn;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(?int $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getSerie(): ?Serie
    {
        return $this->serie;
    }

    public function setSerie(?Serie $serie): static
    {
        if ($this->serie === $serie) {
            return $this;
        }

        if ($this->serie !== null && $serie !== null && $this->serie->getId() !== null
            && $this->serie->getId() === $serie->getId()) {
            return $this;
        }

        $this->serie = $serie;

        return $this;
    }

    /** @return Collection<int, Categorie> */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategorie(Categorie $categorie): static
    {
        if (!$this->categories->contains($categorie)) {
            $this->categories->add($categorie);
            $categorie->ajouterOeuvre($this);
        }

        return $this;
    }

    public function removeCategorie(Categorie $categorie): static
    {
        if ($this->categories->removeElement($categorie)) {
            $categorie->retirerOeuvre($this);
        }

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): static
    {
        $this->media = $media;

        return $this;
    }

    public function getStatut(): StatutPublication
    {
        return $this->statut;
    }

    public function setStatut(StatutPublication $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function isVisibleIndex(): bool
    {
        return $this->visibleIndex;
    }

    public function setVisibleIndex(bool $visibleIndex): static
    {
        $this->visibleIndex = $visibleIndex;

        return $this;
    }

    public function isMiseEnAvant(): bool
    {
        return $this->miseEnAvant === true;
    }

    public function setMiseEnAvant(bool $miseEnAvant): static
    {
        $this->miseEnAvant = $miseEnAvant ? true : null;

        return $this;
    }

    public function getOrdreIndex(): int
    {
        return $this->ordreIndex;
    }

    public function setOrdreIndex(int $ordreIndex): static
    {
        $this->ordreIndex = $ordreIndex;

        return $this;
    }

    public function getOrdreSerie(): int
    {
        return $this->ordreSerie;
    }

    public function setOrdreSerie(int $ordreSerie): static
    {
        $this->ordreSerie = $ordreSerie;

        return $this;
    }

    public function getProcedeFr(): ?string
    {
        return $this->procedeFr;
    }

    public function setProcedeFr(?string $procedeFr): static
    {
        $this->procedeFr = $procedeFr;

        return $this;
    }

    public function getProcedeEn(): ?string
    {
        return $this->procedeEn;
    }

    public function setProcedeEn(?string $procedeEn): static
    {
        $this->procedeEn = $procedeEn;

        return $this;
    }

    public function getIterations(): ?string
    {
        return $this->iterations;
    }

    public function setIterations(?string $iterations): static
    {
        $this->iterations = $iterations;

        return $this;
    }

    public function getTirageFr(): ?string
    {
        return $this->tirageFr;
    }

    public function setTirageFr(?string $tirageFr): static
    {
        $this->tirageFr = $tirageFr;

        return $this;
    }

    public function getTirageEn(): ?string
    {
        return $this->tirageEn;
    }

    public function setTirageEn(?string $tirageEn): static
    {
        $this->tirageEn = $tirageEn;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getEdition(): ?string
    {
        return $this->edition;
    }

    public function setEdition(?string $edition): static
    {
        $this->edition = $edition;

        return $this;
    }

    public function isPrixSurDemande(): bool
    {
        return $this->prixSurDemande;
    }

    public function setPrixSurDemande(bool $prixSurDemande): static
    {
        $this->prixSurDemande = $prixSurDemande;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(?string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getNombreTotal(): ?int
    {
        return $this->nombreTotal;
    }

    public function setNombreTotal(?int $nombreTotal): static
    {
        $this->nombreTotal = $nombreTotal;

        return $this;
    }

    public function getNombreRestant(): ?int
    {
        return $this->nombreRestant;
    }

    public function setNombreRestant(?int $nombreRestant): static
    {
        $this->nombreRestant = $nombreRestant;

        return $this;
    }

    public function getSeoTitreFr(): ?string
    {
        return $this->seoTitreFr;
    }

    public function setSeoTitreFr(?string $seoTitreFr): static
    {
        $this->seoTitreFr = $seoTitreFr;

        return $this;
    }

    public function getSeoTitreEn(): ?string
    {
        return $this->seoTitreEn;
    }

    public function setSeoTitreEn(?string $seoTitreEn): static
    {
        $this->seoTitreEn = $seoTitreEn;

        return $this;
    }

    public function getSeoDescriptionFr(): ?string
    {
        return $this->seoDescriptionFr;
    }

    public function setSeoDescriptionFr(?string $seoDescriptionFr): static
    {
        $this->seoDescriptionFr = $seoDescriptionFr;

        return $this;
    }

    public function getSeoDescriptionEn(): ?string
    {
        return $this->seoDescriptionEn;
    }

    public function setSeoDescriptionEn(?string $seoDescriptionEn): static
    {
        $this->seoDescriptionEn = $seoDescriptionEn;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function mettreALaCorbeille(): static
    {
        $this->deletedAt = new \DateTimeImmutable();

        return $this;
    }

    public function restaurer(): static
    {
        $this->deletedAt = null;

        return $this;
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
