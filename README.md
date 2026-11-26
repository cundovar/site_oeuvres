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
# site_oeuvres
