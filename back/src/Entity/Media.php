<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\TypeMedia;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'media')]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, enumType: TypeMedia::class, length: 20)]
    private TypeMedia $type = TypeMedia::Image;

    #[ORM\Column(length: 500, unique: true)]
    private string $cheminStockage;

    #[ORM\Column(length: 255)]
    private string $nomOriginal;

    #[ORM\Column(length: 100)]
    private string $mimeType;

    #[ORM\Column(type: Types::BIGINT)]
    private string $taille;

    #[ORM\Column(nullable: true)]
    private ?int $largeur = null;

    #[ORM\Column(nullable: true)]
    private ?int $hauteur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $altFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $altEn = null;

    #[ORM\Column(nullable: true)]
    private ?int $duree = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $etatTraitement = null;

    #[ORM\Column(nullable: true)]
    private ?int $progressionTraitement = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $erreurTraitement = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct(string $cheminStockage, string $nomOriginal, string $mimeType, int $taille)
    {
        $this->cheminStockage = $cheminStockage;
        $this->nomOriginal = $nomOriginal;
        $this->mimeType = $mimeType;
        $this->taille = (string) $taille;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): TypeMedia
    {
        return $this->type;
    }

    public function setType(TypeMedia $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCheminStockage(): string
    {
        return $this->cheminStockage;
    }

    public function setCheminStockage(string $cheminStockage): static
    {
        $this->cheminStockage = $cheminStockage;

        return $this;
    }

    public function getNomOriginal(): string
    {
        return $this->nomOriginal;
    }

    public function setNomOriginal(string $nomOriginal): static
    {
        $this->nomOriginal = $nomOriginal;

        return $this;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): static
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getTaille(): string
    {
        return $this->taille;
    }

    public function setTaille(int $taille): static
    {
        $this->taille = (string) $taille;

        return $this;
    }

    public function getLargeur(): ?int
    {
        return $this->largeur;
    }

    public function setLargeur(?int $largeur): static
    {
        $this->largeur = $largeur;

        return $this;
    }

    public function getHauteur(): ?int
    {
        return $this->hauteur;
    }

    public function setHauteur(?int $hauteur): static
    {
        $this->hauteur = $hauteur;

        return $this;
    }

    public function getAltFr(): ?string
    {
        return $this->altFr;
    }

    public function setAltFr(?string $altFr): static
    {
        $this->altFr = $altFr;

        return $this;
    }

    public function getAltEn(): ?string
    {
        return $this->altEn;
    }

    public function setAltEn(?string $altEn): static
    {
        $this->altEn = $altEn;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getEtatTraitement(): ?string
    {
        return $this->etatTraitement;
    }

    public function setEtatTraitement(?string $etatTraitement): static
    {
        $this->etatTraitement = $etatTraitement;

        return $this;
    }

    public function getProgressionTraitement(): ?int
    {
        return $this->progressionTraitement;
    }

    public function setProgressionTraitement(?int $progressionTraitement): static
    {
        $this->progressionTraitement = $progressionTraitement;

        return $this;
    }

    public function getErreurTraitement(): ?string
    {
        return $this->erreurTraitement;
    }

    public function setErreurTraitement(?string $erreurTraitement): static
    {
        $this->erreurTraitement = $erreurTraitement;

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
