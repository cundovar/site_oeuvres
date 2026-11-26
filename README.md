# Site d'art

Le projet comprend un front Next.js dans `front/` et un back Symfony dans `back/`.

## Développement avec Docker

Installer Docker et Docker Compose, puis lancer à la racine du projet :

```bash
docker compose up --build
```

- Front : http://localhost:3000
- Back : http://localhost:18080

Si un port est déjà occupé, il est possible de le changer au lancement, par exemple : `FRONT_PORT=3001 BACK_PORT=18081 docker compose up --build`.

Les dépendances sont installées au démarrage dans des volumes Docker. Le code est partagé avec les conteneurs : les modifications locales sont prises en compte pendant le développement. Le premier démarrage peut prendre quelques minutes. Symfony est pour l'instant un squelette sans route métier ; une réponse 404 sur sa page d'accueil est donc normale.

Pour arrêter les services :

```bash
docker compose down
```

Cette configuration est destinée au développement local. Elle ne configure pas encore de base de données ni de communication entre le front et le back.

## Intégration et déploiement continus

Le workflow GitHub Actions `.github/workflows/ci.yml` s'exécute sur les pushs, les pull requests et à la demande. Il vérifie le lint et la compilation Next.js, l'installation et la configuration Symfony, ainsi que la construction des images Docker de développement. Il ne lance pas encore de tests automatisés : aucun n'est configuré dans le projet.

Le déploiement continu reste à configurer une fois l'hébergement et la méthode de déploiement choisis. Les images Docker actuelles servent au développement et ne constituent pas encore des images de production. Aucun secret de déploiement n'est nécessaire pour la CI actuelle.

## Documentation

La documentation VitePress se trouve dans `docs/`. Pour la consulter en local :

```bash
cd docs
npm ci
npm run dev
```

Sa compilation est vérifiée par la CI. Les changements validés qui ont une incidence sur le projet doivent être accompagnés d'une mise à jour des pages concernées.

Les commandes de contrôle du front, du back, de Docker et de la documentation sont décrites dans `docs/guide/verifications.md`. Les consignes communes pour les agents figurent dans `AGENTS.md` et sont reprises par `CLAUDE.md`.
