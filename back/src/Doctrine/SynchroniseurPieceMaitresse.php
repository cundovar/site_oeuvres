<?php

declare(strict_types=1);

namespace App\Doctrine;

use App\Entity\Oeuvre;
use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;

#[AsDoctrineListener('onFlush')]
final class SynchroniseurPieceMaitresse
{
    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $entityManager = $eventArgs->getObjectManager();
        if (!$entityManager instanceof EntityManagerInterface) {
            return;
        }

        $unitOfWork = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityUpdates($entityManager) as $entite) {
            if (!$entite instanceof Oeuvre) {
                continue;
            }

            $modifications = $unitOfWork->getEntityChangeSet($entite);
            if (!\array_key_exists('serie', $modifications)) {
                continue;
            }

            $ancienne = $modifications['serie'][0];
            if (!$ancienne instanceof Serie) {
                continue;
            }

            $piece = $ancienne->getPieceMaitresse();
            if ($piece === null || $piece->getId() !== $entite->getId()) {
                continue;
            }

            $ancienne->setPieceMaitresse(null);
            $unitOfWork->recomputeSingleEntityChangeSet(
                $entityManager->getClassMetadata(Serie::class),
                $ancienne
            );
        }
    }
}
