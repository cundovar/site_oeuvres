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
#[ORM\Table(name: 'serie')]
class Serie
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
    private ?string $introductionFr = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $introductionEn = null;

    #[ORM\Column(type: Types::STRING, enumType: StatutPublication::class, length: 20)]
    private StatutPublication $statut = StatutPublication::Brouillon;

    #[ORM\Column]
    private bool $visibleIndex = false;

    #[ORM\Column]
    private int $ordreIndex = 0;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Oeuvre $pieceMaitresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $procedeFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $procedeEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tirageFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tirageEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $expositionFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $expositionEn = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    /** @var Collection<int, Oeuvre> */
    #[ORM\OneToMany(mappedBy: 'serie', targetEntity: Oeuvre::class)]
    #[ORM\OrderBy(['ordreSerie' => 'ASC'])]
    private Collection $oeuvres;

    public function __construct(string $slug, string $titreFr)
    {
        $this->slug = $slug;
        $this->titreFr = $titreFr;
        $this->oeuvres = new ArrayCollection();
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

    public function getIntroductionFr(): ?string
    {
        return $this->introductionFr;
    }

    public function setIntroductionFr(?string $introductionFr): static
    {
        $this->introductionFr = $introductionFr;

        return $this;
    }

    public function getIntroductionEn(): ?string
    {
        return $this->introductionEn;
    }

    public function setIntroductionEn(?string $introductionEn): static
    {
        $this->introductionEn = $introductionEn;

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

    public function getOrdreIndex(): int
    {
        return $this->ordreIndex;
    }

    public function setOrdreIndex(int $ordreIndex): static
    {
        $this->ordreIndex = $ordreIndex;

        return $this;
    }

    public function getPieceMaitresse(): ?Oeuvre
    {
        return $this->pieceMaitresse;
    }

    public function setPieceMaitresse(?Oeuvre $oeuvre): static
    {
        if ($oeuvre !== null && $oeuvre->getSerie() !== $this
            && $oeuvre->getSerie()?->getId() !== $this->getId()) {
            throw new \InvalidArgumentException('La pièce maîtresse doit appartenir à la série.');
        }

        $this->pieceMaitresse = $oeuvre;

        return $this;
    }

    /** @return Collection<int, Oeuvre> */
    public function getOeuvres(): Collection
    {
        return $this->oeuvres;
    }

    public function ajouterOeuvre(Oeuvre $oeuvre): void
    {
        if ($oeuvre->getSerie()?->getId() !== $this->getId()) {
            $oeuvre->setSerie($this);
        }
    }

    public function retirerOeuvre(Oeuvre $oeuvre): void
    {
        if ($oeuvre->getSerie()?->getId() === $this->getId()) {
            $oeuvre->setSerie(null);
        }
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

    public function getExpositionFr(): ?string
    {
        return $this->expositionFr;
    }

    public function setExpositionFr(?string $expositionFr): static
    {
        $this->expositionFr = $expositionFr;

        return $this;
    }

    public function getExpositionEn(): ?string
    {
        return $this->expositionEn;
    }

    public function setExpositionEn(?string $expositionEn): static
    {
        $this->expositionEn = $expositionEn;

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
