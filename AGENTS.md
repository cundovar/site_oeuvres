# Instructions du projet

## Organisation et contexte

- Le site d'art est en cours de conception dans Penpot. `front/` contient Next.js, `back/` Symfony et `docs/` la documentation VitePress. `compose.yaml` sert au développement local.
- Avant de modifier un périmètre, examiner les fichiers et instructions qui le concernent. Préserver les changements existants et ne pas écraser le travail d'autrui.
- Respecter les maquettes et le design system validés lorsqu'ils sont disponibles. Si un choix de design manquant est déterminant, demander une précision ; sinon, expliciter les hypothèses retenues.
- Utiliser TypeScript et SCSS pour le front ; ne pas ajouter Tailwind CSS. Privilégier les modules SCSS pour les styles propres aux composants.
- Tenir compte de l'accessibilité et des écrans de différentes tailles pour les changements d'interface. Ne pas présenter une fonctionnalité prévue comme déjà opérationnelle.

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
