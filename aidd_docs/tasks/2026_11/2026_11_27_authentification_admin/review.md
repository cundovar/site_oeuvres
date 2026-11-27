# Review: authentification et modèle métier

- **Verdict**: changes-requested
- **Diff**: `ee1c856..540ff7c`
- **Axes run**: code, functional, relevancy
- **Date**: 2026-11-27
- **Findings**: 0 critical, 5 warning, 0 minor

## Phases

### Phase 1 — Architecture

- [ ] Les choix de session et d’historique ne sont pas explicitement confirmés par l’utilisateur ; la topologie de production reste à préciser — fix.

### Phase 2 — Compte et mot de passe

- [ ] Secret par défaut retiré et Compose exige une valeur, mais les parcours d’accès et les droits n’ont pu être testés ici — fix.

### Phase 3 — Lien e-mail

- [ ] Usage unique, expiration et panne SMTP couverts par des tests présents mais non exécutés ici ; le lien initial reste dans une URL reçue par le serveur — fix.

### Phase 4 — Interface et intégration

- [ ] Le parcours navigateur et l’accessibilité clavier ne sont pas vérifiés dans cette revue ; l’échec réseau de session reste non traité — fix.

### Phase 5 — Vérification et documentation

- [ ] Tests MySQL, migrations sur données préexistantes et parcours complet impossibles ici — fix.

## Findings

| Sev | Kind | Phase | Location | Issue | Fix |
| --- | --- | --- | --- | --- | --- |
| 🟡 | code | - | `back/src/Entity/Serie.php:193` | Deux séries non persistées ont des identifiants nuls comparés comme identiques : une pièce étrangère peut être acceptée et ajouter une œuvre à une nouvelle série ne fait rien. Reproduit sur des objets isolés. | Comparer l’identité objet en premier et les identifiants seulement s’ils sont non nuls ; tester les entités nouvelles. |
| 🟡 | code | - | `back/src/Entity/Categorie.php:94` | Ajouter une catégorie à une œuvre ne met plus à jour la collection inverse en mémoire ; l’ajout et le retrait depuis une série ne synchronisent pas non plus sa collection. Reproduit sur catégorie. | Synchroniser les deux côtés sans récursion et tester les collections avant et après rechargement. |
| 🟡 | code | - | `back/migrations/Version20261127150653.php:22` | La migration écrit NULL avant de rendre la colonne nullable ; sur une base existante contenant des œuvres non mises en avant elle échoue. L’opération inverse rend la colonne NOT NULL avant de convertir les valeurs nulles. | Changer l’ordre des opérations et tester montée et descente sur base contenant des données. |
| 🟡 | functional | 4 | `front/app/administration/page.tsx:16` | Une erreur réseau lors de la vérification de session n’est pas capturée et peut laisser le chargement affiché. | Traiter le rejet et présenter une erreur accessible. |
| 🟡 | functional | 5 | `back/tests/SecuriteAdministrationTest.php` | Aucun test MySQL ou parcours navigateur ne peut être relancé dans cet environnement ; l’initialisation locale est incomplète. | Vérifier migrations et tests sur base isolée et le parcours complet avec Mailpit et navigateur. |

## Verification

| Metric | Value |
| --- | --- |
| Verified | 0% (0/5 critères auth pleinement vérifiés dans cette revue) |
| Files checked | Entités, service, migrations, contrôleurs, sécurité, interface, tests, documentation et corrections ultérieures |
| Unchecked | Phases 1 à 5 — fix : décisions, droits, parcours complet et tests |
| Unplanned | Corrections du modèle métier incluses dans cette revue élargie. |
| Runtime | Cas d’objets isolés reproduits ; syntaxe PHP, lint front et config Compose valides ; Docker refusé, PHPUnit et navigateur non exécutés. |
