# Phase 2 — Expérience client : journal d'avancement

**Statut : en cours.** Découpée en sous-étapes validées une par une avec Ahmed (voir [feedback_workflow] côté suivi de projet) :

1. ✅ **Catalogue public** (listing, catégories, fiche produit) — fait
2. ✅ **Panier** (session invité + compte, fusion, isolation) — fait
3. ⏳ Comptes clients (adresses, historique commandes, infos perso) — à venir
4. ⏳ Mobile + performance (images optimisées, cache, lazy loading) — à venir
5. ⏳ Tests + revue sécurité de fin de phase (XSS, CSRF, IDOR adresses/historique) — à venir

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
