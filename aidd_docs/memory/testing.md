# Tests

- Back : PHPUnit (`back/phpunit.dist.xml`, `back/tests/`) via `docker compose exec back php vendor/bin/phpunit`, base `oeuvres_test` isolée. Deux classes : persistance/invariants du modèle et sécurité de l'administration.
- Pièges connus : les services Symfony sont réinitialisés entre requêtes BrowserKit, donc les événements mailer ne survivent pas à une requête suivante (assertions d'e-mails immédiates) ; les jetons CSRF de session sont détruits à la migration de session lors de la connexion.
- La base de test dépend du script `docker/mysql/initdb/01-oeuvres-test.sql` et du suffixe `_test` de l'environnement test.
- La CI ne lance pas encore les tests PHPUnit ni MySQL.
- Vérifier les changements d'interface dans un navigateur lorsque possible ; les commandes techniques sont dans `docs/guide/verifications.md`.
