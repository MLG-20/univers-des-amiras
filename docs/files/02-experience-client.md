# Phase 2 — Expérience Client
## L'Univers des Amiras — Boutique E-commerce

**Objectif de la phase :** construire l'interface publique de la boutique — catalogue navigable, panier fonctionnel, comptes clients — avec une expérience fluide et rapide, en particulier sur mobile.

---

## 1. Prérequis avant de commencer

- Phase 1 terminée et validée (fondations, catalogue back-office, authentification opérationnels)
- Contenu produit minimal disponible pour tester l'affichage réel (photos, descriptions)

---

## 2. Périmètre fonctionnel de la phase

### 2.1 Catalogue public
- Page listing produits par catégorie/sous-catégorie
- Fiche produit détaillée : galerie photos, description, prix, sélection de variante (taille/couleur/contenance), disponibilité en stock affichée clairement
- Recherche textuelle (nom produit, catégorie)
- Filtres combinables (catégorie, tranche de prix, disponibilité)
- Pagination ou chargement progressif des listes de produits (jamais charger tout le catalogue d'un coup)

### 2.2 Panier
- Ajout au panier avec sélection de variante obligatoire si applicable
- Modification des quantités, suppression d'article
- Persistance du panier pour un visiteur non connecté (session) ET pour un client connecté (associé au compte, retrouvable même après déconnexion/reconnexion)
- Calcul du total en temps réel, gestion des ruptures de stock au moment de l'ajout

### 2.3 Comptes clients
- Inscription, connexion, réinitialisation de mot de passe
- Gestion des adresses de livraison (ajout, modification, suppression, adresse par défaut)
- Historique des commandes (accessible dès cette phase même si le tunnel de commande complet arrive en Phase 3)
- Modification des informations personnelles

### 2.4 Expérience mobile
- Conception mobile-first réelle : la majorité du trafic attendu est mobile, donc le design doit être pensé pour petit écran en priorité, pas adapté après coup depuis une version desktop
- Navigation tactile fluide (menu, filtres, galerie photo swipeable)

---

## 3. Sécurité — points critiques de cette phase

**Gestion de session et panier**
- Le panier associé à un visiteur non connecté doit être isolé par session de manière stricte — un visiteur ne doit jamais pouvoir accéder ou modifier le panier d'un autre via manipulation d'identifiant de session.
- Lors de la fusion du panier "invité" avec le compte au moment de la connexion, valider que les données fusionnées appartiennent bien à la session en cours (éviter l'injection de contenu de panier arbitraire).

**Protection des données personnelles**
- Les adresses de livraison et informations personnelles doivent être accessibles uniquement par leur propriétaire — même logique IDOR que pour le back-office : vérifier systématiquement que l'utilisateur authentifié est bien le propriétaire de la ressource demandée (adresse, historique de commande), pas seulement filtrer côté affichage.
- Formulaire de réinitialisation de mot de passe : ne jamais révéler si un email existe ou non dans la base (message identique dans les deux cas), pour éviter l'énumération de comptes clients.

**Failles d'affichage (XSS)**
- Toute donnée saisie par l'utilisateur et réaffichée quelque part (nom, avis produit si activé plus tard, adresse) doit être échappée à l'affichage pour éviter l'injection de scripts malveillants (Cross-Site Scripting). Ne jamais faire confiance à une donnée utilisateur affichée telle quelle dans une page.

**Protection contre les attaques CSRF**
- Toute action qui modifie une donnée (ajout panier, modification profil, suppression adresse) doit être protégée par un jeton anti-CSRF, pour empêcher qu'un site malveillant ne déclenche une action à l'insu du client connecté.

**Recherche et filtres**
- Les paramètres de recherche/filtre transmis par l'utilisateur (texte de recherche, ID de catégorie) doivent être validés et nettoyés avant d'être utilisés dans une requête, pour éviter toute tentative d'injection via les paramètres d'URL.

---

## 4. Performance — points critiques de cette phase

- Optimisation des images produits : formats modernes (WebP), plusieurs tailles générées (vignette liste / taille fiche produit), chargement différé (lazy loading) des images hors écran
- Mise en cache des listes de catégories et des pages de catalogue peu volatiles, avec invalidation propre au moment d'une modification produit en back-office
- Éviter absolument les requêtes N+1 sur les listes de produits (chargement des variantes, images, catégories en une seule requête optimisée, pas une requête par produit affiché)
- Temps de chargement cible : catalogue affiché en moins de 2 secondes sur connexion mobile standard sénégalaise (pas seulement testé en fibre au bureau)
- Recherche et filtres réactifs sans rechargement complet de page (interaction fluide type "live search")

---

## 5. Fiabilité & maintenabilité long terme

- Composants d'interface réutilisables (carte produit, sélecteur de variante, bouton ajout panier) plutôt que du code dupliqué à chaque page — facilite les évolutions futures
- Gestion propre des cas limites : produit en rupture de stock au moment de l'ajout au panier, variante supprimée entre-temps, panier vide
- Tests automatisés sur les parcours critiques : ajout au panier, calcul du total, isolation du panier entre deux sessions différentes
- Prévoir dès cette phase la structure qui accueillera les avis clients et la liste de souhaits (fonctionnalités optionnelles V1) pour ne pas devoir restructurer le modèle produit plus tard

---

## 6. Livrables attendus en fin de phase

- Catalogue public navigable avec recherche et filtres fonctionnels
- Panier complet (ajout, modification, suppression, persistance) pour visiteur et client connecté
- Espace client fonctionnel (inscription, connexion, gestion adresses, historique)
- Expérience mobile validée sur au moins deux tailles d'écran réelles

---

## 7. Checklist de validation de fin de phase

- [ ] Le catalogue affiche correctement les produits avec pagination/chargement progressif
- [ ] La recherche et les filtres retournent des résultats cohérents et se combinent correctement
- [ ] L'ajout au panier fonctionne pour un produit avec variantes obligatoires (impossible d'ajouter sans sélection)
- [ ] Le panier d'un visiteur non connecté persiste après rafraîchissement de page
- [ ] Le panier invité fusionne correctement avec le compte au moment de la connexion
- [ ] Deux sessions de navigation différentes (ex: navigation privée) ont des paniers totalement isolés l'un de l'autre
- [ ] Un client connecté ne peut pas accéder à l'adresse ou l'historique d'un autre client en modifiant un identifiant dans l'URL
- [ ] La réinitialisation de mot de passe ne révèle pas si l'email existe en base
- [ ] Un nom de compte ou une donnée saisie contenant du code HTML/script ne s'exécute pas à l'affichage (test XSS basique)
- [ ] Les actions de modification (profil, adresse, panier) échouent si le jeton CSRF est absent ou invalide
- [ ] Les images du catalogue sont servies en formats optimisés avec chargement différé
- [ ] Le temps de chargement de la page catalogue est mesuré et conforme à l'objectif fixé
- [ ] L'affichage est testé et validé sur mobile réel, pas uniquement en simulateur desktop

---

*Phase précédente : 01-fondations.md — Phase suivante : 03-commande-paiement.md*
