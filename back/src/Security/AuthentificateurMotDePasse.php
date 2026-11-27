<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\TooManyLoginAttemptsAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

final class AuthentificateurMotDePasse extends AbstractAuthenticator
{
    public const ID_JETON = 'admin_connexion';

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'api_admin_connexion_mot_de_passe'
            && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $donnees = json_decode($request->getContent() ?: '[]', true, 512, JSON_THROW_ON_ERROR);
        $email = mb_strtolower(trim((string) ($donnees['email'] ?? '')));
        $motDePasse = (string) ($donnees['motDePasse'] ?? '');
        $csrf = (string) ($donnees['csrf'] ?? $request->headers->get('X-CSRF-Token', ''));

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($motDePasse),
            [
                new CsrfTokenBadge(self::ID_JETON, $csrf),
            ],
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new JsonResponse(['email' => $token->getUserIdentifier()]);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $status = $exception instanceof TooManyLoginAttemptsAuthenticationException ? 429 : 401;

        return new JsonResponse(['message' => 'Connexion refusée.'], $status);
    }
}
