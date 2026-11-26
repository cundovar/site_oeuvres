# Préparer l'intégration Penpot

Le design system, les pages et les composants sont en préparation dans Penpot local. Aucun accès à un fichier ou connecteur Penpot n'a encore été configuré pour ce dépôt.

## Éléments à réunir pour une première page

- Un accès au fichier Penpot, ou un export des maquettes validées, avec la page et les dimensions de référence.
- Les styles validés : couleurs, typographies, espacements et règles de mise en page.
- Les composants et leurs états utiles : navigation, boutons, liens et autres éléments présents dans la page.
- Les adaptations prévues pour les écrans mobiles et ordinateurs, ainsi que les comportements interactifs.
- Les images et autres ressources nécessaires, accompagnées de leurs droits d'utilisation et de leurs textes alternatifs lorsqu'ils sont connus.

Si une information manque, relever les questions qui influent sur le rendu ou le comportement avant de l'inventer. Les détails encore provisoires doivent être identifiés comme tels.

## Intégrer et vérifier

1. Identifier les éléments communs et les composants propres à la page.
2. Les intégrer dans `front/` avec TypeScript et SCSS, en respectant les règles d'accessibilité et les maquettes validées.
3. Comparer le rendu aux références disponibles sur ordinateur et mobile, puis vérifier les états et interactions pertinents.
4. Exécuter les [vérifications du projet](/guide/verifications), documenter les décisions utiles et signaler les écarts ou contrôles impossibles.

Le style actuel de la page d'accueil est provisoire ; il ne constitue pas le design system du site.
