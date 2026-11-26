import { defineConfig } from 'vitepress'

export default defineConfig({
  title: "Site d'art",
  description: 'Documentation du projet',
  lang: 'fr-FR',
  themeConfig: {
    nav: [{ text: 'Accueil', link: '/' }, { text: 'Guide', link: '/guide/projet' }],
    sidebar: [{ text: 'Projet', items: [
      { text: 'Présentation', link: '/guide/projet' },
      { text: 'Développement', link: '/guide/developpement' },
      { text: 'Intégration Penpot', link: '/guide/penpot' },
      { text: 'Vérifications', link: '/guide/verifications' },
      { text: 'CI et déploiement', link: '/guide/ci-cd' },
      { text: 'Documentation', link: '/guide/documentation' }
    ] }],
    outline: { label: 'Sur cette page' },
    docFooter: { prev: 'Précédent', next: 'Suivant' }
  }
})
