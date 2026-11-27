---
objective: "Disposer d’un socle Symfony et MySQL vérifié et d’un premier modèle persistant pour les œuvres, séries, catégories et médias."
status: done
---

# Plan : socle backend du site d’art

## Overview

| Field | Value |
| --- | --- |
| **Goal** | Valider MySQL et construire le premier modèle métier et ses migrations. |
| **Source** | Demande de l’utilisateur et `docs/guide/modele-metier.md`, issu des maquettes Penpot. |

Le travail déjà présent est à préserver et à vérifier : Doctrine et Compose ont été configurés, mais Docker n’a pas pu être démarré. Les films, comptes, messages et API complètes sont hors de ce premier lot ; ils nécessiteront leur propre définition.

## Phases

| # | Phase | File |
| --- | --- | --- |
| 1 | Valider les règles du modèle | [phase-1.md](./phase-1.md) |
| 2 | Vérifier MySQL et Doctrine | [phase-2.md](./phase-2.md) |
| 3 | Implémenter les entités et migrations | [phase-3.md](./phase-3.md) |
| 4 | Vérifier les relations et documenter | [phase-4.md](./phase-4.md) |

## Decisions

| Decision | Why |
| --- | --- |
| MySQL dans un conteneur avec volume persistant | Choix explicite de l’utilisateur ; les données survivent au redémarrage. |
| Français et anglais prévus dès le schéma, français seul au lancement | Décision existante ; les traductions restent facultatives hors français. |
| Pages fixes et textes modifiables | Décision existante ; aucun constructeur de pages. |
| Une œuvre a zéro ou une série et plusieurs catégories | Conforme aux maquettes et aux décisions retenues. |
