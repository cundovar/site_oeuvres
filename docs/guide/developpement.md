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

MySQL 8.4 démarre avec le backend et conserve ses données dans le volume Docker `mysql_data`. Le port local par défaut est `127.0.0.1:13306` (modifiable avec `MYSQL_PORT`). Les identifiants du fichier Compose sont réservés au développement local. Ne pas les utiliser en production ; définir alors `DATABASE_URL` et les secrets dans l'environnement approprié.

Doctrine et ses migrations sont installés, ainsi que les entités métier (œuvres, séries, catégories, médias) et la migration initiale. Depuis le conteneur :

```bash
docker compose exec back php bin/console doctrine:migrations:status
docker compose exec back php bin/console doctrine:migrations:migrate
```

Sur une base de développement vide, la migration crée les tables. Pour tout réinitialiser : `docker compose down -v` supprime aussi les volumes, puis un nouveau démarrage recrée la base et applique le script `docker/mysql/initdb/01-oeuvres-test.sql`, qui autorise l'utilisateur de développement sur la base de test.

Les tests PHPUnit couvrent la persistance et les invariants du modèle. Ils s'exécutent dans le conteneur et utilisent une base `oeuvres_test` isolée, créée au premier lancement :

```bash
docker compose exec back php vendor/bin/phpunit
```

Depuis PHP sur la machine hôte, l'URL par défaut dans `back/.env` utilise le port 13306 ; si `MYSQL_PORT` change, adapter `DATABASE_URL` dans `back/.env.local`. PHP doit disposer de `pdo_mysql`.

Le code est partagé avec les conteneurs et les dépendances utilisent des volumes Docker. Pour arrêter les services : `docker compose down`.

Le front utilise SCSS : les styles globaux sont dans `front/app/globals.scss` et les styles locaux peuvent utiliser des fichiers `.module.scss`.

## Administration et e-mails de test

Avant le premier démarrage, créer les fichiers de secrets locaux (ignorés par git) :

```bash
openssl rand -hex 16   # valeur à placer dans .env à la racine
BACK_APP_SECRET=<valeur générée>          # .env à la racine, pour Compose
APP_SECRET=<valeur générée>               # back/.env.local, pour un usage hors Compose
```

Mailpit reçoit les e-mails du backend sans envoi réel : interface sur `http://localhost:18025` (modifiable avec `MAILPIT_PORT`). L'administration est protégée par une session Symfony ; le front relaie les appels `/api/admin` vers le backend, origine configurable avec `BACK_ORIGIN` dans Compose ou `next.config.ts`.

Créer un compte administrateur (mot de passe demandé à la volée, 12 caractères minimum) :

```bash
docker compose exec back php bin/console app:administrateur:creer adresse@example.org
```

Le parcours complet démarre sur `http://localhost:3000/administration/connexion` : lien par e-mail ou mot de passe.

## Documentation en local

Avec Node.js 24 et npm, depuis la racine :

```bash
cd docs
npm ci
npm run dev
```

L'adresse locale est indiquée dans le terminal. Pour vérifier la compilation : `npm run build`.
