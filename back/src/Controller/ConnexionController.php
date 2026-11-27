<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Administrateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkDetails;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

final class ConnexionController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoginLinkHandlerInterface $loginLinkHandler,
        private readonly MailerInterface $mailer,
        #[Autowire('%app.front_url%')] private readonly string $frontUrl,
        #[Autowire('%app.mailer_from%')] private readonly string $mailerFrom,
    ) {
    }

    #[Route('/api/admin/connexion/lien/demande', name: 'api_admin_connexion_lien_demande', methods: ['POST'])]
    public function demanderLien(Request $request, #[Autowire(service: 'limiter.connexion_lien')] RateLimiterFactoryInterface $connexionLienLimiter): JsonResponse
    {
        $limiteur = $connexionLienLimiter->create($request->getClientIp() ?? 'inconnu');
        $limite = $limiteur->consume();
        if (!$limite->isAccepted()) {
            return new JsonResponse(['message' => 'Trop de demandes. Réessayez plus tard.'], 429);
        }

        $donnees = json_decode($request->getContent() ?: '[]', true);
        if (!\is_array($donnees)) {
            $donnees = [];
        }
        $email = mb_strtolower(trim((string) ($donnees['email'] ?? '')));

        $administrateur = $this->entityManager->getRepository(Administrateur::class)->findOneBy(['email' => $email]);

        if ($administrateur !== null) {
            $lien = $this->loginLinkHandler->createLoginLink($administrateur);
            $this->envoyerEmail($administrateur->getEmail(), $this->urlFront($lien));
        }

        return new JsonResponse(['message' => 'Si cette adresse est autorisée, un lien vous a été envoyé.'], 202);
    }

    #[Route('/api/admin/connexion/lien/valide', name: 'api_admin_connexion_lien_valide', methods: ['POST'])]
    public function validerLien(): JsonResponse
    {
        return new JsonResponse(['message' => 'Lien de connexion refusé.'], 401);
    }

    private function urlFront(LoginLinkDetails $lien): string
    {
        parse_str((string) parse_url($lien->getUrl(), PHP_URL_QUERY), $parametres);
        $requete = http_build_query($parametres);

        return rtrim($this->frontUrl, '/') . '/administration/connexion/lien' . ($requete === '' ? '' : '?' . $requete);
    }

    private function envoyerEmail(string $destinataire, string $lien): void
    {
        $email = (new Email())
            ->from($this->mailerFrom)
            ->to($destinataire)
            ->subject('Lien de connexion à l’administration')
            ->text(
                "Bonjour,\n\n"
                . "Voici votre lien de connexion à l’administration :\n\n"
                . $lien . "\n\n"
                . "Ce lien est valable 15 minutes et ne peut être utilisé qu’une seule fois.\n"
                . "Ouvrez-le, puis confirmez la connexion sur la page qui s’affiche.\n\n"
                . "Si vous n’êtes pas à l’origine de cette demande, ignorez ce message."
            );

        $this->mailer->send($email);
    }
}
