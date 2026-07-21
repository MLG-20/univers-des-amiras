# Phase 2 — Expérience client : journal d'avancement

**Statut : en cours.** Découpée en sous-étapes validées une par une avec Ahmed (voir [feedback_workflow] côté suivi de projet) :

1. ✅ **Catalogue public** (listing, catégories, fiche produit) — fait
2. ✅ **Panier** (session invité + compte, fusion, isolation) — fait
3. ✅ **Comptes clients** (adresses, historique commandes, infos perso) — fait
4. ✅ **Mobile + performance** (images optimisées, cache, lazy loading) — fait
5. ✅ **Page d'accueil** (hero, collections, nouveautés, réassurance) — fait, ajoutée hors périmètre initial à la demande d'Ahmed pour avoir un support visuel à montrer à la cliente
6. ✅ **Attractivité visuelle sans photos réelles** (données de démo réalistes, placeholders illustrés, bannières catégorie) — fait, ajoutée après retour d'Ahmed sur le manque d'attrait du rendu
7. ⏳ Tests + revue sécurité de fin de phase (XSS, CSRF, IDOR adresses/historique) — à venir

Référence complète du périmètre : [docs/files/02-experience-client.md](../files/02-experience-client.md).

---

## Sous-étape 1 — Catalogue public (fait)

### Ce qui a été construit
- Routes publiques dans `routes/shop.php` : `/` et `/catalogue` (listing paginé, 12 produits actifs/page), `/categories/{slug}` (catégorie + ses sous-catégories + ses produits), `/produits/{slug}` (fiche détaillée).
- Contrôleurs `App\Http\Controllers\Shop\{ProductController,CategoryController}` — chargement eager (`with(['category','images','variants'])`) pour éviter le N+1 sur les listings, comme exigé section 4 du cahier des charges.
- `Product::isInStock()` et `Product::primaryImage()` (dans `app/Models/Catalogue/Product.php`) calculent à partir des relations déjà chargées, pas de requête supplémentaire par produit affiché.
- Sécurité : un produit/catégorie `is_active=false` ou soft-supprimé renvoie **404** côté client (même logique que l'API v1) — évite l'énumération de fiches désactivées.
- Layout public dédié (`resources/views/layouts/shop.blade.php` + composant `App\View\Components\ShopLayout`), séparé du layout authentifié Breeze (`layouts/app.blade.php`) et du panel Filament.
- Composant réutilisable `x-shop.product-card` (image, nom, prix, badge rupture de stock).
- Catégories racines (nav du header) injectées automatiquement à toutes les vues boutique via un `View::composer('layouts.shop', ...)` dans `AppServiceProvider` — évite de les repasser manuellement dans chaque contrôleur.

### Direction visuelle
Alignée sur [docs/files/ux-ui-direction-amiras.md](../files/ux-ui-direction-amiras.md) (oubliée dans un premier jet, corrigée après retour d'Ahmed — voir commit "Aligne le catalogue public sur la direction UX/UI") :
- Palette dans `tailwind.config.js` (`amiras.cream/ivory/ink/gold/bordeaux/taupe`), utilisée partout à la place des gris Tailwind par défaut.
- Typographie : `font-display` (Playfair Display, titres) + `font-sans` (Work Sans, texte courant), chargées via Bunny Fonts dans les 3 layouts (`app`, `guest`, `shop`).
- Bordures fines dorées en accent (survol carte produit, sélecteur de variante) plutôt que des ombres ; arrondi léger sur les cartes produit (`rounded-md`), pas d'arrondi prononcé.
- **Non fait à ce stade** (hors périmètre fonctionnel du fichier `02-experience-client.md`, à traiter à part si Ahmed le demande) : hero pleine largeur avec boîtes à bordure fine, bandeau de confiance, avis clients, storytelling, pages institutionnelles (À propos, Contact, FAQ, Livraison & Retours, Mentions légales, 404 sur-mesure).

### Choix technique : recherche/filtres réactifs
Pas encore implémentés (prochaine sous-étape probable après le panier, ou à combiner avec le panier). Décision prise avec Ahmed : **Alpine.js + fetch** vers un endpoint JSON/partiel, plutôt que Livewire — pour rester cohérent avec le choix Blade/Breeze déjà fait côté public et parce que Livewire ajoute un aller-retour serveur plus lourd, à contre-courant de l'objectif de performance mobile (< 2s) visé section 4 du cahier des charges. Livewire reste réservé au panel Filament admin.

### Tests
`tests/Feature/Shop/CatalogueTest.php` — 8 tests : produits actifs uniquement sur l'accueil, pagination, 404 sur produit inactif/soft-supprimé, variantes actives affichées avec leur prix, isolation des produits par catégorie, 404 sur catégorie inactive, sous-catégories listées.

### Comment vérifier
```bash
php artisan test --filter=CatalogueTest
php artisan serve   # puis / , /catalogue, /categories/{slug}, /produits/{slug}
```

---

## Sous-étape 2 — Panier (fait)

### Ce qui a été construit
- Domaine `App\Models\Cart\{Cart,CartItem}` + migrations `carts`/`cart_items`. `carts.user_id` et `carts.session_id` sont uniques et mutuellement exclusifs (un panier = soit un invité via session, soit un compte).
- `App\Services\CartService` centralise toute la logique métier (résolution du panier courant, ajout, changement de quantité, suppression, fusion invité→compte) — le contrôleur ne fait qu'orchestrer et traduire les erreurs en messages.
- Prix **jamais stocké** sur `cart_items` : `CartItem::unitPrice()`/`lineTotal()` recalculent à partir du produit/variante à chaque affichage, donc le panier reflète toujours le prix catalogue actuel (pas un instantané figé).
- Vérification de stock **à l'ajout et à la mise à jour de quantité** (pas seulement à l'affichage), variante obligatoire si le produit en a, produit/variante inactifs rejetés.
- Ajouter deux fois le même produit/variante incrémente la ligne existante au lieu de dupliquer.

### Sécurité (section 3 du cahier des charges)
- **Isolation stricte des paniers invités** : le panier courant est résolu uniquement via `$request->session()->getId()` côté serveur — jamais un id de panier/session accepté depuis la requête (query, champ caché). Deux sessions différentes ne peuvent physiquement pas se voir.
- **Anti-IDOR sur les lignes de panier** : `CartController::authorizeItemOwnership()` vérifie que la ligne appartient bien au panier du visiteur courant avant toute modification/suppression — sinon 403. Sans ce contrôle, l'id auto-incrémenté d'une ligne aurait pu être deviné et modifié par un autre visiteur.
- **Fusion panier invité → compte** : à la connexion (`AuthenticatedSessionController`) et à l'inscription (`RegisteredUserController`), l'id de session est capturé **avant** `authenticate()`/`Auth::login()` (qui régénèrent l'id en interne), puis passé explicitement à `CartService::mergeGuestCartIntoUser()`. La fusion ne fait donc jamais confiance à un identifiant fourni par le client — seulement à celui de la requête HTTP en cours.
- **CSRF** : aucune protection ajoutée à la main — le groupe de middleware `web` (par défaut, non modifié) protège déjà toutes les routes panier. Vérifié manuellement (`curl` sans jeton → `419`) plutôt que via un test automatisé, car l'environnement de test Laravel contourne volontairement la vérification CSRF (`runningUnitTests()`), comme pour les tests d'auth Breeze livrés par défaut.

### Piège rencontré : tester la persistance de session
Le client de test Laravel ne renvoie pas automatiquement les cookies d'une requête à l'autre (contrairement à un navigateur), donc deux appels `$this->post()`/`$this->get()` séparés utilisent par défaut deux sessions différentes. Après une fausse piste (rejouer le `Set-Cookie` capturé, ou le rechiffrer à la main — les deux échouent silencieusement), la bonne méthode est de passer l'id de session **en clair** à `withCookie()` : le client de test le chiffre lui-même avant l'envoi (`MakesHttpRequests::prepareCookiesForRequest()`). Voir `tests/Feature/Shop/CartTest::asGuestSession()`.

### Tests
`tests/Feature/Shop/CartTest.php` — 13 tests : variante obligatoire, produit sans variante, persistance panier invité entre requêtes, fusion des lignes dupliquées, refus si stock insuffisant, isolation entre deux sessions invité indépendantes, anti-IDOR sur une ligne d'un autre visiteur, fusion à la connexion, plafonnement de la quantité fusionnée au stock disponible, suppression, quantité à 0 = suppression, total recalculé sur le prix courant, produit inactif rejeté.

### Comment vérifier
```bash
php artisan test --filter=CartTest
php artisan serve   # ajouter un produit au panier depuis /produits/{slug}, voir /panier
```

---

## Sous-étape 3 — Recherche et filtres du catalogue (fait)

### Ce qui a été construit
- `App\Http\Requests\Shop\FilterProductsRequest` valide et nettoie tous les paramètres de filtre avant qu'ils n'atteignent la base : `q` (texte, max 100 caractères), `category_id` (doit exister **et être une catégorie active**), `min_price`/`max_price` (numériques, `max_price` ne peut être inférieur à `min_price` **que si `min_price` est réellement fourni** — piège rencontré, voir plus bas), `in_stock` (booléen).
- `Product::scopeFilter()` (dans `app/Models/Catalogue/Product.php`) assemble la requête à partir de ces valeurs déjà validées — réutilisé à l'identique par `ProductController::index` (catalogue global) et `CategoryController::show` (page catégorie), pour ne pas dupliquer la logique de filtre à deux endroits.
- Recherche texte : correspond au nom du produit **ou** au nom de sa catégorie (`orWhereHas('category', ...)`).
- Filtre stock : un produit sans variante est considéré disponible (pas de suivi de stock à ce niveau, cohérent avec `Product::isInStock()`) ; un produit avec variantes doit avoir au moins une variante active en stock.
- Interaction réactive **sans rechargement de page** (choix déjà acté en sous-étape 1) : le composant Alpine `productFilters` (`resources/js/shop-filters.js`, enregistré dans `app.js`) fait un `fetch()` vers la même route catalogue/catégorie avec un en-tête `X-Requested-With: XMLHttpRequest`. Le contrôleur détecte cet en-tête (`$request->ajax()`) et renvoie uniquement le fragment `shop.partials.product-grid` (grille + pagination), pas la page complète — aucune logique d'affichage dupliquée en JS, tout le rendu reste côté serveur.
- Les liens de pagination (générés par `$products->links()`) sont interceptés en JS après chaque rendu du fragment, pour rester dans le même flux fetch/remplacement plutôt que de recharger la page au changement de page.
- L'URL du navigateur est mise à jour via `history.pushState` à chaque filtre appliqué (recherche partageable/navigable au bouton retour), et un écouteur `popstate` réapplique le fragment correspondant.
- Nouveau composant Blade `x-shop.filter-bar` (recherche, catégorie — uniquement sur le catalogue global, la page catégorie est déjà scopée —, prix min/max, case "en stock"), inclus dans `shop/index.blade.php` et `shop/category.blade.php`.

### Sécurité
- **Anti-injection sur les paramètres de filtre** (exigence explicite section 3 du cahier des charges) : tout passe par `FilterProductsRequest` — un `category_id` invalide ou inactif est silencieusement ignoré (pas d'erreur qui révèle l'existence d'une catégorie désactivée), un prix non numérique ou une recherche trop longue est rejetée.
- **Échappement des jokers LIKE** : le terme de recherche est échappé (`addcslashes($q, '%_\\')`) avant d'être inséré dans un `LIKE %...%` — sans ça, un utilisateur tapant `%` ou `_` pourrait élargir la correspondance au-delà de l'intention (pas une faille d'injection SQL grâce aux requêtes préparées, mais un comportement de recherche incorrect/exploitable pour de l'énumération).
- **La page catégorie ne peut pas être détournée via `category_id`** : `CategoryController::show` force `category_id` à `null` dans les filtres appliqués, quel que soit ce qui est passé en paramètre d'URL — on reste toujours dans le périmètre de la catégorie de la route, jamais dans une autre.

### Piège rencontré : la règle de validation `gte:min_price`
La règle Laravel `gte:min_price` sur `max_price` échoue **même quand `min_price` est absent de la requête** (elle compare `max_price` à un champ vide plutôt que d'ignorer la comparaison), ce qui rejetait à tort tout filtre "prix max seul". Corrigé en n'ajoutant la règle `gte:min_price` que si `min_price` est effectivement présent (`$this->filled('min_price')`).

### Tests
`tests/Feature/Shop/CatalogueFilterTest.php` — 11 tests : recherche par nom de produit, recherche par nom de catégorie, jokers LIKE traités comme texte littéral, filtre catégorie, catégorie inactive ignorée silencieusement, plage de prix, plage de prix invalide rejetée (422), filtre stock disponible, filtres combinés sur la page catégorie, `category_id` de l'URL sans effet sur la page catégorie, requête AJAX ne renvoyant que le fragment grille.

### Comment vérifier
```bash
php artisan test --filter=CatalogueFilterTest
php artisan serve   # /catalogue puis utiliser la barre de recherche/filtres, vérifier que l'URL se met à jour sans rechargement
```

### Vérifié en navigateur réel (Playwright headless, session ultérieure)
Testé avec Chromium piloté par Playwright (installé temporairement pour ce test, aucune dépendance ajoutée au projet) contre `php artisan serve` local : recherche texte avec debounce (400 ms), filtre prix, mise à jour de l'URL via `history.pushState` (`?q=...`, `?min_price=...`), bouton retour du navigateur qui redéclenche `popstate` et restaure correctement le fragment + l'URL précédente, aucune erreur console/page. Comportement confirmé fonctionnel de bout en bout.

Non testé faute de données : l'interception des liens de pagination (seulement 9 produits en base de test, une seule page à 12/page) — à revérifier une fois le catalogue plus fourni en produits.

---

## Sous-étape 3 — Comptes clients (fait)

Décisions prises avec Ahmed avant implémentation : plusieurs adresses par compte avec une adresse par défaut (carnet d'adresses) ; infos personnelles modifiables = nom, email, téléphone, mot de passe.

### Ce qui a été construit
- Domaine `App\Models\Customer\Address` (table `customer_addresses`, migration séparée de la table `addresses` par défaut de Laravel pour éviter toute ambiguïté avec un futur usage du nom générique) — `recipient_name`, `phone`, `city`, `address_line`, `landmark` (repère, optionnel — pas de code postal fiable au Sénégal), `is_default`.
- `App\Services\AddressService` centralise la logique d'unicité de l'adresse par défaut (même pattern que `CartService` en sous-étape 2) : la création de la première adresse d'un compte la marque automatiquement par défaut même sans case cochée ; définir une nouvelle adresse par défaut désactive l'ancienne dans la même transaction ; supprimer l'adresse par défaut en réassigne une autre automatiquement — un compte avec des adresses n'est jamais sans adresse par défaut.
- `App\Http\Controllers\Account\AddressController` (index/store/update/destroy) sous `/compte/adresses`, `App\Http\Controllers\Account\OrderController` sous `/compte/commandes`.
- Champ `phone` ajouté à `users` (migration `add_phone_to_users_table`) et au formulaire d'infos personnelles Breeze existant (`ProfileUpdateRequest`, `update-profile-information-form.blade.php`), en plus de nom/email/mot de passe déjà gérés par Breeze.
- Nouvel espace « Mon compte » avec sous-navigation (`account.partials.nav` : Informations personnelles / Adresses / Commandes), restylé avec le layout boutique (`x-shop-layout`) et la palette amiras au lieu du layout Breeze par défaut gris/indigo — cohérence avec le reste du site public, dans la continuité de la remarque déjà faite par Ahmed en sous-étape 1 sur l'alignement UX/UI. Les composants Blade partagés (`x-primary-button`, `x-text-input`, `x-input-label`, `x-secondary-button`) et les libellés Breeze par défaut (mot de passe, suppression de compte) ont été traduits/restylés au passage puisqu'ils sont aussi utilisés par login/register — non dupliqué page par page.
- Historique de commandes : page et route existantes dès maintenant (cahier des charges section 2.3 le demande explicitement), mais affiche un état vide (« Vous n'avez pas encore passé de commande ») — **aucun modèle `Order` créé**, le domaine Commande complet est du ressort de la Phase 3. Créer un modèle à moitié fonctionnel maintenant aurait été prématuré.

### Sécurité
- **Anti-IDOR sur les adresses** : `AddressController::authorizeOwnership()` vérifie que `address->user_id` correspond à l'utilisateur authentifié avant toute modification/suppression (403 sinon) — même exigence et même pattern que l'anti-IDOR déjà appliqué aux lignes de panier en sous-étape 2 (cahier des charges section 3, « protection des données personnelles »).
- **CSRF** : couvert par le middleware `web` par défaut (non modifié), comme pour le panier.
- **XSS** : aucune donnée d'adresse n'est affichée sans échappement Blade (`{{ }}`), pas de `{!! !!}` utilisé.

### Tests
- `tests/Feature/Account/AddressTest.php` — 7 tests : accès refusé aux invités, première adresse automatiquement par défaut, changement de défaut désactive l'ancienne, suppression de l'adresse par défaut en réassigne une autre, anti-IDOR sur modification/suppression de l'adresse d'un autre utilisateur, validation des champs obligatoires.
- `tests/Feature/ProfileTest.php` — ajout d'un test de mise à jour du téléphone.
- Suite complète : 84 tests, tous verts. Style vérifié avec `./vendor/bin/pint --test`.

### Vérifié en navigateur réel (Playwright headless)
Connexion, navigation vers `/profile`, `/compte/adresses` (ajout d'une adresse, affichage de la carte avec badge « Par défaut »), `/compte/commandes` (état vide) — aucune erreur console, rendu cohérent avec la palette amiras.

### Comment vérifier
```bash
php artisan test --filter=AddressTest
php artisan serve   # se connecter puis /profile, /compte/adresses, /compte/commandes
```

---

## Sous-étape 4 — Mobile + performance (fait)

Décision prise avec Ahmed avant implémentation : redimensionnement automatique des images côté serveur (option complète plutôt que lazy loading seul), quitte à prendre un peu plus de temps.

### Ce qui a été construit
- **Redimensionnement automatique des images produit** : `intervention/image` (v4, driver GD) ajouté. `App\Services\ImageVariantGenerator` génère 3 variantes WebP par image (`thumb` 480px, `medium` 800px, `large` 1400px de large max, qualité 80, ratio conservé — pas de recadrage serveur, le cadrage visuel reste géré en CSS `object-cover` comme avant). `App\Observers\ProductImageObserver` déclenche la génération à la création/modification d'une `ProductImage` et supprime les variantes à la suppression. `ProductImage::sizedUrl($size)` retombe sur l'image d'origine si la variante n'existe pas encore (image uploadée avant cette fonctionnalité, ou génération ayant échoué) — jamais d'image cassée côté client.
- Commande `php artisan images:generate-variants` pour générer les variantes des images déjà en base (uploadées avant l'ajout de cette fonctionnalité).
- Toutes les images publiques (carte produit, fiche produit, panier) utilisent maintenant `srcset`/`sizes` avec les tailles adaptées à leur contexte d'affichage (ex. carte catalogue : `thumb`/`medium`, galerie fiche produit : `medium`/`large`), et `loading="lazy"` partout sauf la première image de la galerie produit (chargée `eager`, visible immédiatement).
- **Galerie produit swipeable au tactile** : geste de swipe gauche/droite sur l'image principale de la fiche produit (`touchstart`/`touchend`, seuil de 40px) pour naviguer entre les images, en plus des vignettes cliquables déjà existantes — répond à l'exigence « navigation tactile fluide » du cahier des charges (section 2.4).
- **Cache de la navigation boutique** : les catégories racines (affichées dans le header sur chaque page boutique) sont mises en cache 1h (`Cache::remember`), invalidées automatiquement à la sauvegarde/suppression d'une catégorie (`Category::booted()`) — évite une requête DB à chaque page vue pour une donnée qui change rarement.

### Piège rencontré : mise en cache d'Eloquent et durcissement sécurité Laravel
Première implémentation : caching direct de la `Collection` de modèles `Category`. Résultat en usage réel (pas détecté par les tests, qui n'utilisent pas le driver de cache `database`) : erreur 500 « Attempt to read property "name" on string ». Cause : `config('cache.serializable_classes')` vaut `false` par défaut dans ce projet — durcissement sécurité de Laravel contre l'injection d'objets PHP via un cache empoisonné (`unserialize()` appelé avec `allowed_classes: false`, qui transforme tout objet désérialisé en `__PHP_Incomplete_Class`). Corrigé en cachant un tableau simple (`['name' => ..., 'slug' => ...]`) plutôt que des modèles Eloquent — n'a pas nécessité d'affaiblir ce durcissement pour ce besoin.

### Sécurité
- Aucune image originale n'est jamais supprimée par erreur : `ProductImageObserver::deleted()` ne touche qu'aux variantes générées, pas au fichier original (dont le cycle de vie était déjà, avant cette sous-étape, géré ailleurs).
- Le cache de navigation ne contient que des données publiques déjà exposées (nom/slug de catégorie active) — aucune surface XSS ajoutée (`{{ }}` échappé comme partout ailleurs).

### Tests
- `tests/Feature/Catalogue/ImageVariantGeneratorTest.php` — 4 tests : génération des 3 tailles WebP, suppression des variantes, repli sur l'original si variante absente, utilisation de la variante une fois générée.
- `tests/Feature/Admin/ProductResourceTest.php` (déjà existant) confirme que le flux Filament réel (upload → génération de variantes) fonctionne de bout en bout.
- Suite complète : 88 tests, tous verts. Style vérifié avec `./vendor/bin/pint --test`.

### Vérifié en navigateur réel (Playwright headless, viewport mobile iPhone 13)
Upload manuel d'un produit avec 2 images réelles (via un script de test, pas via l'interface) : fiche produit affichée avec la bonne variante `large` en `src`/`srcset`, aucune erreur console, **geste de swipe tactile simulé confirmé fonctionnel** (l'image affichée change bien après un swipe gauche, testé via de vrais événements `TouchEvent`).

### Comment vérifier
```bash
php artisan test --filter=ImageVariantGeneratorTest
php artisan images:generate-variants   # backfill des images existantes
php artisan serve   # /produits/{slug} sur un produit avec plusieurs images, tester le swipe sur mobile/responsive mode
```

---

## Sous-étape 5 — Page d'accueil (fait)

Ajoutée hors périmètre initial de la Phase 2 : Ahmed a besoin d'un support visuel concret à montrer à la cliente (promotrice) pour la rassurer, avant de continuer sur la revue sécurité de fin de phase (qui n'a pas de valeur de démonstration immédiate). Décision prise avec Ahmed : hero + mise en avant produits + bandeau de réassurance (pas la version complète avis clients/storytelling/Instagram/newsletter du doc UX/UI section 6.1bis, qui reste à faire plus tard si besoin).

### Ce qui a été construit
- `App\Http\Controllers\Shop\HomeController` remplace `ProductController::index` sur la route `/` (nommée `home`) — le catalogue complet reste sur `/catalogue`, désormais bien distinct de l'accueil.
- `resources/views/shop/home.blade.php`, structuré selon le wireframe du doc UX/UI (section 6.1) :
  - **Hero pleine largeur** avec les boîtes à bordure fine posées dessus (élément signature du doc UX/UI, section 4) — fond en dégradé de la palette amiras en attendant une vraie photographie de la cliente (aucune photo produit réelle n'existe encore en base de démo). La structure (boîtes CTA superposées) reste identique le jour où une photo est fournie ; seul le fond change.
  - **Bannières de collection** pleine largeur, une par catégorie racine, fond alterné ivoire/crème.
  - **Nouveautés** : défilement horizontal des 8 derniers produits actifs (réutilise `x-shop.product-card`, donc bénéficie déjà du `srcset`/lazy loading de la sous-étape 4).
  - **Bandeau de réassurance** : paiement à la livraison, livraison organisée avec soin, contact direct — formulations volontairement génériques, aucune zone de livraison ni délai de retour n'étant encore validé avec la cliente (cahier des charges, section 7, points à clarifier).
- Seul le lien du logo (`route('home')`) pointait vers l'ancienne page — aucune autre route à migrer.

### Piège rencontré : classes Tailwind arbitraires absentes du build
Après création de la vue, le hero s'affichait sans hauteur (les classes `h-[70vh]`, dégradés `bg-gradient-to-br`, etc. n'étaient pas dans le CSS compilé) car aucun process Vite ne tournait pour re-scanner le nouveau fichier Blade. Un simple `npm run build` a suffi — pas un bug, juste un rappel qu'un nouveau fichier de vue avec des classes Tailwind inédites nécessite un rebuild avant de pouvoir le vérifier visuellement.

### Tests
- Suite complète (88 tests) toujours verte — `CatalogueTest::test_the_homepage_lists_active_products_only` continue de passer sans modification : elle vérifie qu'un produit actif apparaît et qu'un inactif n'apparaît pas sur `/`, ce qui reste vrai avec la section Nouveautés (filtrée par le scope `active()`).
- Style vérifié avec `./vendor/bin/pint --test`.

### Vérifié en navigateur réel (Playwright headless, desktop + mobile iPhone 13)
Rendu correct sur les deux formats, aucune erreur console, défilement horizontal des nouveautés fonctionnel au toucher (aperçu de la carte suivante visible en bord d'écran mobile).

### Comment vérifier
```bash
npm run build   # nécessaire après toute nouvelle classe Tailwind pas encore compilée
php artisan serve   # /
```

---

## Sous-étape 6 — Attractivité visuelle sans photos réelles (fait)

Ahmed a jugé le rendu de la page d'accueil (et du site en général) trop plat en le testant en vrai dans le navigateur. Cause principale identifiée : **toutes les données de démo utilisaient du texte Faker en latin** (noms de produits comme « Consequuntur Quo Nulla », une catégorie parasite « Amet Laborum » créée par un test antérieur) — aucun design ne peut paraître engageant avec du charabia à la place des noms de produits. Décision prise avec Ahmed : nettoyer les données de démo ET enrichir visuellement les zones sans photo, plutôt que de se limiter à l'un ou l'autre.

### Ce qui a été construit
- **`database/seeders/CatalogueSeeder.php` réécrit** : noms et descriptions de produits réalistes en français par catégorie (ex. « Hijab Jersey Ivoire », « Eau de Parfum Ambre Doré », « Sac Bandoulière Cuir Beige »), variantes adaptées au type de produit (contenance pour les parfums, couleur pour le reste) plutôt que couleur/taille générique partout. Base de dev reconstruite (`migrate:fresh --seed`) pour repartir d'un catalogue propre — supprime au passage la catégorie « Amet Laborum » qui n'avait jamais été dans le seeder officiel.
- **`x-shop.image-placeholder`** (nouveau composant) : remplace le bloc plat « Pas d'image » par un fond dégradé + une icône discrète choisie selon le nom de la catégorie (croissant pour voiles/hijabs, flacon pour parfums, sac pour maroquinerie, étincelle en repli générique) — utilisé partout où une image peut manquer (carte produit, galerie fiche produit, panier).
- **Bannière de catégorie** (`shop/category.blade.php`) : le simple `<h1>` est remplacé par un bandeau plein largeur en dégradé, même traitement que les bandes collection de l'accueil — donne du poids visuel à la page même sans photo de catégorie.

### Tests
Suite complète (88 tests) toujours verte après la reconstruction de la base de démo — les tests utilisent leurs propres factories/base de test, indépendantes des données de démo. Style vérifié avec `./vendor/bin/pint`.

### Comment vérifier
```bash
php artisan migrate:fresh --seed   # recharge le catalogue de démo réaliste
npm run build
php artisan serve   # /, /catalogue, /categories/{slug}, /produits/{slug}
```

---

## Sous-étape 7 — Site vitrine complet + contenu éditable en admin (fait, avec un point ouvert)

Demandé par Ahmed après la sous-étape 6 : un site "digne de ce nom" avec page À propos, page Contact, et surtout un moyen pour lui (ou la cliente) de modifier le contenu (hero, réassurance, à propos, contact, footer) sans toucher au code.

### Ce qui a été construit
- **Pages À propos et Contact** (`shop.about`, `shop.contact`) — nouvelles routes `/a-propos` et `/contact`. Contact inclut un vrai formulaire (nom/email/message) persistant en base via `App\Models\Contact\ContactMessage` (pas d'envoi d'email pour l'instant, juste stocké — pas d'admin dédié pour les consulter, à ajouter si besoin).
- **Contenu éditable depuis Filament** (groupe de navigation "Contenu du site") :
  - **Hero (accueil)** — `App\Models\Content\HeroSlide` + `HeroSlideResource` (Filament) : plusieurs slides, chacune avec image, titre, sous-titre, texte/lien de bouton, ordre, actif/inactif, **et un réglage de cadrage d'image** (gauche/centre/droite) pour éviter qu'une photo portrait ne soit mal recadrée dans le bandeau large. Les images du hero passent par le même pipeline de redimensionnement que les photos produit (`ImageVariantGenerator`, sous-étape 4) via `HeroSlideObserver`.
  - **Réglages du site** — `App\Models\Content\SiteSetting` (singleton) + page Filament dédiée : bandeau de réassurance (jusqu'à 3 phrases), histoire de la marque + citation + valeurs (page À propos), coordonnées de contact, accroche et réseaux sociaux du footer.
- **Page d'accueil** : le hero est devenu un vrai carrousel (Alpine, rotation auto 6s + points de navigation, pause visuelle gérée par simple affichage/masquage), qui retombe sur un contenu par défaut si aucune slide n'est configurée. Section collections passée d'un empilement de bandes à une grille de cartes visuelles. Section nouveautés : défilement continu en boucle (CSS `@keyframes`), pause au survol.
- **Footer entièrement refondu** : traitement "bandeau signature" (ébène + or, écho au hero), accroche + icônes réseaux sociaux éditables, colonnes de liens.
- **Panel admin nettoyé** : widgets par défaut Filament (message d'accueil, bloc version) retirés du tableau de bord ; page de profil admin (`->profile()`) activée pour que l'admin change nom/email/mot de passe depuis le panel.

### Bug réel trouvé et corrigé : upload d'image bloqué
Deux causes cumulées empêchaient l'upload de photos réelles (de téléphone, donc plusieurs Mo) dans les champs image (hero et produits) :
1. **`upload_max_filesize` PHP à 2 Mo** (`/etc/php/8.4/cli/php.ini`) — relevé à 20 Mo. Nécessite de redémarrer `php artisan serve` pour prendre effet (le process ne relit pas le php.ini à chaud).
2. **`->maxSize(4096)` codé en dur dans les FileUpload Filament** (`HeroSlideResource`, `ProductResource`) — validation Filament indépendante de la limite PHP, rejetait silencieusement tout fichier > 4 Mo même après le correctif PHP. Relevée à 15 Mo dans les deux resources.

Confirmé corrigé par un test de bout en bout (upload d'une photo de 10 Mo, sauvegarde réussie, vérifiée en base).

### Point ouvert : affichage figé du champ image dans le navigateur d'Ahmed
Après les deux correctifs ci-dessus, Ahmed voit toujours le champ image resté bloqué sur "Chargement / En attente de la taille" pour une image déjà enregistrée (upload déjà réussi et persisté en base — vérifié en base de données, l'image existe bien et est servie correctement, `curl` renvoie 200). Reproduit avec exactement le même enregistrement dans un navigateur Chromium piloté automatiquement : **aucun blocage, la miniature et la taille s'affichent correctement, aucune erreur console**. La seule différence visible sur les captures d'Ahmed est la bannière **"Traduit en français"** de Google Chrome — hypothèse non confirmée par Ahmed au moment de l'interruption : la traduction automatique de Chrome réécrit le DOM pendant que la page Livewire/Alpine tourne, ce qui est une cause connue de blocages d'UI sur ce type de composant JS interactif.

**À faire en priorité à la prochaine session** : demander à Ahmed de désactiver la traduction automatique sur les pages `/admin` (clic droit → "Ne jamais traduire ce site", ou dans les réglages Chrome) et retester. Si le blocage persiste même sans traduction automatique, creuser plus profondément côté FilePond/Livewire (extension navigateur bloquant une requête, cache navigateur, version de Chrome, etc.) — la piste "traduction automatique" n'a pas pu être vérifiée avant la fin de la session.

### Tests
Aucun test automatisé écrit pour cette sous-étape à la demande d'Ahmed (« arrête les tests jusqu'à qu'on termine la section ») — à couvrir avant de clore la Phase 2 : formulaire de contact (validation, persistance), CRUD des hero slides, page de réglages du site.

### Comment vérifier
```bash
php artisan serve
# /, /a-propos, /contact, /admin (Contenu du site > Hero, Réglages du site)
```

---

## Sous-étape 8 — Avis clients, images de catégories, tiroir panier (fait)

Dernier lot de la Phase 2 avant la revue de fin de phase. Objectif : finir de rendre le site « rassurant » (preuve sociale, catégories illustrées) et fluidifier l'ajout au panier.

### Ce qui a été construit
- **Avis clients** — `App\Models\Content\Review` + table `reviews` (auteur, ville, note, commentaire, `is_published`, `position`). Deux entrées :
  - **Public** : `ReviewController@store` sur `POST /avis`, validé par `StoreReviewRequest`. Les avis soumis depuis le site arrivent **non publiés** — ils n'apparaissent qu'après modération explicite en admin. C'est délibéré : un formulaire d'avis public sans modération est une porte ouverte au spam et aux contenus injurieux sur la vitrine de la cliente.
  - **Admin** : `ReviewResource` (Filament, groupe « Contenu du site ») pour publier/dépublier et ordonner.
  - Affichage via `x-shop.review-card`, alimenté par le scope `Review::published()`.
- **Images de catégories** — migration `add_image_path_to_categories_table` + `CategoryObserver` qui branche les catégories sur le même `ImageVariantGenerator` que les produits et le hero (génération des variantes à l'enregistrement, nettoyage à la suppression). Les bannières de catégorie ne dépendent plus uniquement du dégradé de repli introduit en sous-étape 6.
- **Tiroir panier (slide-over)** — `shop/partials/cart-drawer.blade.php` : panneau latéral Alpine (état `cartOpen` porté par `<body>`), rendu côté serveur à partir de `$headerCart` partagé au layout. Fermeture au clic sur le voile et à la touche Échap. Évite de quitter la page de catalogue pour vérifier son panier.
- **Pages d'authentification personnalisables** — champs `auth_title`, `auth_subtitle`, `auth_image_path` sur `SiteSetting`, éditables depuis la page Réglages du site. La migration remplit la ligne singleton existante avec des textes par défaut (`firstOrCreate` ne réapplique pas les défauts à une ligne déjà créée) pour que login/register ne soient jamais vides.
- **`config/livewire.php` publié** — `temporary_file_upload.rules` relevé à `max:16384` (16 Mo), suite du correctif d'upload de la sous-étape 7.

### Tests
Suite complète verte : **97 tests, 270 assertions**. Nouveaux tests :
- `Shop/ReviewSubmissionTest` — un avis soumis est enregistré mais **non publié**, seuls les avis publiés sortent sur l'accueil, nom et commentaire obligatoires.
- `Account/DashboardTest` — redirection des invités, tuiles d'accès rapide pour le client connecté.
- `Admin/CategoryResourceTest` et `Admin/ProductResourceTest` étendus (upload et champs image).

> La suite prend ~6 min : la génération de variantes d'images domine le temps d'exécution. À optimiser (fake du générateur dans les tests qui ne l'exercent pas) si ça devient gênant.

### Comment vérifier
```bash
php artisan migrate --seed
npm run build
php artisan serve --no-reload   # cf. note uploads, sous-étape 7
# /  (section avis)  ·  /admin (Contenu du site > Avis)  ·  clic sur l'icône panier
```

---

## Fin de la Phase 2 — changement d'identité de marque

La cliente a transmis le **rapport d'identité visuelle « Aissatou Store » V3** (`docs/Aissatou_Store_Rapport_Identite_Visuelle.pdf`, 17 pages, juillet 2026) après la sous-étape 8. Il remplace `docs/files/ux-ui-direction-amiras.md` comme référentiel visuel.

**Portée réelle du changement :** couche présentation uniquement. Aucun impact sur le domaine métier (catalogue, panier, comptes, commandes), la sécurité ou la scalabilité. Le code ne contient aucune couleur hex en dur dans les vues Blade — tout passe par les tokens Tailwind `amiras-*`, ce qui rend la substitution de palette essentiellement mécanique.

Détail du plan de reprise et des arbitrages en attente : voir `docs/progress/02b-reidentite-aissatou-store.md`.
