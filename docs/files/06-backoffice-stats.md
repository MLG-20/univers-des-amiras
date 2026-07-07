# Phase 6 — Back-office Avancé & Statistiques
## L'Univers des Amiras — Boutique E-commerce

**Objectif de la phase :** doter le client d'outils de pilotage réels de son activité — statistiques, promotions, gestion fine du stock — pour qu'il puisse gérer sa boutique de façon autonome et professionnelle au quotidien.

---

## 1. Prérequis avant de commencer

- Phase 5 terminée et validée (commandes et paiements fiables — les statistiques n'ont de sens que sur des données de base solides)
- Un minimum de données réelles ou simulées en base pour tester la pertinence des statistiques (statistiques sur une base vide n'ont pas de valeur de test)

---

## 2. Périmètre fonctionnel de la phase

### 2.1 Tableau de bord statistique
- Chiffre d'affaires par période (jour, semaine, mois), avec comparaison à la période précédente
- Produits les plus vendus (par quantité et par chiffre d'affaires)
- Taux de conversion (visites → commandes) — nécessite un minimum de tracking des visites déjà en place ou à ajouter
- Détection et affichage des paniers abandonnés (paniers non transformés en commande après un certain délai)

### 2.2 Promotions et codes promo
- Création de codes promo (réduction en pourcentage ou montant fixe)
- Conditions d'application (date de validité, montant minimum d'achat, produit/catégorie ciblé, nombre d'utilisations maximum)
- Application du code dans le tunnel de commande avec recalcul du total

### 2.3 Gestion fine du stock
- Alertes automatiques en cas de stock faible sur une variante produit (seuil configurable)
- Historique des mouvements de stock (entrée, sortie, ajustement manuel) avec traçabilité de l'auteur du mouvement
- Vue d'ensemble de l'inventaire par catégorie

### 2.4 Gestion clients avancée
- Segmentation basique des clients (clients réguliers, clients avec panier abandonné, clients inactifs)
- Historique complet des achats par client visible depuis le back-office

### 2.5 Gestion des utilisateurs admin
- Possibilité d'ajouter plusieurs comptes admin si le client souhaite déléguer la gestion à une autre personne
- Rôles différenciés si nécessaire (admin complet vs gestionnaire limité au catalogue, par exemple)

---

## 3. Sécurité — points critiques de cette phase

**Protection des données statistiques et clients**
- Les statistiques et données clients agrégées sont des informations business sensibles : accès strictement réservé aux comptes admin authentifiés, jamais exposé sur une route publique même par erreur de configuration.
- La segmentation et l'historique client exposent potentiellement des données personnelles sensibles (habitudes d'achat) — traiter ces vues avec la même rigueur de contrôle d'accès que les données personnelles en Phase 2.

**Gestion des comptes admin multiples**
- Si plusieurs comptes admin sont introduits, chaque action sensible doit être tracée avec l'identité de l'auteur précis (pas un compte admin générique partagé) — indispensable pour la responsabilisation et l'audit en cas de problème.
- Vulnérabilité à éviter : élévation de privilège — un compte "gestionnaire limité" ne doit jamais pouvoir accéder à des fonctionnalités réservées à l'admin complet en modifiant une URL ou un paramètre de requête. Chaque route et action doit revérifier le rôle réel côté serveur.

**Codes promo — abus et fraude**
- Le nombre d'utilisations d'un code promo doit être vérifié et incrémenté de façon atomique au moment de la commande, pour éviter qu'un code à usage limité soit utilisé plus de fois que prévu en cas de commandes simultanées.
- Valider strictement les conditions d'application du code côté serveur au moment de la validation finale de commande, jamais uniquement au moment de la saisie du code (un client ne doit pas pouvoir modifier son panier après validation du code pour contourner le montant minimum requis, par exemple).

**Mouvements de stock**
- Tout ajustement manuel de stock doit être journalisé avec l'auteur et la raison, pour éviter les ajustements arbitraires non traçables (protection contre l'erreur humaine autant que contre un abus).

---

## 4. Performance — points critiques de cette phase

- Les statistiques agrégées (chiffre d'affaires, produits les plus vendus) doivent être calculées de façon optimisée — pas de recalcul complet à chaque chargement de page sur l'ensemble de l'historique des commandes une fois le volume de données conséquent. Prévoir une agrégation périodique ou un système de cache adapté.
- La détection des paniers abandonnés doit tourner en tâche planifiée en arrière-plan, pas en calcul à la volée à chaque consultation du tableau de bord.
- Le tableau de bord doit rester réactif même avec plusieurs mois, voire années, d'historique de commandes.

---

## 5. Fiabilité & maintenabilité long terme

- Les statistiques doivent être basées sur des données historisées immuables (prix et quantités figés au moment de l'achat, cf Phase 3) pour rester exactes même si le catalogue évolue après coup
- Documentation claire des règles de calcul de chaque statistique (comment est défini un "panier abandonné", sur quelle période le taux de conversion est calculé) — évite les incompréhensions futures avec le client sur l'interprétation des chiffres
- Tests automatisés sur le calcul des statistiques clés et sur l'application des codes promo (y compris les cas limites : code expiré, montant minimum non atteint, usage maximum atteint)
- Prévoir l'extensibilité du système de rôles admin si le client souhaite déléguer davantage de responsabilités à l'avenir

---

## 6. Livrables attendus en fin de phase

- Tableau de bord statistique complet et fiable
- Système de codes promo opérationnel et robuste face aux abus
- Gestion fine du stock avec alertes et traçabilité
- Back-office capable d'accueillir plusieurs comptes admin avec rôles différenciés

---

## 7. Checklist de validation de fin de phase

- [ ] Le chiffre d'affaires affiché correspond exactement à la somme réelle des commandes payées sur la période sélectionnée
- [ ] Les produits les plus vendus reflètent les données réelles de commande, pas seulement le stock ou les vues
- [ ] Un panier abandonné est correctement détecté selon la règle définie et documentée
- [ ] Un code promo expiré ou ayant atteint son nombre maximum d'utilisations est refusé au moment de la validation finale, pas seulement à la saisie
- [ ] Une tentative d'utilisation simultanée d'un code promo à usage unique par deux commandes ne permet pas un double usage (test de concurrence)
- [ ] Un compte admin à rôle limité ne peut pas accéder aux fonctionnalités réservées à l'admin complet en modifiant une URL
- [ ] Chaque ajustement manuel de stock est journalisé avec l'auteur, la date et la raison
- [ ] Les alertes de stock faible se déclenchent correctement selon le seuil configuré
- [ ] Le tableau de bord reste rapide à charger avec un volume de test de plusieurs centaines/milliers de commandes
- [ ] Aucune donnée statistique ou client n'est accessible sans authentification admin valide

---

*Phase précédente : 05-paiements-electroniques.md — Phase suivante : 07-finalisation-production.md*
