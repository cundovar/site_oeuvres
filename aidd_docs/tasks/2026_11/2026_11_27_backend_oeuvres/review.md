# Review: socle backend du site d’art

- **Verdict**: changes-requested
- **Diff**: `ee1c856..7630eef` ; modifications ultérieures d’authentification exclues
- **Axes run**: code, functional, relevancy
- **Date**: 2026-11-27
- **Findings**: 0 critical, 5 warning, 0 minor

## Phases

### Phase 1 — Valider les règles du modèle

- [x] Champs et règles essentiels documentés ; choix encore ouverts identifiés — `docs/guide/modele-metier.md`.

### Phase 2 — Vérifier MySQL et Doctrine

- [ ] Connexion et persistance après redémarrage : non vérifiables ici, accès Docker refusé — fix : exécuter dans un environnement Docker accessible.

### Phase 3 — Implémenter les entités et migrations

- [x] Entités, relations et migration présentes — `back/src/Entity/`, `back/migrations/Version20261127142027.php`.
- [ ] Unicité de la mise en avant garantie : contournable et non atomique — fix : faire respecter l’invariant dans tous les chemins, y compris concurrents.

### Phase 4 — Vérifier les relations et documenter

- [ ] Tests de persistance et de migration non exécutés dans cette revue ; installation isolée bloquée par DNS et Docker inaccessible — fix : exécuter sur MySQL isolé et rapporter les résultats.
- [x] Documentation de mise en place et vérifications présente — `docs/guide/developpement.md`, `docs/guide/verifications.md`.

## Findings

| Sev | Kind | Phase | Location | Issue | Fix |
| --- | --- | --- | --- | --- | --- |
| 🟡 | functional | 2 | `compose.yaml:31` | Connexion et persistance MySQL non vérifiées pendant cette revue. | Démarrer MySQL et vérifier connexion et redémarrage avec données de test. |
| 🟡 | functional | 3 | `back/src/Service/GestionOeuvre.php:20` | L’unicité de mise en avant est contournable par le setter public et le service ne protège pas des appels concurrents ou de deux mises en avant avant flush. | Garantir l’invariant de façon atomique et tester ces cas. |
| 🟡 | code | 3 | `back/migrations/Version20261127142027.php:24` | La taille du média est un INT signé : les vidéos de plus de 2 147 483 647 octets ne sont pas représentables, alors que Penpot montre un fichier de 2,4 Go. | Utiliser un entier 64 bits et tester une taille supérieure à 2 Go. |
| 🟡 | code | 3 | `back/src/Entity/Serie.php:210` | Ajouter ou retirer directement une œuvre modifie uniquement la collection inverse ; l’œuvre peut conserver son ancienne série après flush, ou une pièce maîtresse devenir incohérente. | Synchroniser les deux côtés et tester les méthodes publiques après rechargement. |
| 🟡 | functional | 4 | `back/tests/PersistanceOeuvresTest.php:24` | Les tests MySQL et la migration n’ont pas pu être exécutés dans cette revue. | Exécuter les tests sur une base isolée après vérification de ses droits et de son nom. |

## Verification

| Metric | Value |
| --- | --- |
| Verified | 50% (3/6 critères) ; vérification partielle fondée sur les preuves disponibles |
| Files checked | Plan et phases, entités, service, migration, tests, Compose, configuration Doctrine et documentation du commit |
| Unchecked | Connexion et persistance MySQL — fix ; unicité — fix ; tests MySQL — fix |
| Unplanned | Modifications d’authentification ultérieures présentes dans l’arbre de travail, exclues de la revue. |
| Runtime | Syntaxe PHP et configuration Compose valides ; VitePress vérifié séparément. Docker refusé ; téléchargement Composer isolé bloqué par DNS. |
