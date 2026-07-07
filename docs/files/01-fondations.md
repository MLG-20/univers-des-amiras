# Phase 1 — Fondations
## L'Univers des Amiras — Boutique E-commerce

**Objectif de la phase :** poser une base technique saine, sécurisée et maintenable, sur laquelle toutes les phases suivantes vont s'appuyer. Une erreur ici se répercute sur tout le reste du projet — c'est la phase la plus critique en termes de rigueur.

---

## 1. Prérequis avant de commencer

- Environnement de développement Laravel opérationnel (PHP 8.3+, Composer, MySQL/MariaDB)
- Dépôt Git initialisé dès le premier commit (jamais de code sans versionning)
- Décision prise sur l'environnement de staging (un environnement de test séparé de la prod, même minimal)
- Nom de domaine et structure de dossiers VPS anticipés (même si le déploiement réel arrive en Phase 7)

---

## 2. Périmètre fonctionnel de la phase

### 2.1 Initialisation du projet
- Structure Laravel propre, organisation modulaire par domaine métier (Catalogue, Commande, Client, Paiement, Livraison, Promotion, Admin, Notification) plutôt qu'une structure MVC générique en vrac
- Configuration des environnements (.env séparés dev/staging/prod, jamais de secrets commités)
- Mise en place des migrations et seeders dès le départ (base de données reproductible sur n'importe quelle machine)

### 2.2 Base de données
- Modélisation complète des entités du catalogue : Produit, Catégorie (hiérarchique), Variante produit, Image produit
- Contraintes d'intégrité au niveau base de données (clés étrangères, contraintes NOT NULL, contraintes d'unicité) — ne jamais reporter la validation uniquement côté applicatif
- Indexation réfléchie dès la conception (colonnes de recherche/filtre fréquents : catégorie, statut actif/inactif, prix)

### 2.3 Authentification
- Système d'inscription/connexion pour les clients
- Système d'authentification séparé pour le back-office admin (jamais le même espace que les comptes clients)
- Gestion des rôles/permissions dès la fondation, même minimaliste (client vs admin vs futur rôle employé)
- Vérification d'email à la création de compte

### 2.4 Back-office — gestion catalogue (base)
- CRUD produits avec gestion des variantes et images multiples
- CRUD catégories avec hiérarchie (catégorie/sous-catégorie)
- Activation/désactivation d'un produit sans le supprimer (soft delete)

---

## 3. Sécurité — points critiques de cette phase

Cette phase pose les fondations de sécurité pour tout le projet. Les failles introduites ici sont les plus coûteuses à corriger plus tard.

**Authentification et mots de passe**
- Les mots de passe doivent être hashés avec un algorithme adapté (jamais de hash réversible, jamais de mot de passe en clair, même temporairement dans les logs)
- Limiter le nombre de tentatives de connexion (protection contre le brute-force) — sans cette limite, un attaquant peut tester des milliers de mots de passe automatiquement contre un compte
- Ne jamais révéler si c'est l'email ou le mot de passe qui est incorrect lors d'un échec de connexion (message générique), pour éviter l'énumération de comptes existants

**Séparation des privilèges**
- L'espace admin doit être totalement cloisonné de l'espace client : un client authentifié ne doit jamais pouvoir accéder à une route admin même en devinant l'URL. Vérifier systématiquement les permissions côté serveur, jamais uniquement en cachant un bouton côté interface.
- Vulnérabilité classique à éviter : la "référence directe non sécurisée à un objet" (IDOR) — un utilisateur qui modifie l'ID d'un produit ou d'une commande dans l'URL ne doit jamais pouvoir accéder aux données d'un autre utilisateur ou à une ressource admin sans autorisation explicite vérifiée en base.

**Validation des données**
- Toute donnée entrante (formulaire produit, inscription, etc.) doit être validée côté serveur, jamais seulement côté client (JavaScript). Le frontend peut être contourné facilement par un attaquant qui envoie des requêtes directement.
- Attention à l'injection SQL : l'usage de l'ORM Eloquent protège dans la grande majorité des cas, mais toute requête SQL brute manuscrite doit systématiquement utiliser des requêtes préparées, jamais de concaténation de chaînes avec une valeur utilisateur.

**Protection des uploads**
- Les images produits uploadées doivent être validées strictement (type MIME réel du fichier, pas seulement l'extension ; taille maximale ; renommage systématique du fichier pour éviter l'exécution de scripts malveillants déguisés en image).

**Gestion des secrets**
- Aucune clé, mot de passe, ou identifiant sensible ne doit jamais apparaître dans le code versionné — uniquement dans les fichiers d'environnement exclus du dépôt Git.

---

## 4. Performance — dès la fondation

- Indexation des colonnes utilisées dans les recherches/filtres futurs (évite de devoir réindexer une base déjà volumineuse plus tard)
- Relations Eloquent bien pensées dès le départ pour éviter le problème classique des "requêtes N+1" (charger une liste de produits puis faire une requête séparée par produit pour ses images/variantes au lieu de tout charger en une seule requête optimisée)
- Mise en cache prévue dès l'architecture pour les données peu volatiles (catégories, par exemple), même si l'implémentation concrète du cache arrive plus tard

---

## 5. Fiabilité & maintenabilité long terme

- Convention de nommage cohérente sur tout le projet (tables, colonnes, classes) — décidée une fois pour toutes ici, pas au fil de l'eau
- Migrations réversibles (toujours prévoir la méthode d'annulation, pas seulement l'application)
- Documentation minimale mais réelle : chaque module a un rôle clairement défini et documenté dans son propre espace
- Tests automatisés dès cette phase sur les fonctionnalités critiques (authentification notamment) — pas besoin d'une couverture à 100%, mais les fondations doivent être testées
- Journalisation (logs) structurée dès le départ pour les actions sensibles (connexion admin, modification produit) — utile pour le diagnostic en cas de problème en production

---

## 6. Livrables attendus en fin de phase

- Projet Laravel structuré et versionné sur Git
- Base de données migrée avec le schéma catalogue complet
- Authentification client et admin fonctionnelle et cloisonnée
- Back-office permettant de créer/modifier/désactiver des produits et catégories
- Environnement de staging accessible pour validation

---

## 7. Checklist de validation de fin de phase

- [ ] Le dépôt Git contient un historique de commits clair depuis le début (pas un commit unique "initial")
- [ ] Aucun secret (clé API, mot de passe) n'est présent dans le code versionné
- [ ] Les mots de passe sont hashés en base (vérification directe en base de données)
- [ ] Une tentative de connexion admin avec un compte client échoue correctement
- [ ] Une tentative d'accès à une route admin sans authentification est bloquée et redirigée
- [ ] La modification de l'ID dans l'URL d'une ressource ne permet pas d'accéder aux données d'un autre utilisateur (test IDOR)
- [ ] Un upload de fichier autre qu'une image (ex: script déguisé) est rejeté
- [ ] La création d'un produit avec variantes et images fonctionne de bout en bout depuis le back-office
- [ ] Les catégories hiérarchiques (parent/sous-catégorie) s'affichent et se gèrent correctement
- [ ] La désactivation d'un produit (soft delete) le retire du catalogue sans le supprimer en base
- [ ] Les migrations peuvent être exécutées et annulées sans erreur sur une base vierge
- [ ] Les tests automatisés sur l'authentification passent

---

*Phase suivante : 02-experience-client.md*
