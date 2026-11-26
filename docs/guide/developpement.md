# Développement

## Applications avec Docker

Depuis la racine du projet :

```bash
docker compose up --build
```

Le front est accessible sur `http://localhost:3000` et le back sur `http://localhost:18080`. Symfony ne possède pas encore de route métier ; une erreur 404 à la racine est attendue.

Les ports peuvent être modifiés au lancement :

```bash
FRONT_PORT=3001 BACK_PORT=18081 docker compose up --build
```

Le code est partagé avec les conteneurs et les dépendances utilisent des volumes Docker. Pour arrêter les services : `docker compose down`.

Le front utilise SCSS : les styles globaux sont dans `front/app/globals.scss` et les styles locaux peuvent utiliser des fichiers `.module.scss`.

## Documentation en local

Avec Node.js 24 et npm, depuis la racine :

```bash
cd docs
npm ci
npm run dev
```

L'adresse locale est indiquée dans le terminal. Pour vérifier la compilation : `npm run build`.
