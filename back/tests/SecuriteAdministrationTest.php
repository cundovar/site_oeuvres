<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Administrateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class SecuriteAdministrationTest extends WebTestCase
{
    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->disableReboot();
    }

    public static function setUpBeforeClass(): void
    {        self::bootKernel();
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
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $em->createQuery('DELETE FROM App\Entity\Administrateur a')->execute();
        self::getContainer()->get('cache.app')->clear();
        self::getContainer()->get('cache.rate_limiter')->clear();
        self::ensureKernelShutdown();
    }

    protected function tearDown(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->disableReboot();
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $em->createQuery('DELETE FROM App\Entity\Administrateur a')->execute();
        self::getContainer()->get('cache.app')->clear();
        self::getContainer()->get('cache.rate_limiter')->clear();
        self::ensureKernelShutdown();
        parent::tearDown();
    }

    private function creerCompte(string $email, ?string $motDePasse): void
    {
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $administrateur = new Administrateur($email);
        if ($motDePasse !== null) {
            $hasher = self::getContainer()->get(UserPasswordHasherInterface::class);
            $administrateur->setPassword($hasher->hashPassword($administrateur, $motDePasse));
        }
        $em->persist($administrateur);
        $em->flush();
        $em->clear();
    }

    /** @return array{string, string} */
    private function jetonCsrf(): array
    {
        static::getClient()->request('GET', '/api/admin/csrf');
        $reponse = static::getClient()->getResponse();
        $jeton = (json_decode($reponse->getContent(), true))['csrf'];

        return [$jeton, $reponse->getContent()];
    }

    public function testConnexionParMotDePasseOuvreUneSession(): void
    {
        $this->creerCompte('mdp-ok@test.local', 'UnMotDePasseSuffisant123');

        [$jeton] = $this->jetonCsrf();
        $client = static::getClient();
        $client->request('POST', '/api/admin/connexion/mot-de-passe', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'mdp-ok@test.local',
            'motDePasse' => 'UnMotDePasseSuffisant123',
            'csrf' => $jeton,
        ], JSON_THROW_ON_ERROR));

        self::assertResponseStatusCodeSame(200);

        $client->request('GET', '/api/admin/session');
        self::assertResponseStatusCodeSame(200);
        self::assertSame('mdp-ok@test.local', json_decode($client->getResponse()->getContent(), true)['email']);
    }

    public function testMauvaisMotDePasseEtCompteInconnuDonnentLaMemeReponse(): void
    {
        $this->creerCompte('mdp-existant@test.local', 'UnMotDePasseSuffisant123');

        [$jeton] = $this->jetonCsrf();
        $client = static::getClient();

        $client->request('POST', '/api/admin/connexion/mot-de-passe', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'mdp-existant@test.local',
            'motDePasse' => 'faux',
            'csrf' => $jeton,
        ], JSON_THROW_ON_ERROR));
        $reponseMauvaisMotDePasse = [$client->getResponse()->getStatusCode(), $client->getResponse()->getContent()];

        $client->request('POST', '/api/admin/connexion/mot-de-passe', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'inconnu@test.local',
            'motDePasse' => 'faux',
            'csrf' => $jeton,
        ], JSON_THROW_ON_ERROR));
        $reponseCompteInconnu = [$client->getResponse()->getStatusCode(), $client->getResponse()->getContent()];

        self::assertSame($reponseMauvaisMotDePasse, $reponseCompteInconnu);
        self::assertSame(401, $reponseMauvaisMotDePasse[0]);
    }

    public function testJetonCsrfManquantRefuseLaConnexion(): void
    {
        $this->creerCompte('csrf@test.local', 'UnMotDePasseSuffisant123');

        $client = static::getClient();
        $client->request('POST', '/api/admin/connexion/mot-de-passe', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'csrf@test.local',
            'motDePasse' => 'UnMotDePasseSuffisant123',
        ], JSON_THROW_ON_ERROR));

        self::assertResponseStatusCodeSame(401);
    }

    public function testTropDeTentativesSontBloquees(): void
    {
        [$jeton] = $this->jetonCsrf();
        $client = static::getClient();
        $email = 'limitation-' . uniqid() . '@test.local';

        $client->request('POST', '/api/admin/connexion/mot-de-passe', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => $email,
            'motDePasse' => 'faux',
            'csrf' => $jeton,
        ], JSON_THROW_ON_ERROR));

        for ($i = 0; $i < 4; ++$i) {
            $client->request('POST', '/api/admin/connexion/mot-de-passe', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
                'email' => $email,
                'motDePasse' => 'faux',
                'csrf' => $jeton,
            ], JSON_THROW_ON_ERROR));
            self::assertResponseStatusCodeSame(401);
        }

        $client->request('POST', '/api/admin/connexion/mot-de-passe', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => $email,
            'motDePasse' => 'faux',
            'csrf' => $jeton,
        ], JSON_THROW_ON_ERROR));

        self::assertResponseStatusCodeSame(429);
    }

    public function testAccesProtegeSansSessionEstRefuse(): void
    {
        $client = static::getClient();
        $client->request('GET', '/api/admin/session');

        self::assertResponseStatusCodeSame(401);
        self::assertSame('Session requise.', json_decode($client->getResponse()->getContent(), true)['message']);
    }

    public function testLienConnexionCompletPuisRejetDuRejeu(): void
    {
        $this->creerCompte('lien@test.local', null);

        $client = static::getClient();
        $client->request('POST', '/api/admin/connexion/lien/demande', [], [], ['CONTENT_TYPE' => 'application/json'], '{"email":"lien@test.local"}');
        self::assertResponseStatusCodeSame(202);
        self::assertEmailCount(1);

        $parametres = $this->parametresDuLien(self::getMailerMessage()->getTextBody());
        self::assertNotNull($parametres);

        $client->request('GET', '/api/admin/connexion/lien/valide');
        self::assertResponseStatusCodeSame(405);

        $client->request('POST', '/api/admin/connexion/lien/valide', $parametres);
        self::assertResponseStatusCodeSame(200);

        $client->request('GET', '/api/admin/session');
        self::assertResponseStatusCodeSame(200);
        self::assertSame('lien@test.local', json_decode($client->getResponse()->getContent(), true)['email']);

        $client->request('POST', '/api/admin/connexion/lien/valide', $parametres);
        self::assertResponseStatusCodeSame(401);
    }

    public function testLienFalsifieEstRefuse(): void
    {
        $this->creerCompte('expire@test.local', null);

        $client = static::getClient();
        $client->request('POST', '/api/admin/connexion/lien/demande', [], [], ['CONTENT_TYPE' => 'application/json'], '{"email":"expire@test.local"}');
        self::assertEmailCount(1);

        $parametres = $this->parametresDuLien(self::getMailerMessage()->getTextBody());
        self::assertNotNull($parametres);
        $parametres['expires'] = (string) ((int) $parametres['expires'] - 1);
        $client->request('POST', '/api/admin/connexion/lien/valide', $parametres);

        self::assertResponseStatusCodeSame(401);
    }

    public function testLienAuthentiqueExpireEstRefuse(): void
    {
        $this->creerCompte('expiration@test.local', null);

        $client = static::getClient();
        $client->request('POST', '/api/admin/connexion/lien/demande', [], [], ['CONTENT_TYPE' => 'application/json'], '{"email":"expiration@test.local"}');
        self::assertEmailCount(1);

        $parametres = $this->parametresDuLien(self::getMailerMessage()->getTextBody());
        self::assertNotNull($parametres);
        sleep(3);
        $client->request('POST', '/api/admin/connexion/lien/valide', $parametres);

        self::assertResponseStatusCodeSame(401);
    }

    public function testEchecEnvoiConserveLaReponseNeutre(): void
    {
        $this->creerCompte('panne@test.local', null);

        $client = static::getClient();
        $mailer = $this->createStub(MailerInterface::class);
        $mailer->method('send')->willThrowException(new TransportException('SMTP indisponible.'));
        self::getContainer()->set('mailer.mailer', $mailer);

        $client->request('POST', '/api/admin/connexion/lien/demande', [], [], ['CONTENT_TYPE' => 'application/json'], '{"email":"inconnu@nul.test"}');
        $inconnu = [$client->getResponse()->getStatusCode(), $client->getResponse()->getContent()];

        $client->request('POST', '/api/admin/connexion/lien/demande', [], [], ['CONTENT_TYPE' => 'application/json'], '{"email":"panne@test.local"}');
        $connu = [$client->getResponse()->getStatusCode(), $client->getResponse()->getContent()];

        self::assertSame($inconnu, $connu);
        self::assertSame(202, $connu[0]);
    }

    /** @return array<string, string>|null */
    private function parametresDuLien(string $texte): ?array
    {
        if (!preg_match('#https?://\S+#', $texte, $correspondances)) {
            return null;
        }

        parse_str((string) parse_url($correspondances[0], PHP_URL_QUERY), $parametres);

        return $parametres === [] ? null : $parametres;
    }

    public function testDemandeIdentiquePourCompteInconnu(): void
    {
        $this->creerCompte('connu@test.local', null);

        $client = static::getClient();
        $client->request('POST', '/api/admin/connexion/lien/demande', [], [], ['CONTENT_TYPE' => 'application/json'], '{"email":"inconnu@test.local"}');
        self::assertResponseStatusCodeSame(202);
        self::assertEmailCount(0);
        $inconnu = [$client->getResponse()->getStatusCode(), $client->getResponse()->getContent()];

        $client->request('POST', '/api/admin/connexion/lien/demande', [], [], ['CONTENT_TYPE' => 'application/json'], '{"email":"connu@test.local"}');
        self::assertResponseStatusCodeSame(202);
        self::assertEmailCount(1);
        $connu = [$client->getResponse()->getStatusCode(), $client->getResponse()->getContent()];

        self::assertSame($connu, $inconnu);
    }

    public function testDeconnexionRequiertCsrfEtInvalideLaSession(): void
    {
        $this->creerCompte('deconnexion@test.local', 'UnMotDePasseSuffisant123');

        $client = static::getClient();
        [$jeton] = $this->jetonCsrf();
        $client->request('POST', '/api/admin/connexion/mot-de-passe', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'deconnexion@test.local',
            'motDePasse' => 'UnMotDePasseSuffisant123',
            'csrf' => $jeton,
        ], JSON_THROW_ON_ERROR));
        self::assertResponseStatusCodeSame(200);

        [$jeton] = $this->jetonCsrf();

        $client->request('POST', '/api/admin/deconnexion');
        self::assertResponseStatusCodeSame(403);

        $client->request('POST', '/api/admin/deconnexion', [], [], ['HTTP_X-CSRF-Token' => $jeton]);
        self::assertResponseStatusCodeSame(204);

        $client->request('GET', '/api/admin/session');
        self::assertResponseStatusCodeSame(401);
    }
}
