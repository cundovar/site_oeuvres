# CI et déploiement

Le workflow GitHub Actions vérifie les pushs et les pull requests, et peut être lancé manuellement. Il exécute le lint et la compilation du front, les vérifications Composer et Symfony, la construction des images Docker et la compilation de la documentation.

Il n'y a pas encore de tests automatisés configurés. La CI vérifie la documentation techniquement, mais ne peut pas garantir que son contenu reflète tous les changements.

Le déploiement reste à définir selon l'hébergement choisi. Les images Docker actuelles sont destinées au développement et ne constituent pas une configuration de production.
