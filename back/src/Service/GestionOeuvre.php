<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Oeuvre;
use Doctrine\ORM\EntityManagerInterface;

final class GestionOeuvre
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function mettreEnAvant(Oeuvre $oeuvre): void
    {
        $repository = $this->entityManager->getRepository(Oeuvre::class);

        foreach ($repository->findBy(['miseEnAvant' => true]) as $enAvant) {
            if ($enAvant !== $oeuvre) {
                $enAvant->setMiseEnAvant(false);
            }
        }

        $oeuvre->setMiseEnAvant(true);
    }
}
