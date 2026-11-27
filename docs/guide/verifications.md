# Vérifications du projet

Choisir les contrôles adaptés aux changements réalisés. Les commandes ci-dessous sont à lancer depuis la racine du dépôt, sauf indication contraire. Les installations nécessitent les versions et outils indiqués dans le [guide de développement](/guide/developpement).

## Front Next.js

Après installation des dépendances avec `cd front && npm ci`, lancer depuis la racine :

```bash
cd front
npm run lint
npm run build
```

Le lint vérifie le code et la compilation vérifie notamment les types. Pour une modification d'interface, contrôler également les pages et interactions concernées dans un navigateur, sur ordinateur et sur mobile quand c'est pertinent.

## Back Symfony

Après installation des dépendances avec `cd back && composer install --no-interaction`, lancer depuis la racine :

```bash
cd back
composer validate --strict --no-check-publish
composer check-platform-reqs
find src config public bin -type f -name '*.php' -print0 | xargs -0 -r -n1 php -l
php bin/console lint:yaml config
php bin/console lint:container
```

Ces commandes vérifient le manifeste, la plateforme PHP, la syntaxe et la configuration. Pour vérifier le comportement du modèle de données, lancer les tests PHPUnit dans le conteneur :

```bash
docker compose exec back php vendor/bin/phpunit
```

Ces tests utilisent une base `oeuvres_test` isolée, créée par le script `docker/mysql/initdb/01-oeuvres-test.sql` lors du premier démarrage de MySQL et par le premier lancement des tests. Ils ne touchent pas aux données de développement. La CI ne lance pas encore ces tests, car elle n'exécute pas de conteneurs MySQL.

## Docker

Depuis la racine, avec Docker et Docker Compose disponibles :

```bash
docker compose config --quiet
docker compose build
```

Pour vérifier le démarrage et les services : `docker compose up --build`, puis consulter les adresses indiquées dans le [guide de développement](/guide/developpement). Pour les arrêter : `docker compose down`.

## Documentation

Après installation des dépendances avec `cd docs && npm ci`, lancer depuis la racine :

```bash
cd docs
npm run build
```

La compilation vérifie notamment les liens internes ; relire aussi le contenu pour confirmer qu'il correspond au projet.

## CI et limites

Le workflow `.github/workflows/ci.yml` exécute ces contrôles techniques sur GitHub après installation des dépendances, sans les tests PHPUnit ni les vérifications de base de données. Les contrôles visuels, les interactions et l'exactitude de la documentation nécessitent une vérification adaptée au changement.
