<?php

declare(strict_types=1);

namespace App\Controller;

use App\Security\AuthentificateurMotDePasse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class AdministrationController extends AbstractController
{
    public function __construct(private readonly CsrfTokenManagerInterface $csrfTokenManager)
    {
    }

    #[Route('/api/admin/csrf', name: 'api_admin_csrf', methods: ['GET'])]
    public function csrf(): JsonResponse
    {
        return new JsonResponse([
            'csrf' => $this->csrfTokenManager->getToken(AuthentificateurMotDePasse::ID_JETON)->getValue(),
        ]);
    }

    #[Route('/api/admin/connexion/mot-de-passe', name: 'api_admin_connexion_mot_de_passe', methods: ['POST'])]
    public function connexionMotDePasse(): JsonResponse
    {
        return new JsonResponse(['message' => 'Connexion refusée.'], 401);
    }

    #[Route('/api/admin/session', name: 'api_admin_session', methods: ['GET'])]
    public function session(#[CurrentUser] ?\Symfony\Component\Security\Core\User\UserInterface $utilisateur): JsonResponse
    {
        return new JsonResponse(['email' => $utilisateur?->getUserIdentifier()]);
    }

    #[Route('/api/admin/deconnexion', name: 'api_admin_deconnexion', methods: ['POST'])]
    public function deconnexion(Request $request): Response
    {
        $jeton = (string) $request->headers->get('X-CSRF-Token', '');
        $session = $request->getSession();

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken(AuthentificateurMotDePasse::ID_JETON, $jeton))) {
            throw new AccessDeniedException('Jeton CSRF invalide.');
        }

        $nomCookie = $session->getName();
        $session->invalidate();

        $reponse = new JsonResponse(null, 204);
        $reponse->headers->clearCookie($nomCookie, '/api/admin');

        return $reponse;
    }
}
