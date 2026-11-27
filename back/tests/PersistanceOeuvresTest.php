<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Categorie;
use App\Entity\Media;
use App\Entity\Oeuvre;
use App\Entity\Serie;
use App\Enum\StatutPublication;
use App\Enum\TypeMedia;
use App\Service\GestionOeuvre;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

final class PersistanceOeuvresTest extends KernelTestCase
{
    private static EntityManagerInterface $em;

    public static function setUpBeforeClass(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);
        $application->setAutoExit(false);
        $application->run(
            new ArrayInput(['command' => 'doctrine:database:create', '--if-not-exists' => true]),
            new NullOutput()
        );
        $application->run(
            new ArrayInput(['command' => 'doctrine:migrations:migrate', '--no-interaction' => true]),
            new NullOutput()
        );
        self::ensureKernelShutdown();
    }

    protected function setUp(): void
    {
        self::bootKernel();
        self::$em = self::getContainer()->get(EntityManagerInterface::class);
    }

    protected function tearDown(): void
    {
        self::$em->createQuery('DELETE FROM App\Entity\Serie s')->execute();
        self::$em->createQuery('DELETE FROM App\Entity\Oeuvre o')->execute();
        self::$em->createQuery('DELETE FROM App\Entity\Categorie c')->execute();
        self::$em->createQuery('DELETE FROM App\Entity\Media m')->execute();
        self::$em->clear();
        self::ensureKernelShutdown();
        parent::tearDown();
    }

    public function testPersistanceCompleteUneOeuvre(): void
    {
        $serie = new Serie('serie-complete', 'Série complète');
        $categorieA = new Categorie('Paysage');
        $categorieB = new Categorie('Nocturne');
        $media = new Media('images/soleil.jpg', 'soleil.jpg', 'image/jpeg', 123456);
        $media->setLargeur(1920)->setHauteur(1080)->setAltFr('Peinture au soleil');
        $video = new Media('videos/atelier.mp4', 'atelier.mp4', 'video/mp4', 2_500_000_000);
        $video->setType(TypeMedia::Video);

        $oeuvre = new Oeuvre('lune-doree', 'Lune dorée');
        $oeuvre->setTitreEn('Golden Moon');
        $oeuvre->setPresentationFr('Une huile sur toile.');
        $oeuvre->setAnnee(2024);
        $oeuvre->setSerie($serie);
        $oeuvre->addCategorie($categorieA);
        $oeuvre->addCategorie($categorieB);
        $oeuvre->setMedia($media);
        $oeuvre->setStatut(StatutPublication::Publie);
        $oeuvre->setVisibleIndex(true);
        $oeuvre->setOrdreIndex(3);
        $oeuvre->setOrdreSerie(1);
        $oeuvre->setProcedeFr('Huile sur toile');
        $oeuvre->setTirageFr('8 exemplaires');
        $oeuvre->setIterations('Itération 2/8');
        $oeuvre->setFormat('40 × 60 cm');
        $oeuvre->setEdition('Atelier');
        $oeuvre->setPrixSurDemande(false);
        $oeuvre->setPrix('1200.00');
        $oeuvre->setNombreTotal(8);
        $oeuvre->setNombreRestant(5);
        $oeuvre->setSeoTitreFr('Lune dorée — huile sur toile');

        self::$em->persist($serie);
        self::$em->persist($categorieA);
        self::$em->persist($categorieB);
        self::$em->persist($media);
        self::$em->persist($video);
        self::$em->persist($oeuvre);
        self::$em->flush();
        $id = $oeuvre->getId();
        $idVideo = $video->getId();
        self::$em->clear();

        $rechargee = self::$em->find(Oeuvre::class, $id);

        self::assertNotNull($rechargee);
        self::assertSame('lune-doree', $rechargee->getSlug());
        self::assertSame('Lune dorée', $rechargee->getTitreFr());
        self::assertSame('Golden Moon', $rechargee->getTitreEn());
        self::assertSame(2024, $rechargee->getAnnee());
        self::assertSame('serie-complete', $rechargee->getSerie()?->getSlug());
        self::assertCount(2, $rechargee->getCategories());
        self::assertSame('soleil.jpg', $rechargee->getMedia()?->getNomOriginal());
        self::assertSame('2500000000', self::$em->find(Media::class, $idVideo)?->getTaille());
        self::assertSame(StatutPublication::Publie, $rechargee->getStatut());
        self::assertTrue($rechargee->isVisibleIndex());
        self::assertFalse($rechargee->isMiseEnAvant());
        self::assertSame(3, $rechargee->getOrdreIndex());
        self::assertSame(1, $rechargee->getOrdreSerie());
        self::assertSame('1200.00', $rechargee->getPrix());
        self::assertSame(8, $rechargee->getNombreTotal());
        self::assertSame(5, $rechargee->getNombreRestant());
        self::assertSame('Lune dorée — huile sur toile', $rechargee->getSeoTitreFr());
        self::assertNull($rechargee->getSeoTitreEn());
        self::assertNull($rechargee->getDeletedAt());
        self::assertInstanceOf(\DateTimeImmutable::class, $rechargee->getCreatedAt());
        self::assertInstanceOf(\DateTimeImmutable::class, $rechargee->getUpdatedAt());
    }

    public function testSerieFacultativeEtTraductions(): void
    {
        $oeuvre = new Oeuvre('oeuvre-isolee', 'Œuvre isolée');
        $oeuvre->setPresentationFr('Sans série.');
        self::$em->persist($oeuvre);
        self::$em->flush();
        $id = $oeuvre->getId();
        self::$em->clear();

        $rechargee = self::$em->find(Oeuvre::class, $id);

        self::assertNotNull($rechargee);
        self::assertNull($rechargee->getSerie());
        self::assertSame(StatutPublication::Brouillon, $rechargee->getStatut());
        self::assertNull($rechargee->getTitreEn());
        self::assertNull($rechargee->getPresentationEn());
        self::assertNull($rechargee->getProcedeEn());
        self::assertNull($rechargee->getTirageEn());

        $rechargee->setSerie(null);
        self::$em->flush();

        self::assertNull($rechargee->getSerie());
        self::assertNotNull($rechargee->getId());
    }

    public function testSuppressionSerieDetachLesOeuvresEtLaPieceMaitresse(): void
    {
        $serie = new Serie('serie-a-supprimer', 'Série à supprimer');
        $oeuvre1 = new Oeuvre('oeuvre-dans-serie-1', 'Œuvre dans série 1');
        $oeuvre1->setSerie($serie);
        $oeuvre2 = new Oeuvre('oeuvre-dans-serie-2', 'Œuvre dans série 2');
        $oeuvre2->setSerie($serie);
        $serie->setPieceMaitresse($oeuvre1);

        self::$em->persist($serie);
        self::$em->persist($oeuvre1);
        self::$em->persist($oeuvre2);
        self::$em->flush();
        $idOeuvre1 = $oeuvre1->getId();
        $idOeuvre2 = $oeuvre2->getId();
        $idSerie = $serie->getId();
        self::$em->clear();

        $serieRecuperee = self::$em->find(Serie::class, $idSerie);
        self::assertSame($idOeuvre1, $serieRecuperee->getPieceMaitresse()->getId());

        self::$em->remove($serieRecuperee);
        self::$em->flush();
        self::$em->clear();

        self::assertNotNull(self::$em->find(Oeuvre::class, $idOeuvre1));
        self::assertNotNull(self::$em->find(Oeuvre::class, $idOeuvre2));
        $oeuvre1Recuperee = self::$em->find(Oeuvre::class, $idOeuvre1);
        self::assertNull($oeuvre1Recuperee->getSerie());
    }

    public function testCategoriesMultiplesEtSuppressionCategorie(): void
    {
        $oeuvre = new Oeuvre('oeuvre-multi', 'Œuvre multi-catégories');
        $categorieA = new Categorie('Abstrait');
        $categorieA->setNomEn('Abstract');
        $categorieB = new Categorie('Minimal');

        $oeuvre->addCategorie($categorieA);
        $oeuvre->addCategorie($categorieB);

        self::$em->persist($categorieA);
        self::$em->persist($categorieB);
        self::$em->persist($oeuvre);
        self::$em->flush();
        $idOeuvre = $oeuvre->getId();
        $idCategorieA = $categorieA->getId();
        self::$em->clear();

        $rechargee = self::$em->find(Oeuvre::class, $idOeuvre);
        self::assertCount(2, $rechargee->getCategories());

        self::$em->remove(self::$em->find(Categorie::class, $idCategorieA));
        self::$em->flush();
        self::$em->clear();

        $sansCategorieA = self::$em->find(Oeuvre::class, $idOeuvre);
        self::assertNotNull($sansCategorieA);
        self::assertCount(1, $sansCategorieA->getCategories());
        self::assertSame('Minimal', $sansCategorieA->getCategories()->first()->getNomFr());
    }

    public function testPieceMaitresseDoitAppartenirASaSerie(): void
    {
        $serieX = new Serie('serie-x', 'Série X');
        $serieY = new Serie('serie-y', 'Série Y');
        $oeuvre = new Oeuvre('oeuvre-x', 'Œuvre de X');
        $oeuvre->setSerie($serieX);

        self::$em->persist($serieX);
        self::$em->persist($serieY);
        self::$em->persist($oeuvre);
        self::$em->flush();

        try {
            $serieY->setPieceMaitresse($oeuvre);
            self::fail('La pièce maîtresse hors série aurait dû être refusée.');
        } catch (\InvalidArgumentException) {
            self::assertNull($serieY->getPieceMaitresse());
        }

        $serieX->setPieceMaitresse($oeuvre);
        self::$em->flush();
        $idSerieX = $serieX->getId();
        $idOeuvre = $oeuvre->getId();
        self::$em->clear();

        $serieXRecuperee = self::$em->find(Serie::class, $idSerieX);
        self::assertSame($idOeuvre, $serieXRecuperee->getPieceMaitresse()->getId());

        $oeuvreRecuperee = self::$em->find(Oeuvre::class, $idOeuvre);
        $oeuvreRecuperee->setSerie(null);
        self::$em->flush();
        self::$em->clear();

        $serieXFinale = self::$em->find(Serie::class, $idSerieX);
        self::assertNull($serieXFinale->getPieceMaitresse());
        self::assertNull(self::$em->find(Oeuvre::class, $idOeuvre)->getSerie());
    }

    public function testMiseEnAvantUnique(): void
    {
        $oeuvreA = new Oeuvre('oeuvre-en-avant-a', 'Œuvre en avant A');
        $oeuvreB = new Oeuvre('oeuvre-en-avant-b', 'Œuvre en avant B');
        self::$em->persist($oeuvreA);
        self::$em->persist($oeuvreB);
        self::$em->flush();
        $idA = $oeuvreA->getId();
        $idB = $oeuvreB->getId();
        self::$em->clear();

        $gestionOeuvre = new GestionOeuvre(self::$em);
        $gestionOeuvre->mettreEnAvant(self::$em->find(Oeuvre::class, $idA));
        self::$em->flush();
        self::$em->clear();

        self::assertTrue(self::$em->find(Oeuvre::class, $idA)->isMiseEnAvant());
        self::assertFalse(self::$em->find(Oeuvre::class, $idB)->isMiseEnAvant());

        $gestionOeuvre->mettreEnAvant(self::$em->find(Oeuvre::class, $idB));
        self::$em->flush();
        self::$em->clear();

        self::assertFalse(self::$em->find(Oeuvre::class, $idA)->isMiseEnAvant());
        self::assertTrue(self::$em->find(Oeuvre::class, $idB)->isMiseEnAvant());
    }

    public function testDeuxMisesEnAvantDirectesSontRefuseesParLaBase(): void
    {
        $oeuvreA = new Oeuvre('oeuvre-en-avant-c', 'Œuvre en avant C');
        $oeuvreB = new Oeuvre('oeuvre-en-avant-d', 'Œuvre en avant D');
        self::$em->persist($oeuvreA);
        self::$em->persist($oeuvreB);
        self::$em->flush();

        $oeuvreA->setMiseEnAvant(true);
        $oeuvreB->setMiseEnAvant(true);

        $this->expectException(UniqueConstraintViolationException::class);
        self::$em->flush();
    }

    public function testAjoutEtRetraitViaLaSerieSontPersistes(): void
    {
        $serieA = new Serie('serie-ajout-a', 'Série ajout A');
        $serieB = new Serie('serie-ajout-b', 'Série ajout B');
        $oeuvre = new Oeuvre('oeuvre-ajout', 'Œuvre ajoutée via série');
        self::$em->persist($serieA);
        self::$em->persist($serieB);
        self::$em->persist($oeuvre);
        self::$em->flush();
        $idOeuvre = $oeuvre->getId();
        $idSerieA = $serieA->getId();
        $idSerieB = $serieB->getId();
        self::$em->clear();

        $serieA = self::$em->find(Serie::class, $idSerieA);
        $oeuvre = self::$em->find(Oeuvre::class, $idOeuvre);
        $serieA->ajouterOeuvre($oeuvre);
        $serieA->setPieceMaitresse($oeuvre);
        self::$em->flush();
        self::$em->clear();

        $oeuvre = self::$em->find(Oeuvre::class, $idOeuvre);
        self::assertSame($idSerieA, $oeuvre->getSerie()?->getId());
        self::assertContains($idOeuvre, self::$em->find(Serie::class, $idSerieA)->getOeuvres()->map(fn (Oeuvre $o) => $o->getId())->toArray());
        self::assertSame($idOeuvre, self::$em->find(Serie::class, $idSerieA)->getPieceMaitresse()?->getId());

        $serieB = self::$em->find(Serie::class, $idSerieB);
        $serieB->ajouterOeuvre($oeuvre);
        self::$em->flush();
        self::$em->clear();

        $oeuvre = self::$em->find(Oeuvre::class, $idOeuvre);
        self::assertSame($idSerieB, $oeuvre->getSerie()?->getId());
        $oeuvresSerieA = self::$em->find(Serie::class, $idSerieA)->getOeuvres();
        self::assertNotContains($idOeuvre, $oeuvresSerieA->map(fn (Oeuvre $o) => $o->getId())->toArray());
        self::assertNull(self::$em->find(Serie::class, $idSerieA)->getPieceMaitresse());

        $serieB = self::$em->find(Serie::class, $idSerieB);
        $serieA = self::$em->find(Serie::class, $idSerieA);
        $serieA->retirerOeuvre($oeuvre);
        self::$em->flush();
        self::$em->clear();

        self::assertSame($idSerieB, self::$em->find(Oeuvre::class, $idOeuvre)->getSerie()?->getId());

        $serieB = self::$em->find(Serie::class, $idSerieB);
        $serieA = self::$em->find(Serie::class, $idSerieA);
        $serieA->retirerOeuvre($oeuvre);
        self::$em->flush();
        self::$em->clear();

        $serieB = self::$em->find(Serie::class, $idSerieB);
        self::assertSame($idOeuvre, $serieB->getOeuvres()->map(fn (Oeuvre $o) => $o->getId())->toArray()[0]);
    }

    public function testUneNouvelleSerieRefuseLaPieceMaitresseDUneAutreNouvelleSerie(): void
    {
        $serieA = new Serie('serie-nouvelle-a', 'Série nouvelle A');
        $serieB = new Serie('serie-nouvelle-b', 'Série nouvelle B');
        $oeuvre = new Oeuvre('oeuvre-nouvelle-b', 'Œuvre nouvelle B');
        $oeuvre->setSerie($serieB);

        try {
            $serieA->setPieceMaitresse($oeuvre);
            self::fail('La pièce maîtresse d’une autre série nouvelle aurait dû être refusée.');
        } catch (\InvalidArgumentException) {
            self::assertNull($serieA->getPieceMaitresse());
        }

        $serieA->ajouterOeuvre($oeuvre);
        self::assertSame($serieA, $oeuvre->getSerie());
        self::assertContains($oeuvre, $serieA->getOeuvres());
        self::assertNotContains($oeuvre, $serieB->getOeuvres());

        $serieA->setPieceMaitresse($oeuvre);
        self::assertSame($oeuvre, $serieA->getPieceMaitresse());
    }

    public function testCollectionsCategoriesSynchroniseesEnMemoire(): void
    {
        $categorie = new Categorie('Mémoire');
        $oeuvre = new Oeuvre('oeuvre-memoire', 'Œuvre mémoire');

        $oeuvre->addCategorie($categorie);
        self::assertContains($oeuvre, $categorie->getOeuvres());

        $oeuvre->removeCategorie($categorie);
        self::assertNotContains($oeuvre, $categorie->getOeuvres());

        $categorie->ajouterOeuvre($oeuvre);
        self::assertContains($categorie, $oeuvre->getCategories());
        self::assertContains($oeuvre, $categorie->getOeuvres());

        $categorie->retirerOeuvre($oeuvre);
        self::assertNotContains($categorie, $oeuvre->getCategories());
        self::assertNotContains($oeuvre, $categorie->getOeuvres());
    }

    public function testSlugUnique(): void
    {
        $oeuvre = new Oeuvre('slug-deja-emploi', 'Œuvre première');
        self::$em->persist($oeuvre);
        self::$em->flush();

        $copie = new Oeuvre('slug-deja-emploi', 'Œuvre copie');
        self::$em->persist($copie);

        $this->expectException(UniqueConstraintViolationException::class);
        self::$em->flush();
    }

    public function testCorbeilleEtRestauration(): void
    {
        $oeuvre = new Oeuvre('oeuvre-corbeille', 'Œuvre en corbeille');
        self::$em->persist($oeuvre);
        self::$em->flush();
        $id = $oeuvre->getId();
        self::$em->clear();

        $recuperee = self::$em->find(Oeuvre::class, $id);
        $recuperee->mettreALaCorbeille();
        self::$em->flush();
        self::$em->clear();

        $corbeille = self::$em->find(Oeuvre::class, $id);
        self::assertNotNull($corbeille->getDeletedAt());
        self::assertGreaterThanOrEqual($corbeille->getCreatedAt(), $corbeille->getUpdatedAt());

        $corbeille->restaurer();
        self::$em->flush();
        self::$em->clear();

        self::assertNull(self::$em->find(Oeuvre::class, $id)->getDeletedAt());
    }
}
