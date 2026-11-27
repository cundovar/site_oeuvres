# Modèle métier initial

Ce modèle est issu des écrans « Admin — Parcours » de Penpot et des décisions du projet. Les entités Symfony `Oeuvre`, `Serie`, `Categorie` et `Media`, leurs relations et la migration initiale sont implémentées depuis le 27 novembre 2026, avec des tests PHPUnit de persistance et d'invariants. Il n'existe encore ni administration, ni API, ni gestion des fichiers de médias : aucun champ décrit ci-dessous n'est modifiable depuis une interface.

## Œuvre

| Champ proposé | Type et règle envisagés |
| --- | --- |
| id | Identifiant technique entier |
| slug | Texte court unique, saisi en français ; sert d'identifiant public dans les URL (œuvres et séries) |
| titre_fr, titre_en | Texte court ; français requis à la publication, anglais facultatif |
| presentation_fr, presentation_en | Texte long facultatif, mise en forme limitée |
| annee | Entier facultatif |
| serie | Référence facultative vers une seule série ; retirer une œuvre de la série ne la supprime pas |
| categories | Plusieurs catégories, via une table de liaison ; suppression d'une catégorie sans suppression des œuvres |
| media | Référence facultative vers un média image ; texte alternatif requis à la publication à préciser selon le contexte |
| statut | Brouillon ou publié ; disponibilité des tirages distincte de la publication |
| visible_index, mise_en_avant | Booléens distincts du statut de publication ; une seule œuvre mise en avant sur le site, unicité contrôlée par l'application |
| ordre_index, ordre_serie | Entiers distincts pour les deux ordres d'affichage |
| procede, iterations, tirage, format, edition | Données techniques facultatives ; les textes destinés au public prévoient français et anglais |
| prix_sur_demande | Booléen ; distingue un prix communiqué sur demande d'un montant chiffré |
| prix | Montant décimal facultatif en euros, deux décimales ; devise unique EUR pour ce premier lot |
| seo_titre_fr/en, seo_description_fr/en | Surcharges facultatives, sinon reprises du contenu |
| created_at, updated_at, deleted_at | Dates de création, modification et mise en corbeille facultative |

La maquette prévoit une corbeille de 30 jours : la mise en corbeille (`deleted_at`) et la restauration sont implémentées sur l'entité, mais la purge automatique ne l'est pas. L'œuvre mise en avant est unique sur le site ; l'application la désigne via le service `GestionOeuvre`, qui désactive les autres. Les conditions exactes de publication (champs requis) relèveront des formulaires d'administration, à venir.

## Série

| Champ proposé | Type et règle envisagés |
| --- | --- |
| id | Identifiant technique entier |
| slug | Texte court unique, saisi en français ; sert d'identifiant public dans les URL |
| titre_fr, titre_en | Texte court ; français requis à la publication, anglais facultatif |
| introduction_fr, introduction_en | Texte long facultatif |
| statut, visible_index, ordre_index | Publication, visibilité et ordre distincts |
| piece_maitresse | Référence facultative vers une œuvre appartenant à cette série ; invariant à contrôler par l'application |
| procede_fr/en, tirage_fr/en, exposition_fr/en | Repères éditoriaux facultatifs |
| created_at, updated_at | Dates de création et modification |

La période est calculée à partir des années connues des œuvres ; le nombre de pièces est calculé, ces valeurs ne sont pas stockées. Une série vide n'apparaît pas sur le site d'après la maquette. Supprimer une série est autorisé : la clé étrangère détache ses œuvres (`SET NULL`), qui restent enregistrées sans série, et efface la référence à la pièce maîtresse ; aucune œuvre n'est supprimée. Retirer une œuvre de sa série efface aussi la pièce maîtresse si elle la désignait ; désigner une pièce maîtresse hors de la série est refusé par l'entité.

## Catégorie

Identifiant, nom français et anglais facultatif, indicateur de présence dans les filtres publics et dates de création/modification. La relation avec les œuvres est plusieurs-à-plusieurs. La fusion prévue transfère les associations vers une catégorie cible avant suppression de la source ; ses règles de conflits de noms restent à définir.

## Média

Identifiant, type (image ou vidéo), chemin de stockage interne unique, nom original, type MIME, taille, largeur, hauteur et dates. Pour une vidéo : durée, état et progression du traitement, erreur éventuelle ; les références aux fichiers dérivés restent à concevoir. Pour une image : les variantes générées restent à concevoir. Les textes alternatifs français et anglais (alt_fr, alt_en) sont portés par le média ; si une image devait être réutilisée dans des contextes nécessitant des textes distincts, une table d'association par contexte sera étudiée.

## Périmètre suivant

Films, sous-titres, textes des pages fixes, réglages, comptes et messages seront modélisés séparément. Les pages sont fixes et leurs textes modifiables ; le menu public comprend Œuvres, Séries, Films, À propos et Contact, et Séries figure aussi dans le pied de page. Les exemples de création de pages et de menu sans Séries dans Penpot ne reflètent pas ces décisions.

Décisions tranchées le 27 novembre 2026 et implémentées dans le schéma : identifiant public sous forme de slug unique par œuvre et par série ; prix modélisé par un booléen « sur demande » et un montant décimal facultatif en euros ; une seule œuvre mise en avant sur le site, avec unicité contrôlée par le service `GestionOeuvre` ; la suppression d'une série détache ses œuvres et efface sa pièce maîtresse ; textes alternatifs portés par le média. Le champ traduisible est une colonne par langue, conformément aux décisions du projet.

Restent à arbitrer ou à implémenter : purge automatique de la corbeille (30 jours), règles de conflits de noms pour la fusion des catégories, stockage et variantes des médias, champs requis à la publication. Les exemples de valeurs affichés dans Penpot ne sont pas des données réelles.
