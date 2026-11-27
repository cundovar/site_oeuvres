---
objective: "Permettre à un administrateur autorisé de se connecter et de gérer une session sécurisée entre Next.js et Symfony."
status: done
---

# Plan : authentification de l’administration

## Overview

| Field | Value |
| --- | --- |
| **Goal** | Authentifier l’administrateur par lien à usage unique ou mot de passe et protéger toutes les opérations administratives. |
| **Source** | Demande de l’utilisateur, échange sur l’authentification et écran Penpot « Connexion ». |

Ce plan est distinct du modèle métier. Il dépend d’une base MySQL fonctionnelle. Préserver le travail en cours. Aucun dispositif décrit ici n’est encore opérationnel. La topologie de production, l’e-mail de connexion, la politique de mot de passe, la durée de session, la récupération d’accès et la seconde étape restent à préciser avant leur implémentation.

## Phases

| # | Phase | File |
| --- | --- | --- |
| 1 | Architecture et décisions de sécurité | [phase-1.md](./phase-1.md) |
| 2 | Compte administrateur et accès par mot de passe | [phase-2.md](./phase-2.md) |
| 3 | Lien de connexion par e-mail | [phase-3.md](./phase-3.md) |
| 4 | Interface et intégration | [phase-4.md](./phase-4.md) |
| 5 | Vérification de bout en bout et documentation | [phase-5.md](./phase-5.md) |

## Resources

| Source | Verified |
| --- | --- |
| [Symfony Security 7.4](https://symfony.com/doc/7.4/security.html) | Protection des routes, sessions et limitation des tentatives. |
| [Symfony Security configuration 7.4](https://symfony.com/doc/7.4/reference/configuration/security.html) | Durée et nombre d’utilisations des liens de connexion. |
| [Symfony login links](https://symfony.com/doc/current/security/login_link.html) | Contraintes et risques des liens envoyés par e-mail. |

## Decisions

| Decision | Why |
| --- | --- |
| Lien par e-mail valable 15 minutes et à usage unique ; mot de passe en alternative | Parcours discuté et cohérent avec la maquette. |
| Comptes autorisés sans inscription publique | Réduit l’accès à l’administration. |
| Autorisation vérifiée par Symfony pour chaque opération protégée | L’interface seule ne peut pas protéger les données. |
| Cookie de session plutôt que jeton dans le stockage navigateur | Permet une session serveur et limite l’exposition du secret au JavaScript ; protections CSRF requises. |
