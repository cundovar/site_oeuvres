# Review: authentification de l’administration

- **Verdict**: blocked
- **Diff**: `7630eef..bd83675`
- **Axes run**: code, functional, relevancy
- **Date**: 2026-11-27
- **Findings**: 1 critical, 8 warning, 0 minor

## Phases

### Phase 1 — Architecture et décisions de sécurité

- [ ] Contrat compatible avec le déploiement : topologie et secret de production non garantis ; décisions de durée de session et absence de journal non attribuées à une validation utilisateur — fix.

### Phase 2 — Compte et mot de passe

- [ ] Seuls les comptes autorisés accèdent aux opérations : sécurité présente dans le code mais pas vérifiée de bout en bout ici ; secret public utilisable par défaut — fix.

### Phase 3 — Lien e-mail

- [ ] Usage unique et expiration : configuration et test de rejeu présents ; expiration réelle non testée, lien exposé aux journaux, défaut SMTP divulguant l’existence du compte — fix.

### Phase 4 — Interface et intégration

- [ ] Clavier et session bout en bout : non vérifiés au navigateur dans cette revue ; échec réseau laisse le chargement affiché — fix.

### Phase 5 — Vérification et documentation

- [ ] Tests MySQL et parcours réel non exécutés dans cette revue ; test d’expiration ne teste qu’une signature falsifiée — fix.

## Findings

| Sev | Kind | Phase | Location | Issue | Fix |
| --- | --- | --- | --- | --- | --- |
| 🔴 | code | 2 | `back/.env:19` | Secret Symfony connu et versionné ; s’il est repris en production, les signatures des liens et autres mécanismes dépendants ne sont plus fiables. | Imposer un secret propre à chaque déploiement, non versionné ; documenter et vérifier sa présence en production. |
| 🟡 | functional | 1 | `docs/guide/authentification.md:27` | Session de 7 jours et absence de journal retenues sans trace de validation utilisateur ; architecture de production reste inconnue. | Confirmer les choix et les exigences de déploiement, corriger le statut du plan. |
| 🟡 | functional | 2 | `back/config/packages/security.yaml:14` | Protection configurée mais parcours réel et droits non vérifiés dans cet environnement. | Tester les opérations protégées en environnement isolé, y compris un compte sans rôle. |
| 🟡 | functional | 3 | `back/tests/SecuriteAdministrationTest.php:203` | Le test modifie la date signée : il teste une signature falsifiée, pas un lien réellement expiré. | Générer un lien signé dont la date est passée ou contrôler l’horloge, puis tester le refus. |
| 🟡 | code | 3 | `back/src/Controller/ConnexionController.php:49` | Une erreur d’envoi SMTP survient uniquement pour un compte existant et peut donner une réponse 500 au lieu de la réponse neutre. | Gérer l’échec d’envoi sans divulguer l’existence du compte, journaliser sans secret et tester. |
| 🟡 | code | 3 | `front/app/administration/connexion/lien/page.tsx:16` | Paramètres sensibles dans les URL GET et POST, susceptibles d’être enregistrés dans les journaux serveur/proxy et l’historique ; aucune mesure de rédaction des logs documentée. | Retirer les secrets des URL dès que possible et configurer la rédaction des journaux et une politique Referrer-Policy adaptée. |
| 🟡 | functional | 4 | `front/app/administration/page.tsx:16` | Échec réseau de la vérification de session non capturé : l’interface peut rester sur « Chargement ». | Traiter le rejet et afficher une erreur accessible ; vérifier clavier et navigateur. |
| 🟡 | functional | 5 | `back/tests/SecuriteAdministrationTest.php:24` | Les tests et le parcours front–back n’ont pas pu être exécutés : Docker indisponible et dépendances locales incomplètes. | Exécuter sur MySQL isolé avec Mailpit et navigateur, puis consigner les résultats. |
| 🟡 | code | 2 | `back/src/Security/AuthentificateurMotDePasse.php:31` | JSON invalide déclenche une exception non traitée au lieu d’une erreur client contrôlée. | Valider le corps et renvoyer une réponse 400 générique ; ajouter un test. |

## Verification

| Metric | Value |
| --- | --- |
| Verified | 0% (0/5 critères pleinement vérifiés dans cette revue) |
| Files checked | Plan, configuration sécurité et session, entité, contrôleurs, commandes, front, tests et documentation |
| Unchecked | Phases 1 à 5 — fix : décisions, sécurité, expiration, navigateur et tests de bout en bout |
| Unplanned | Modifications non commitées des entités du premier plan exclues de la revue. |
| Runtime | ESLint, compilation VitePress et configuration Compose valides ; Docker inaccessible, PHP vendor local incomplet, tests MySQL et navigateur non exécutés. |
