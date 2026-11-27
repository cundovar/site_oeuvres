# Authentification de l'administration

Cette page décrit le contrat d'authentification de l'espace d'administration, les décisions de sécurité retenues et les hypothèses techniques. Le mécanisme est implémenté depuis le 27 novembre 2026 et vérifié par des tests automatisés ainsi que par un parcours complet front vers backend en local. La topologie de production, la configuration SMTP réelle et la seconde étape éventuelle restent à préciser avant la mise en production.

## Parcours de connexion

L'administration est réservée à des comptes créés manuellement ; il n'existe aucune inscription publique. Deux parcours mènent à la même session :

- **Lien par e-mail.** L'administrateur saisit son adresse ; un lien signé, valable 900 secondes et à usage unique, est envoyé si le compte existe. La page intermédiaire demande une confirmation explicite (POST) avant d'ouvrir la session, afin qu'un scanner de messagerie ne consomme pas le lien. Les paramètres de validation sont transmis dans le corps de la requête et jamais dans une URL ; la page de confirmation remplace aussi l'entrée d'historique qui portait le lien. La réponse est identique que l'adresse soit connue ou non, y compris en cas d'échec d'envoi (échec journalisé côté serveur).
- **Mot de passe.** Alternative au lien. Les tentatives sont limitées ; les réponses d'échec sont génériques et ne précisent pas si le compte existe.

Chaque authentification réussie renouvelle l'identifiant de session. La protection des opérations est vérifiée par Symfony côté serveur pour chaque requête ; l'interface ne protège rien par elle-même.

## Contrat d'API

Toutes les routes sont sous `/api/admin`, servies par Symfony. Le front Next.js les appelle en même origine via son proxy, avec l'envoi du cookie de session et un en-tête `X-CSRF-Token` pour les requêtes qui modifient l'état.

| Route | Méthode | Description |
| --- | --- | --- |
| `/api/admin/csrf` | GET | Fournit un jeton CSRF lié à la session |
| `/api/admin/connexion/lien/demande` | POST | Demande d'envoi du lien par e-mail ; réponse identique pour une adresse inconnue ; requêtes limitées |
| `/api/admin/connexion/lien/valide` | POST | Valide le lien (paramètres dans le corps de la requête) et ouvre la session |
| `/api/admin/connexion/mot-de-passe` | POST | Connexion par adresse et mot de passe ; CSRF requis |
| `/api/admin/session` | GET | État de la session : informations du compte ou 401 |
| `/api/admin/deconnexion` | POST | Invalide la session ; CSRF requis |

## Décisions retenues le 27 novembre 2026

- **Session de 7 jours** : cookie `HttpOnly`, `SameSite=Lax`, chemin `/api/admin`, `Secure` en HTTPS. Aucun jeton dans le stockage navigateur.
- **Récupération d'accès par commande CLI** : création de compte et changement de mot de passe via `bin/console`, sans secret dans les arguments ni les journaux. Pas d'e-mail de réinitialisation dans ce lot.
- **Pas de double authentification supplémentaire dans ce lot** : le lien e-mail à usage unique constitue la preuve d'accès à la messagerie. Un TOTP éventuel devra s'appliquer aux deux parcours.
- **Pas de journal des connexions en base** : seuls les journaux applicatifs Symfony restent actifs.

## Hypothèses techniques

- **Secrets** : aucun secret n'est versionné. En développement, `BACK_APP_SECRET` est défini dans un fichier `.env` à la racine (utilisé par Compose) et `APP_SECRET` dans `back/.env.local` (pour un usage hors Compose) ; ces fichiers sont ignorés par git. En production, chaque déploiement fournit ses propres valeurs par l'environnement ; le secret versionné du dépôt ne doit jamais y être utilisé.
- **Topologie** : le front Next.js relaie `/api/admin` vers Symfony (proxy même origine), ce qui évite CORS en développement comme en production. La topologie de production réelle reste à définir ; elle devra préserver cette même origine ou justifier un ajustement des cookies.
- **HTTPS** : requis en production pour activer `Secure` sur le cookie ; le développement local reste en HTTP.
- **E-mail** : transport SMTP configurable par `MAILER_DSN`. En local, Mailpit reçoit les messages sans envoi réel ; les tests utilisent le transport de test Symfony.
- **Usage unique du lien** : les liens consommés sont mémorisés dans le cache applicatif, persistant via le volume Docker en développement. Une production multi-instances exigera un cache partagé (Redis ou équivalent).
- **Sessions** : stockées en fichiers côté Symfony en développement. Un stockage partagé sera nécessaire si la production déploie plusieurs instances.

## Interface

- `front/app/administration/connexion` : formulaire d'adresse e-mail pour recevoir le lien, avec bascule vers la connexion par mot de passe. La maquette Penpot « Connexion » a servi de référence ; les états validés restent à confronter au design system final.
- `front/app/administration/connexion/lien` : page intermédiaire ouverte par le lien de l'e-mail ; la session n'est ouverte qu'après la confirmation explicite.
- `front/app/administration` : espace protégé qui vérifie la session côté serveur et propose la déconnexion. La gestion des œuvres n'y est pas encore exposée.

Le front relaie `/api/admin` vers Symfony (proxy dans `front/next.config.ts`, origine configurable par `BACK_ORIGIN`). Aucun secret n'est stocké dans le navigateur : le cookie de session est `HttpOnly`.

## Gestion des comptes

Les commandes s'exécutent dans le conteneur backend ; le mot de passe est saisi à la volée, jamais en argument ni dans les journaux :

```bash
docker compose exec back php bin/console app:administrateur:creer adresse@example.org
docker compose exec back php bin/console app:administrateur:changer-mot-de-passe adresse@example.org
```

Le mot de passe exige au moins 12 caractères. Sans mot de passe, un compte ne peut se connecter que par lien e-mail. En production, ces commandes s'exécutent sur le serveur par l'hébergeur ; il n'existe pas de récupération automatique.

## Vérifications en local

- E-mails de test : Mailpit reçoit les messages du backend, interface sur `http://localhost:18025` (port `MAILPIT_PORT`).
- Parcours complet : demander un lien depuis `http://localhost:3000/administration/connexion`, l'ouvrir depuis Mailpit, confirmer, constater la session et la déconnexion.
- Tests automatisés : `docker compose exec back php vendor/bin/phpunit` couvre connexion par mot de passe, CSRF, limitation des tentatives, lien falsifié, lien authentique arrivé à expiration (durée de vie réduite à 2 secondes en environnement de test), échec d'envoi simulé, accès sans session et déconnexion (voir [vérifications](/guide/verifications)).

## Reste à faire

La purge des sessions, le TOTP éventuel, la récupération par e-mail, la configuration SMTP réelle de production et la topologie définitive restent à préciser avant la mise en production. Le lien reçu par e-mail reste une URL : sa visibilité dépend du client de messagerie, de l'historique du navigateur (nettoyé dès l'arrivée sur la page de confirmation) et d'un éventuel proxy inverse, qu'un déploiement devra configurer sans journaliser ces URL. Voir aussi le [modèle métier](/guide/modele-metier) : les comptes administrateurs sont gérés hors du modèle des œuvres.
