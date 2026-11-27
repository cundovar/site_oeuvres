# Présentation

Le projet est un futur site d'art. Son design system, ses pages et ses composants sont en préparation dans Penpot local.

- `front/` contient l'application Next.js en TypeScript, avec SCSS pour les styles.
- `back/` contient le squelette Symfony, destiné au backend.
- `docs/` contient cette documentation VitePress.
- `compose.yaml` configure les services de développement Docker.

Le projet est au démarrage : les fonctionnalités métier et la liaison entre les applications restent à définir.

La [préparation de l'intégration Penpot](/guide/penpot) décrit les éléments à réunir pour intégrer une première page.

## Décisions retenues

Ces décisions sont prises pour la conception ; aucune n'est encore implémentée.

- **Langues.** Le site est publié en français. Le schéma de données prévoit néanmoins une langue par champ traduisible (titre, texte de présentation, textes de page, texte de remplacement des images). L'administration n'affiche que la saisie française tant que l'anglais n'est pas activé ; l'activer doit rester un changement d'interface, sans migration des données.
- **Navigation publique.** Le menu principal comporte Œuvres, Séries, Films, À propos et Contact, dans cet ordre, sur ordinateur comme sur mobile. L'entrée correspondant à la page consultée est mise en évidence ; une page de détail met en évidence sa rubrique parente.
- **Administration.** Séries figure aussi dans le pied de page. Pas de constructeur de pages : les pages du site sont fixes et seuls leurs textes sont modifiables. Une œuvre appartient à une seule série mais peut porter plusieurs catégories.

Le [modèle métier initial](/guide/modele-metier) décrit les champs et relations proposés à partir des maquettes.
