# Tests

- Back : PHPUnit est configuré (`back/phpunit.dist.xml`, `back/tests/`). Les tests de persistance et d'invariants du modèle s'exécutent dans le conteneur via `docker compose exec back php vendor/bin/phpunit`, sur la base `oeuvres_test` isolée.
- La base de test dépend du script `docker/mysql/initdb/01-oeuvres-test.sql` (droits sur `oeuvres_test`) et du suffixe `_test` de l'environnement test.
- La CI ne lance pas encore les tests PHPUnit ni MySQL.
- Vérifier les changements d'interface dans un navigateur lorsque possible ; les commandes techniques sont dans `docs/guide/verifications.md`.
