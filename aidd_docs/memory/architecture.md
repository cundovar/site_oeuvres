# Architecture

- Front Next.js en TypeScript ; SCSS et modules SCSS pour les styles, sans Tailwind.
- Back Symfony avec Doctrine, MySQL 8.4 (Compose) et le premier modèle métier : entités `Oeuvre`, `Serie`, `Categorie`, `Media`, migration initiale et service `GestionOeuvre`. Aucune API, administration ni liaison front-back.
- Docker Compose sert au développement local ; l'hébergement de production et le déploiement restent à définir.
