# Maintenir la documentation

À chaque changement validé qui modifie le comportement, l'architecture, l'installation, la configuration ou les procédures, mettre à jour les pages concernées dans le même ensemble de modifications. Documenter l'état réel, les prérequis et les limites ; distinguer les idées et les fonctionnalités prévues de ce qui est opérationnel.

Lors d'une revue, vérifier si la documentation doit changer et contrôler sa compilation avec `cd docs && npm run build`. Une modification sans incidence sur la documentation ne nécessite pas de changement artificiel.

Les instructions partagées des agents se trouvent dans `AGENTS.md` à la racine ; `CLAUDE.md` y renvoie pour Claude Code. Les commandes de contrôle sont rassemblées dans le [guide des vérifications](/guide/verifications).

Le contexte AIDD est conservé dans `aidd_docs/memory/` et référencé dans les instructions des deux assistants. Le mettre à jour lorsque les décisions ou l'état du projet changent.
