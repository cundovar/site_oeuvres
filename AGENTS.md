# Instructions du projet

## Organisation et contexte

- Le site d'art est en cours de conception dans Penpot. `front/` contient Next.js, `back/` Symfony et `docs/` la documentation VitePress. `compose.yaml` sert au développement local.
- Avant de modifier un périmètre, examiner les fichiers et instructions qui le concernent. Préserver les changements existants et ne pas écraser le travail d'autrui.
- Respecter les maquettes et le design system validés lorsqu'ils sont disponibles. Si un choix de design manquant est déterminant, demander une précision ; sinon, expliciter les hypothèses retenues.
- Utiliser TypeScript et SCSS pour le front ; ne pas ajouter Tailwind CSS. Privilégier les modules SCSS pour les styles propres aux composants.
- Tenir compte de l'accessibilité et des écrans de différentes tailles pour les changements d'interface. Ne pas présenter une fonctionnalité prévue comme déjà opérationnelle.

## Contrôleurs et services Symfony

Ces règles s’appliquent aux contrôleurs HTTP et aux services du backend, y compris les futures API.

- Garder les contrôleurs centrés sur HTTP : réception et validation des entrées, vérification des droits, appel du cas d’usage et construction de la réponse.
- Viser des actions courtes et lisibles, idéalement autour de 30 lignes ; ce repère n’est pas une limite mécanique. Découper dès que plusieurs responsabilités ou branches métier nuisent à la compréhension.
- Regrouper les routes par responsabilité cohérente ; utiliser un contrôleur par action lorsqu’une opération le justifie, sans multiplier les classes pour de simples délégations.
- Placer les règles métier et invariants dans les entités ou des services de domaine adaptés ; ne pas les dupliquer entre contrôleurs ni les confier uniquement au front.
- Utiliser un service applicatif pour un cas d’usage qui coordonne plusieurs étapes, services ou transactions. Un orchestrateur n’est nécessaire que si cette coordination apporte une réelle clarté ; éviter les couches qui ne font que transmettre un appel.
- Confier les requêtes de lecture ou recherche non triviales aux repositories ; éviter d’éparpiller les requêtes et la persistance dans les contrôleurs.
- Définir explicitement les données d’entrée et de sortie ; valider les entrées côté serveur et ne pas exposer directement toutes les propriétés d’une entité ou des informations sensibles.
- Appliquer les contrôles d’authentification et d’autorisation côté Symfony à chaque opération protégée ; distinguer accès public et administration. Prévoir les protections adaptées aux sessions et aux mutations.
- Retourner des réponses et codes HTTP cohérents, avec des erreurs compréhensibles sans détails internes. Éviter de masquer une erreur inattendue comme un succès.
- Tester les règles métier au niveau approprié et les contrats HTTP importants : cas nominal, validation, accès refusé et erreurs pertinentes. Vérifier le comportement réel plutôt que le nombre de lignes.

## Vérification

- Choisir les contrôles adaptés aux fichiers et aux comportements modifiés. Le guide `docs/guide/verifications.md` répertorie les commandes existantes et la CI.
- Pour une interface, vérifier aussi le rendu et les interactions quand l'environnement le permet. Pour un changement de comportement, ajouter ou adapter des tests pertinents lorsque c'est nécessaire.
- Avant de terminer, indiquer les vérifications effectuées et leurs résultats, ainsi que les contrôles impossibles et les limites connues. Ne pas annoncer un changement comme validé si les vérifications nécessaires n'ont pas été faites.

## Documentation

Pour chaque changement validé qui modifie le comportement, l'architecture, l'installation, la configuration ou les procédures, mettre à jour les pages concernées de `docs/` dans le même ensemble de modifications. Documenter uniquement l'état réel et distinguer ce qui est prévu de ce qui est opérationnel. Vérifier la compilation VitePress lorsque la documentation change. Si aucune mise à jour n'est pertinente, ne pas créer de modification artificielle.

## Commit naming

When asked for a commit name, reply with one ready-to-copy Conventional Commits line in English: `type(scope): short description`. No explanation or list.

## Git add suggestion

When asked for files to add, reply with one ready-to-copy `git add <file1> <file2> ...` command containing only relevant changed files. No explanation.

## Memory Management

Lire les fichiers de premier niveau référencés ci-dessous dans `aidd_docs/memory/` au début du travail ; les notes de `internal/` et `external/` sont à lire au besoin.

<aidd_project_memory>
@aidd_docs/memory/architecture.md
@aidd_docs/memory/codebase-map.md
@aidd_docs/memory/coding-assertions.md
@aidd_docs/memory/project-brief.md
@aidd_docs/memory/testing.md
@aidd_docs/memory/vcs.md
</aidd_project_memory>
