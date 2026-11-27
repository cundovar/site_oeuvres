# Architecture

- Front Next.js en TypeScript ; SCSS et modules SCSS pour les styles, sans Tailwind.
- Back Symfony avec Doctrine, MySQL 8.4 (Compose) et le premier modèle métier : entités `Oeuvre`, `Serie`, `Categorie`, `Media`, migration initiale et service `GestionOeuvre`.
- Authentification administration : compte `Administrateur` (mot de passe ou lien e-mail usage unique), firewall `/api/admin` avec session cookie 7 jours et CSRF. Aucune gestion des œuvres exposée, aucune API publique.
- Front Next.js relaie `/api/admin` vers Symfony via rewrites (`BACK_ORIGIN`) ; Mailpit reçoit les e-mails locaux (port 18025).
- Docker Compose sert au développement local ; l'hébergement de production et le déploiement restent à définir.
