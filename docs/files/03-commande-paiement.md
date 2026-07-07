# Phase 3 — Commande & Paiement de Base
## L'Univers des Amiras — Boutique E-commerce

**Objectif de la phase :** construire le tunnel de commande complet et rendre la boutique réellement opérationnelle avec un premier mode de paiement fonctionnel (paiement à la livraison), avant d'ajouter la complexité des paiements électroniques en Phase 5.

---

## 1. Prérequis avant de commencer

- Phase 2 terminée et validée (panier fiable et sécurisé, comptes clients opérationnels)
- Décision confirmée sur la structure des statuts de commande avec le client (nouvelle → en préparation → expédiée → livrée → annulée, ou variante adaptée à son organisation réelle)

---

## 2. Périmètre fonctionnel de la phase

### 2.1 Tunnel de commande
- Étape de récapitulatif panier avant validation
- Sélection ou saisie de l'adresse de livraison
- Sélection du mode de paiement disponible
- Étape de confirmation finale avant création de la commande
- Page de confirmation post-commande avec numéro de commande

### 2.2 Gestion de la commande
- Création de la commande en base avec figeage des prix au moment de l'achat (le prix affiché sur une commande passée ne doit jamais varier même si le prix catalogue change ensuite)
- Décrémentation du stock au moment de la validation de la commande, pas seulement au moment de l'ajout au panier
- Génération d'un numéro de commande unique et lisible pour le client

### 2.3 Paiement à la livraison
- Intégration du mode "cash on delivery" comme premier fournisseur de paiement dans l'architecture découplée définie précédemment
- Statut de paiement associé à la commande, distinct du statut de livraison (une commande peut être "en préparation" avec un paiement "en attente")

### 2.4 Notifications
- Email de confirmation de commande au client
- Notification (email ou SMS) à chaque changement de statut significatif
- Notification interne à l'admin lors d'une nouvelle commande

### 2.5 Back-office — gestion des commandes
- Liste des commandes avec filtres (statut, date, client)
- Détail d'une commande (articles, client, adresse, statut, historique des changements de statut)
- Changement manuel de statut par l'admin
- Génération de facture PDF

---

## 3. Sécurité — points critiques de cette phase

**Intégrité du calcul de prix**
- Le montant total d'une commande ne doit jamais être calculé ou modifié côté client puis simplement accepté par le serveur. Le serveur doit systématiquement recalculer le total à partir des prix réels en base au moment de la validation — sinon un attaquant pourrait intercepter et modifier le montant transmis pour payer moins cher.
- Vulnérabilité classique à éviter : faire confiance à un prix ou une quantité envoyés depuis le formulaire de commande sans revalidation serveur complète (produit toujours actif, stock réellement disponible, prix actuel).

**Contrôle d'accès sur les commandes**
- Un client ne doit pouvoir consulter que ses propres commandes, jamais celles d'un autre — même vérification systématique de propriété que pour les adresses (Phase 2). Le numéro de commande ne doit pas être devinable de manière séquentielle triviale sans vérification d'appartenance.
- Le détail complet d'une commande (adresse, téléphone, articles) est une donnée sensible : accès strictement réservé au client propriétaire et à l'admin authentifié.

**Gestion des statuts**
- Seul l'admin authentifié peut modifier le statut d'une commande. Vérifier que cette action n'est jamais accessible via une route mal protégée qui serait exploitable par un client normal.
- Traçabilité obligatoire : chaque changement de statut doit être journalisé (qui, quand, ancien statut, nouveau statut) pour pouvoir enquêter en cas de litige avec un client.

**Notifications**
- Ne jamais inclure de données sensibles complètes (numéro de téléphone entier, adresse complète) dans le contenu brut d'un email si ce n'est pas nécessaire — limiter l'exposition en cas de compromission de la boîte mail.

---

## 4. Performance — points critiques de cette phase

- La validation de commande (vérification stock, recalcul prix, création commande) doit être une opération atomique : soit tout réussit, soit rien n'est appliqué, pour éviter les incohérences (stock décrémenté sans commande créée, par exemple) — particulièrement important si deux clients tentent d'acheter le dernier exemplaire d'une variante au même moment.
- Envoi des emails/notifications en tâche asynchrone (file d'attente), pas de manière synchrone bloquante pendant que le client attend la confirmation de sa commande.
- Liste des commandes en back-office paginée et indexée correctement (recherche par statut/date/client rapide même avec un volume de commandes conséquent après plusieurs mois d'activité).

---

## 5. Fiabilité & maintenabilité long terme

- Modéliser le cycle de vie de la commande de façon explicite (statuts clairement définis, transitions autorisées documentées) plutôt que des statuts en texte libre qui deviennent incohérents avec le temps
- Historiser chaque commande de façon immuable : articles, prix, quantités au moment de l'achat conservés indépendamment de l'évolution ultérieure du catalogue
- Prévoir dès cette phase la structure d'extension pour accueillir d'autres fournisseurs de paiement (Phase 5) sans modification du cœur du tunnel de commande
- Tests automatisés sur le calcul de total, la gestion de stock concurrente, et le contrôle d'accès aux commandes

---

## 6. Livrables attendus en fin de phase

- Tunnel de commande complet et fonctionnel de bout en bout
- Paiement à la livraison opérationnel comme premier mode de paiement réel
- Back-office permettant de gérer et suivre les commandes
- Notifications automatiques au client à chaque étape clé
- Boutique commercialement utilisable en V1 minimale (une commande réelle peut être passée et honorée)

---

## 7. Checklist de validation de fin de phase

- [ ] Une commande complète peut être passée de bout en bout par un client de test
- [ ] Le total de la commande est recalculé et vérifié côté serveur, indépendamment de ce qui a pu être manipulé côté client
- [ ] Le stock est décrémenté uniquement à la validation finale, pas au simple ajout au panier
- [ ] Deux commandes simulées simultanément sur le dernier exemplaire d'une variante ne créent pas de survente (test de concurrence)
- [ ] Un client ne peut pas consulter le détail d'une commande qui ne lui appartient pas en modifiant l'identifiant dans l'URL
- [ ] Seul un compte admin authentifié peut modifier le statut d'une commande
- [ ] Chaque changement de statut est journalisé avec horodatage et auteur
- [ ] L'email de confirmation de commande est reçu et contient les bonnes informations
- [ ] La facture PDF générée correspond exactement aux données de la commande
- [ ] Les emails/notifications sont envoyés en asynchrone sans bloquer la validation de commande côté client
- [ ] La liste des commandes en back-office reste rapide avec un volume de test conséquent (plusieurs centaines de commandes simulées)

---

*Phase précédente : 02-experience-client.md — Phase suivante : 04-livraison.md*
