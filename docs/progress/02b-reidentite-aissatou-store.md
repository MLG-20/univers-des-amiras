# Ré-identité « Aissatou Store » — plan de reprise

**Statut :** en attente de validation d'Ahmed avant exécution
**Déclencheur :** rapport d'identité visuelle V3 transmis par la cliente le 2026-07-21 (`docs/Aissatou_Store_Rapport_Identite_Visuelle.pdf`, 17 pages, auteurs Cheikh Ahmadou Bamba Sow et Mouhamed Sow)

---

## 1. Ce que le rapport change — et ce qu'il ne change pas

Le document est un système de marque complet : positionnement, logo, palette, typographie, langage graphique, maquettes d'interface e-commerce et règles de cohérence. Il **remplace** `docs/files/ux-ui-direction-amiras.md`, qui devient un document d'archive.

### Non impacté (vérifié dans le code)
Modèles, migrations, `CartService` et l'isolation panier invité/compte, comptes clients, recherche et filtres, panel Filament, pipeline d'images, stratégie de cache, suite de tests. **La sécurité et la scalabilité ne sont pas concernées** : une identité visuelle vit dans la couche présentation, elle ne touche ni le modèle de données, ni les frontières de confiance, ni les requêtes.

Point favorable relevé à l'audit : **aucune couleur hex en dur dans les 46 vues Blade**. Toutes les couleurs passent par les tokens Tailwind `amiras-*` déclarés dans `tailwind.config.js`. La bascule de palette est donc une substitution centralisée, pas une réécriture des vues.

### Impacté
Palette, typographies, nom de marque, arborescence des catégories, et quelques composants dont le traitement est désormais explicitement interdit (voir §4).

---

## 2. Palette — « Atelier Nocturne »

| Rôle (PDF) | Nom | Hex | Part visée |
|---|---|---|---|
| Texte / ancrage | Encre Noire | `#17151B` | 38 % |
| Signature / profondeur | Cassis Laqué | `#4A1833` | 25 % |
| Fond / respiration | Parchemin | `#F4E6D5` | 20 % |
| Contrepoint / textile | Sauge Fumée | `#A7AE91` | 10 % |
| Edit Cut / signal | Rouge Garance | `#9F2D40` | 7 % |

**L'or (`#B8923F`) disparaît complètement du système.** C'était la couleur d'accent de l'ancienne direction ; elle n'a pas d'équivalent dans la nouvelle.

### Correspondance des tokens

Les tokens passent d'un nommage par couleur (`amiras-gold`) à un nommage **par rôle** (`brand-accent`) : le prochain changement d'identité ne touchera alors que `tailwind.config.js`, pas les vues.

| Token actuel | Occurrences | Devient | Note |
|---|---|---|---|
| `amiras-ink` `#1A1815` | 164 | `brand-ink` `#17151B` | 1:1, quasi identique |
| `amiras-cream` `#FDFCFA` | 56 | `brand-surface` | Fond de page. Le Parchemin pur en pleine page est trop chargé — teinte claire dérivée du Parchemin. |
| `amiras-ivory` `#F3EDE1` | 12 | `brand-parchment` `#F4E6D5` | 1:1, sections et cartes |
| `amiras-taupe` `#8A8074` | 59 | `brand-muted` | Texte secondaire. Le PDF ne fournit pas de gris — dérivé de l'Encre désaturée. |
| `amiras-bordeaux` `#5C1A28` | 8 | `brand-signature` `#4A1833` | 1:1 |
| `amiras-gold` `#B8923F` | **105** | **contextuel** | Seul vrai point de jugement, détaillé ci-dessous |

### Le cas `amiras-gold` (105 occurrences)

L'or portait trois rôles distincts qui se séparent dans le nouveau système :

| Usage actuel | Nb | Devient | Pourquoi |
|---|---|---|---|
| `bg-amiras-gold` (boutons pleins) | 17 | `bg-brand-accent` (Garance) | CTA principal. ⚠️ Le PDF impose **un seul CTA Garance par écran** — à auditer page par page, pas à substituer aveuglément. |
| `ring-amiras-gold` (focus) | 14 | `ring-brand-accent` (Garance) | Le PDF désigne explicitement Garance comme couleur d'état actif / focus. |
| `border-amiras-gold` (bordures fines) | 31 | `border-brand-signature` (Cassis) | Le PDF réserve Garance au signal ; une bordure permanente n'est pas un signal. |
| `text-amiras-gold` (détails typo) | 41 | Cassis ou Encre selon le fond | Contraste à vérifier au cas par cas. |
| `via-amiras-gold` (dégradés) | 2 | **supprimé** | Voir §4. |

---

## 3. Typographie

Le rapport impose **Canela** (titres, voix éditoriale) et **Neue Haas Grotesk** (interface).

> ⚠️ **Ce sont deux familles commerciales payantes** (Commercial Type / Monotype), dont la licence webfont est facturée selon le trafic. Elles ne peuvent pas être mises en ligne sans licence achetée par la cliente.

**Décision prise avec Ahmed :** partir sur des substituts libres au caractère proche, et re-basculer plus tard si la cliente achète les licences. Comme les polices passent par `tailwind.config.js` (`font-display`, `font-sans`), la bascule future coûtera deux lignes.

Échelle typographique du PDF, à reprendre telle quelle : H1 56-88 px · H2 32-48 px · Produit 22-30 px · Corps 16-18 px · Navigation 12-14 px · Légende 11-13 px.

---

## 4. Traitements à supprimer (interdits par le rapport)

La page 9 liste explicitement ce qu'il faut éviter : *motif trop dense · courbes décoratives arbitraires · rouge en grand aplat · effets brillants numériques*. La page 16 ajoute cinq principes non négociables.

Conséquences directes sur du code existant :

- **Le halo doré animé des cartes « L'expérience Amiras »** (`resources/css/app.css`, dégradé conique animé via `@property --trust-angle`) tombe sous « effets brillants numériques ». **À retirer**, pas à recolorer en Garance — le recolorer produirait en plus un rouge animé, doublement hors système.
- **Les dégradés `via-amiras-gold`** dans les bandeaux : à remplacer par des aplats ou les cadres ouverts du langage graphique (§5).
- **Les fonds en grand aplat Garance** : Garance est plafonné à 7 % de la surface.

C'est une simplification nette du CSS, pas une dette.

---

## 5. Langage graphique à introduire

Le rapport dérive quatre motifs du logo, à utiliser à la place des ornements actuels :
*Passage Frame* (cadre ouvert accueillant image ou message) · *Fold Path* (trajectoire souple) · *Edit Cut* (incision Garance brève signalant un choix) · *Curated Window* (recadrage mettant en valeur un objet).

Le principe « cadre fin posé sur la photo » de l'ancienne direction survit sous le nom **Passage Frame** — c'est la seule continuité visuelle entre les deux identités.

Jetons d'interface (p.13) : rayon 0-4 px · espacement 8/16/24/40 · ligne 1 px · focus Garance · fond Parchemin.

---

## 6. Arborescence — écart avec le catalogue actuel

| Rapport (p.10-12) | Chez nous |
|---|---|
| Nouveautés · Hijabs · Foulards · Cols · Parfums · Cadeaux · Collections · Journal | Voiles & Hijabs · Parfums · Sacs à main · Collants · Vêtements |

Les catégories sont des **données**, pas du code : un `CatalogueSeeder` réécrit suffit. Deux entrées font exception et sont de vraies fonctionnalités :

- **Collections** (Atelier Nocturne, Essentiels, Saison, L'art d'offrir) — notion transverse à une catégorie : un produit appartient à une collection *et* à une catégorie. Demande une table et une relation.
- **Journal** — un module éditorial (blog) complet.

---

## 7. Hors périmètre du cahier des charges — à arbitrer avec la cliente

Quatre éléments apparaissent dans les maquettes sans exister dans le cahier des charges. Ce sont des **fonctionnalités**, pas du design : elles ne peuvent pas être absorbées silencieusement dans la Phase 2.

1. **Journal** — module éditorial complet (modèle, admin, routes, pages).
2. **Collections** — table + relation + filtre.
3. **Wishlist** — icône cœur présente dans la navigation (p.10, 11, 12). Aucune liste d'envies n'est prévue à ce jour.
4. **Filtre Matière** (soie / modal / coton / laine & cachemire) — nouvel attribut produit. Le moteur de filtres existe déjà depuis la sous-étape 3 ; c'est l'attribut qui manque.

En revanche, ces éléments des maquettes sont **déjà couverts** : recherche, compte, tiroir panier avec compteur, filtres prix et disponibilité, grille produit, badges promo.

Les **labels commerciaux** *Sélectionné / Édition limitée / Nouveauté* (p.13) sont triviaux : un champ énuméré sur le produit. Le rapport précise qu'ils doivent rester rares et jamais décoratifs.

---

## 8. Deux contradictions à remonter à la cliente

1. **Devise en euros.** Tout le rapport affiche des prix en EUR (42,00 € · 68,00 € · 110,00 €), alors que le projet cible le Sénégal, avec Wave et Orange Money envisagés (cahier des charges §Paiement). À trancher : c'est la devise d'affichage **et** les moyens de paiement de la Phase 5 qui en dépendent.
2. **Licences typographiques** (§3) — décision budget.

---

## 9. Ordre d'exécution proposé

1. Tokens sémantiques dans `tailwind.config.js` + substitution des classes dans les vues (mécanique sauf les 105 `amiras-gold`, §2).
2. Nettoyage CSS : retrait du halo doré animé et des dégradés interdits (§4).
3. Typographies de substitution + échelle typographique du rapport.
4. Renommage de marque : `APP_NAME`, titres, e-mails, pied de page, README, docs. *(Décision d'Ahmed : « Aissatou Store » partout ; le dépôt et le dossier gardent leur nom actuel, sans impact fonctionnel.)*
5. Reprise de la page d'accueil selon la maquette p.10, pour montrer à la cliente.
6. Nouvel arbre de catégories dans le seeder de démo.
7. Reste du site (catalogue, fiche produit, panier, compte) aligné sur les maquettes p.11-13.

Les points du §7 restent hors de cette liste tant que la cliente n'a pas tranché.
