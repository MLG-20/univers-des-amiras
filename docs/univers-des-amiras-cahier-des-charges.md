# Cahier des Charges & Architecture
## L'Univers des Amiras — Boutique E-commerce

**Client :** Promotionnaire (Université)
**Développeur :** Ahmed
**Stack :** Laravel (PHP)
**Date :** Juillet 2026

---

## 1. Présentation du projet

### 1.1 Concept
L'Univers des Amiras est une boutique en ligne dédiée à la mode féminine, positionnée autour de l'élégance et de la féminité (le nom "Amira" signifiant "princesse" en arabe).

### 1.2 Catégories de produits (V1)
- Voiles (hijabs, foulards)
- Parfums
- Sacs à main
- Collants
- Vêtements

> 📌 À clarifier avec le client : arborescence précise des sous-catégories (ex : Voiles > Soie / Coton / Imprimés).

### 1.3 Type de commerce
Boutique mono-vendeur.

### 1.4 Objectifs business
- Digitaliser la vente, toucher une clientèle plus large
- Professionnaliser l'image de marque autour du nom "L'Univers des Amiras"
- Faciliter la gestion des stocks et commandes
- Base scalable pour une extension future (volume, zones géographiques)

---

## 2. Public cible

| Persona | Description |
|---|---|
| Cliente principale | 18-45 ans, sensible à la mode/pudeur vestimentaire, achète via mobile |
| Cliente récurrente | Suit la marque sur réseaux sociaux, achète régulièrement |
| Acheteur cadeau | Achète pour offrir (parfum, sac) |

Trafic majoritairement mobile → conception mobile-first obligatoire.

---

## 3. Périmètre fonctionnel

### 3.1 Front boutique (client final)

**Catalogue & navigation**
- Liste produits par catégorie/sous-catégorie
- Fiche produit : photos multiples, description, prix, variantes (taille, couleur, contenance), stock disponible
- Recherche (nom, catégorie)
- Filtres (catégorie, prix, disponibilité, couleur/taille)

**Panier & commande**
- Ajout/suppression, gestion quantités
- Récapitulatif avant validation
- Choix mode de livraison
- Choix mode de paiement
- Confirmation (email/SMS)

**Compte client**
- Inscription/connexion
- Historique commandes + suivi statut
- Gestion adresses de livraison
- Liste de souhaits (optionnel V1)

**Autres**
- Page À propos / Contact
- Avis clients (optionnel V1)
- Newsletter/notifications promos (optionnel V1)

### 3.2 Back-office admin

**Gestion catalogue**
- CRUD produits (variantes, images multiples, prix, stock)
- CRUD catégories/sous-catégories
- Gestion promotions/codes promo

**Gestion des commandes**
- Liste avec filtres (statut, date, client)
- Changement de statut (nouvelle → préparation → expédiée → livrée → annulée)
- Détail commande + facture PDF

**Gestion stock**
- Suivi quantités en temps réel
- Alertes stock faible
- Historique des mouvements

**Gestion clients**
- Liste clients + historique d'achats
- Segmentation basique (réguliers, paniers abandonnés)

**Statistiques**
- Chiffre d'affaires (jour/semaine/mois)
- Produits les plus vendus
- Taux de conversion
- Paniers abandonnés

**Paramètres**
- Modes de paiement actifs
- Zones/frais de livraison
- Utilisateurs admin

---

## 4. Exigences non-fonctionnelles

| Exigence | Détail |
|---|---|
| Responsive / Mobile-first | Priorité absolue |
| Performance | Chargement catalogue < 2s, images optimisées (lazy loading, WebP) |
| Sécurité | HTTPS/SSL, protection CSRF, validation stricte des formulaires |
| SEO | URLs propres, meta-tags dynamiques, sitemap.xml |
| Scalabilité | Architecture modulaire, pas de refonte nécessaire en cas de montée en charge |
| PWA | À évaluer selon les usages mobiles observés une fois en ligne |
| Hébergement | VPS avec SSL |

---

## 5. Architecture — vue d'ensemble

L'architecture est pensée pour être **modulaire par domaine métier**, ce qui permet d'isoler chaque responsabilité (catalogue, commande, paiement, livraison) et de faire évoluer un module sans impacter les autres.

### 5.1 Domaines applicatifs

1. **Catalogue** — Produits, Catégories, Variantes, Stock
2. **Panier** — Gestion session/persistée
3. **Commande** — Cycle de vie de la commande, statuts, facturation
4. **Client** — Comptes, adresses, historique
5. **Paiement** — Abstraction multi-fournisseur (voir 5.3)
6. **Livraison** — Zones, frais, suivi (voir 5.4)
7. **Promotion** — Codes promo, réductions
8. **Administration** — Back-office, statistiques
9. **Notification** — Emails, SMS (confirmation commande, changement de statut)

### 5.2 Modèle de données — entités principales

- **Utilisateur (client)** : identité, contact, adresses liées, commandes liées
- **Produit** : informations générales, catégorie, variantes liées, images liées
- **Catégorie** : hiérarchique (catégories et sous-catégories)
- **Variante produit** : déclinaison (taille/couleur/contenance), SKU, prix ajusté, stock propre
- **Panier / Article de panier** : lié à un utilisateur ou une session invité
- **Commande** : statut, total, adresse de livraison, mode de paiement/livraison choisis
- **Article de commande** : produit/variante, quantité, prix au moment de l'achat (figé)
- **Paiement** : fournisseur utilisé, statut, référence de transaction, montant
- **Zone de livraison** : nom, frais, délai estimé
- **Code promo** : type de réduction, valeur, validité

### 5.3 Architecture du paiement

Principe : **découpler complètement la logique métier des commandes du fournisseur de paiement utilisé**, pour pouvoir ajouter, retirer ou changer un moyen de paiement sans toucher au reste de l'application.

Fournisseurs envisagés, à confirmer/prioriser avec le client :
- Paiement à la livraison (le plus simple, disponible dès le lancement)
- Wave
- Orange Money
- PayTech (en option future selon disponibilité des accès)

Chaque fournisseur respecte le même contrat fonctionnel : initier un paiement, vérifier son statut, confirmer sa disponibilité. Le reste de l'application (commande, back-office) n'a jamais besoin de connaître les détails techniques du fournisseur actif.

### 5.4 Architecture de la livraison

Même principe de découplage : la logique de commande ne dépend pas d'un transporteur précis. On prévoit dès la conception un **mode manuel** (l'admin assigne et suit la livraison lui-même en back-office), pour pouvoir lancer le projet même sans partenaire logistique intégré, puis brancher une intégration transporteur plus tard sans rien casser.

> 📌 À rechercher : transporteurs locaux au Sénégal disposant d'une API ou d'un partenariat structuré exploitable.

---

## 6. Étapes de développement

### Étape 1 — Fondations
- Initialisation du projet, base de données, authentification
- Structure du catalogue (produits, catégories, variantes)
- Back-office : gestion produits/catégories

### Étape 2 — Expérience client
- Front boutique : catalogue, fiche produit, recherche, filtres
- Panier
- Comptes clients

### Étape 3 — Commande & paiement de base
- Tunnel de commande complet
- Paiement à la livraison opérationnel
- Gestion des statuts de commande côté admin

### Étape 4 — Livraison
- Zones de livraison et frais
- Mode manuel d'assignation en back-office
- Recherche/intégration d'un transporteur local

### Étape 5 — Paiements électroniques
- Intégration Wave et/ou Orange Money
- Tests transactionnels

### Étape 6 — Back-office avancé & statistiques
- Tableau de bord (CA, produits vendus, paniers abandonnés)
- Promotions/codes promo
- Alertes stock faible

### Étape 7 — Finalisation & mise en production
- Optimisation SEO et performance
- SSL, déploiement VPS
- Formation du client à l'utilisation du back-office

---

## 7. Points à clarifier avec le client

- [ ] Identité visuelle (logo, couleurs) pour L'Univers des Amiras
- [ ] Arborescence précise des catégories/sous-catégories
- [ ] Volume de produits estimé au lancement
- [ ] Zones de livraison couvertes au démarrage
- [ ] Comptes Wave Business / Orange Money Marchand déjà existants ?
- [ ] Budget prévu pour hébergement et nom de domaine
- [ ] Présence réseaux sociaux existante à synchroniser
- [ ] Qui gérera le back-office au quotidien

---

## 8. Risques identifiés

| Risque | Impact | Mitigation |
|---|---|---|
| Accès API paiement électronique bloqué/lent | Retard fonctionnalités paiement | Architecture multi-fournisseur, lancement possible en paiement à la livraison |
| Absence d'API de livraison locale fiable | Logistique manuelle plus lourde | Mode manuel prévu dès la conception |
| Charge de travail solo | Risque de retard sur le périmètre complet | Développement par étapes, priorisation du socle avant fonctionnalités avancées |
| Absence de deadline stricte | Risque de dérive de scope | Jalons internes par étape |

---

*Document évolutif — à ajuster au fil des retours du client et de l'avancement.*
