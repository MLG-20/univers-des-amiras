# Direction UX/UI (révisée)
## L'Univers des Amiras — Boutique E-commerce

**Ambiance recherchée :** claire, minimale, la photographie produit domine — inspirée d'une référence envoyée par le client
**Portée :** direction visuelle + structure des parcours (wireframes décrits, sans code)

---

## 1. Vision (mise à jour)

Changement de cap suite à une référence fournie par le client : au lieu d'un univers sombre et immersif (ébène + or partout), on passe sur une direction **claire et minimale, où la photographie du produit/mannequin porte tout le poids visuel**. Le design s'efface pour laisser respirer les vêtements et accessoires — un header blanc discret, très peu d'éléments graphiques, des visuels pleine largeur.

L'or reste présent mais devient un **accent rare et précis** (bordures fines, boutons, détails typographiques) plutôt qu'une couleur de fond dominante. C'est un changement de rôle : d'ambiance à signature discrète.

---

## 2. Palette (révisée)

| Nom | Hex | Usage |
|---|---|---|
| Blanc cassé | `#FDFCFA` | Fond principal, header, sections claires |
| Ivoire | `#F3EDE1` | Fonds de section alternés, cartes |
| Noir doux | `#1A1815` | Texte principal, header sur fond clair |
| Or (accent unique) | `#B8923F` | Bordures fines, CTA, détails, survol — utilisé avec parcimonie, jamais en fond |
| Bordeaux profond | `#5C1A28` | Accent secondaire ponctuel (badges promo, états) |
| Gris chaud | `#8A8074` | Texte secondaire, légendes |

> Le noir/ébène et l'usage massif du doré (version précédente) sont conservés uniquement comme **option pour des sections ponctuelles** (ex: un bandeau "Notre histoire" en fin de page), pas comme identité dominante.

---

## 3. Typographie (inchangée dans l'esprit, ajustée dans l'usage)

- **Display** : serif élégante à empattements fins, désormais utilisée en **noir sur fond clair** plutôt qu'en clair sur fond sombre — même caractère, contexte différent.
- **Texte courant** : sans-serif humaniste, inchangée.
- **Utilitaire** : sans-serif condensée en petites capitales pour les labels, inchangée.

---

## 4. Élément signature (révisé)

**Les boîtes à bordure fine sur la photo** — inspirées directement de la référence client : des cadres fins (or ou blanc selon le contraste de la photo) qui encadrent un texte d'accroche ou un call-to-action, posés directement sur la photographie plutôt que sur un fond uni. C'est un principe simple, réutilisable sur le hero, les bannières de collection, et certaines fiches produit.

Le fondu doré animé de la version précédente est conservé mais en version très discrète, réservé au changement d'image dans une galerie produit (transition douce, jamais un effet dominant).

---

## 5. Principes de layout (révisés)

- **La photo domine, le design s'efface** : header minimal, fond clair, pas d'éléments graphiques qui font concurrence à l'image
- **Pleine largeur pour le hero et les bannières de collection** — comme sur la référence, l'image occupe tout l'espace disponible, pas de marge qui la réduit
- **Header épuré** : logo centré ou aligné à gauche, icônes fines (menu, recherche, panier), fond blanc ou transparent sur la photo du hero
- **Bordures fines** toujours privilégiées aux ombres portées, cohérent avec l'esprit initial
- **Coins légèrement arrondis à très nets selon le composant** — les cadres sur photo (boîtes CTA) restent anguleux comme sur la référence, pour un rendu net et actuel ; les cartes produit gardent un arrondi léger

---

## 6. Parcours clés — wireframes révisés

### 6.1 Page d'accueil (révisée)

```
┌─────────────────────────────────────────┐
│ [≡]      L'UNIVERS DES AMIRAS      [Q][🛍]│  ← header blanc minimal, logo centré
├─────────────────────────────────────────┤
│                                           │
│   [PHOTO PLEINE LARGEUR — mannequin/     │  ← photographie réelle en pleine largeur,
│    produit phare, fond réel type salon]  │     pas de fond coloré
│                                           │
│   ┌───────────────────────────┐          │
│   │  Découvrir la collection  │          │  ← boîte à bordure fine posée sur la photo
│   └───────────────────────────┘          │
│   ┌───────────────────────────┐          │
│   │  Voiles modal              │          │  ← deuxième boîte, information complémentaire
│   └───────────────────────────┘          │
├─────────────────────────────────────────┤
│  Collections — bannières pleine largeur  │  ← même principe que le hero, répété
│  [Photo Voiles]                          │     pour chaque collection, pas de grille
│  [Photo Parfums]                         │     de blocs colorés
│  [Photo Sacs]                            │
├─────────────────────────────────────────┤
│  Nouveautés — défilement horizontal      │  ← cartes produit classiques, fond clair
├─────────────────────────────────────────┤
│  Bandeau signature (optionnel, sombre)   │  ← seule zone qui peut reprendre
│  "Depuis Dakar..." fond ébène + or       │     l'ambiance premium sombre, en accent ponctuel
└─────────────────────────────────────────┘
```

### 6.1bis Page d'accueil — renforcement de l'attractivité

Un tunnel d'achat propre ne suffit pas à donner envie de rester : l'accueil doit aussi séduire et rassurer. Sections ajoutées à la structure précédente :

```
[...Hero, Collections, Nouveautés — inchangés...]
├─────────────────────────────────────────┤
│  Indicateurs de confiance (bandeau fin)  │  ← paiement sécurisé, livraison Dakar,
│  Paiement sécurisé / Livraison rapide /  │     retour possible — rassure sans
│  Retour facile                            │     être criard, format icône + texte court
├─────────────────────────────────────────┤
│  Avis clientes (2-3 témoignages)         │  ← preuve sociale, texte court + prénom,
│  "Le voile est magnifique..." — Aïda     │     garder un ton crédible et sincère
├─────────────────────────────────────────┤
│  Notre histoire (storytelling court)     │  ← 2-3 lignes + photo, humanise la marque,
│  photo + texte, fond clair                │    différencie d'un site "boîte anonyme"
├─────────────────────────────────────────┤
│  Suivez-nous (grille type Instagram)     │  ← 4-6 photos carrées, lien réseaux sociaux,
│                                           │     donne une impression de marque vivante
├─────────────────────────────────────────┤
│  Newsletter (bandeau discret)            │  ← "Soyez la première informée des
│                                           │     nouveautés" + champ email, un seul CTA
├─────────────────────────────────────────┤
│  Bandeau signature sombre + or           │  ← inchangé, clôture la page
├─────────────────────────────────────────┤
│  Footer complet (voir 6.7)               │
└─────────────────────────────────────────┘
```

Principe général pour rendre la page attirante sans la surcharger : **chaque section a un rôle émotionnel précis** (vendre, rassurer, prouver, humaniser, fidéliser) et n'apparaît qu'une fois — pas de répétition de blocs similaires qui donnerait une impression de remplissage.

### 6.2 à 6.6

Les parcours Catalogue, Fiche produit, Panier, Tunnel de commande et Espace client restent structurellement identiques à la version précédente du document (voir historique) — seul le traitement visuel change : fond clair dominant, bordures fines dorées en accent, photographie réelle en priorité partout où c'est pertinent (fiche produit notamment, qui doit désormais s'appuyer sur une vraie galerie photo pleine largeur plutôt que sur un cadre neutre).

### 6.7 Pages institutionnelles (nouvelles, essentielles à ne pas oublier)

Ces pages ne génèrent pas directement de vente mais sont indispensables à la crédibilité d'une boutique en ligne — leur absence est souvent ce qui fait fuir une cliente qui hésite encore.

**À propos**
```
┌─────────────────────────────────────────┐
│  Photo pleine largeur (fondatrice/       │
│  atelier/inspiration de la marque)       │
├─────────────────────────────────────────┤
│  Histoire de la marque (texte + valeurs) │  ← pourquoi "Amiras", ce qui différencie
│  ce qui inspire les créations             │     la marque, ancrage Sénégal/authenticité
├─────────────────────────────────────────┤
│  Photo + citation ou philosophie         │
└─────────────────────────────────────────┘
```

**Contact**
```
┌─────────────────────────────────────────┐
│  Formulaire simple (nom, email, message) │  ← 3-4 champs maximum, jamais un
├─────────────────────────────────────────┤
│  Coordonnées directes                    │     formulaire long et intimidant
│  WhatsApp / téléphone / email / adresse  │  ← le WhatsApp est probablement LE canal
│                                           │     le plus utilisé par la clientèle cible,
│                                           │     à mettre en avant clairement
├─────────────────────────────────────────┤
│  Horaires / zone de livraison couverte   │
└─────────────────────────────────────────┘
```

**FAQ**
Questions fréquentes organisées par thème (Commande, Livraison, Paiement, Retours) en accordéon — réduit la charge du service client une fois le site lancé.

**Livraison & Retours**
Page dédiée détaillant zones couvertes, délais, frais, et politique de retour claire — cette page est souvent consultée juste avant l'achat, elle doit rassurer sans ambiguïté.

**Mentions légales / CGV**
Obligatoire pour la crédibilité et la conformité — identité du vendeur, conditions générales de vente, politique de confidentialité (même basique, sa présence rassure plus que son contenu détaillé).

**Page 404**
Cohérente avec l'identité de marque (pas une page d'erreur générique du serveur), avec un lien de retour clair vers l'accueil ou le catalogue.

### 6.8 Plan du site — vue d'ensemble

| Page | Rôle |
|---|---|
| Accueil | Séduire, orienter, rassurer |
| Catalogue (par catégorie) | Permettre la recherche/découverte |
| Fiche produit | Convaincre à l'achat |
| Panier | Faciliter la modification avant achat |
| Tunnel de commande | Convertir sans friction |
| Espace client | Fidéliser, faciliter le suivi |
| À propos | Humaniser la marque, créer la confiance |
| Contact | Lever les derniers doutes, canal direct |
| FAQ | Réduire la friction/incertitude |
| Livraison & Retours | Rassurer avant l'achat |
| Mentions légales / CGV | Crédibilité, conformité |
| Page 404 | Ne jamais perdre un visiteur égaré |

---

## 7. Ton de la copie (inchangé)

Voix active, directe, cohérence du vocabulaire bouton/confirmation, messages d'erreur clairs, ton élégant mais chaleureux — voir version précédente, aucun changement nécessaire ici.

---

## 8. Accessibilité & responsive (inchangé, renforcé)

Le contraste est en réalité plus simple à garantir avec cette nouvelle direction claire (texte noir sur fond blanc/ivoire) qu'avec l'ancienne version sombre — un vrai bénéfice secondaire de ce changement de cap. Les boîtes à bordure fine posées sur photo doivent cependant être testées avec soin : le texte à l'intérieur doit rester lisible quelle que soit la photo en arrière-plan (prévoir un léger fond semi-opaque derrière le texte si la photo est trop chargée visuellement).

---

## 9. Ce qui a changé par rapport à la version initiale

| Aspect | Avant | Maintenant |
|---|---|---|
| Ambiance générale | Sombre, immersive, ébène + or | Claire, minimale, photo dominante |
| Rôle du doré | Couleur de fond et d'accent large | Accent rare (bordures, CTA uniquement) |
| Hero | Bloc de couleur avec titre | Photo réelle pleine largeur avec boîtes à bordure |
| Header | Discret sur fond sombre | Minimal sur fond blanc |
| Zone sombre/dorée | Dominante sur tout le site | Réservée à une section ponctuelle (bandeau de marque) |

---

*Document de direction — version révisée après retour client. Prochaine étape : nouvelle maquette visuelle alignée sur cette direction.*
