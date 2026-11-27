---
status: done
---

# Instruction : Vérifier les relations et documenter

## Architecture projection

> ✅ créer · ✏️ modifier · ❌ supprimer

```txt
✅ back/tests/ — tests de persistance et invariants pertinents
✏️ back/composer.json et back/composer.lock — outil de test si nécessaire
✏️ docs/guide/verifications.md et docs/guide/developpement.md — commandes vérifiées
✏️ docs/guide/modele-metier.md — limites et état final
```

## Test Scope

```mermaid
---
title: Test scope
---
journey
  section Setup
    Préparer un environnement isolé et les décisions nécessaires => prérequis disponibles: 5: cli
  section Happy path
    Exécuter les tâches de la phase => Les tests démontrent les relations et contraintes retenues ; la documentation compile et décrit le fonctionnement réel.: 5: cli
```

## Tasks to do

### `1)` Vérifier les relations et documenter

> Réaliser la phase sans écraser les changements existants.

1. Tester la persistance, les relations, le retrait d’une série, les catégories multiples et les traductions facultatives avec une base de test isolée.
2. Tester les suppressions et invariants selon les règles confirmées ; ne pas supprimer de données de développement.
3. Exécuter les contrôles Symfony, migrations et VitePress ; rapporter les résultats et limites.

## Test acceptance criteria

| Task | Acceptance criteria |
| --- | --- |
| 1 | Les tests démontrent les relations et contraintes retenues ; la documentation compile et décrit le fonctionnement réel. |
