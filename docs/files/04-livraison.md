# Phase 4 — Livraison & Logistique
## L'Univers des Amiras — Boutique E-commerce

**Objectif de la phase :** structurer la gestion de la livraison de manière flexible — mode manuel opérationnel immédiatement, prêt à accueillir une intégration transporteur dès qu'un partenaire fiable est identifié.

---

## 1. Prérequis avant de commencer

- Phase 3 terminée et validée (tunnel de commande fiable, statuts de commande opérationnels)
- Zones de livraison prioritaires définies avec le client (a minima Dakar et grandes villes, à étendre progressivement)
- Recherche préliminaire des transporteurs locaux disponibles (API exploitable ou partenariat manuel)

---

## 2. Périmètre fonctionnel de la phase

### 2.1 Zones et frais de livraison
- Définition de zones géographiques avec frais et délai estimé associés
- Calcul automatique du frais de livraison au moment du tunnel de commande selon l'adresse sélectionnée
- Gestion back-office des zones (ajout, modification, désactivation d'une zone)

### 2.2 Mode manuel (disponible dès cette phase)
- L'admin peut assigner une commande à un livreur ou un mode de livraison manuel
- Suivi de statut de livraison manuel (en préparation, en cours de livraison, livrée) mis à jour par l'admin
- Historique des changements de statut de livraison, visible par le client dans son espace commande

### 2.3 Intégration transporteur (si un partenaire exploitable est identifié)
- Récupération automatique des frais/délais selon l'adresse via l'API du transporteur
- Création automatique de l'expédition auprès du transporteur au moment de la validation
- Synchronisation du statut de suivi entre le transporteur et la commande côté boutique

### 2.4 Communication client
- Le client voit à tout moment le statut de sa livraison dans son espace commande
- Notification au client lors des changements de statut de livraison significatifs (expédiée, livrée)

---

## 3. Sécurité — points critiques de cette phase

**Protection des données de livraison**
- Les adresses de livraison transmises à un transporteur externe (si intégration API) doivent transiter uniquement via une connexion chiffrée, jamais en clair.
- Si des identifiants d'API transporteur sont utilisés, ils doivent être stockés exclusivement dans les variables d'environnement sécurisées, jamais en dur dans le code.

**Contrôle d'accès au suivi**
- Le lien ou la page de suivi de livraison d'une commande ne doit être accessible qu'au client propriétaire de la commande et à l'admin — même logique de contrôle d'accès strict qu'en Phase 3, à appliquer systématiquement à toute nouvelle ressource créée (ici, le suivi de livraison).
- Si un système de suivi par lien public est envisagé (lien envoyé par SMS sans authentification), le token utilisé dans ce lien doit être suffisamment long et aléatoire pour ne pas être devinable, et à usage limité dans le temps.

**Validation des changements de statut de livraison**
- Seul l'admin (ou le webhook authentifié du transporteur, si intégration API) peut modifier le statut de livraison d'une commande. Un webhook entrant depuis un transporteur externe doit être vérifié (signature ou clé secrète) pour s'assurer qu'il provient bien du transporteur et non d'une source falsifiée.

---

## 4. Performance — points critiques de cette phase

- Le calcul du frais de livraison dans le tunnel de commande doit être quasi instantané (zones en cache, pas de recalcul lourd à chaque changement d'adresse)
- Si intégration API transporteur : prévoir un mécanisme de repli (fallback) en cas d'indisponibilité temporaire de l'API du transporteur, pour ne jamais bloquer un client en plein tunnel de commande à cause d'un service tiers en panne
- Les appels vers une API transporteur externe doivent avoir un délai d'attente (timeout) strict pour ne jamais faire attendre indéfiniment le client

---

## 5. Fiabilité & maintenabilité long terme

- L'architecture découplée définie dans le cahier des charges (interface commune pour tout transporteur) doit être respectée strictement ici : le mode manuel et une future intégration API doivent pouvoir cohabiter et être interchangeables sans modification du tunnel de commande
- Journalisation de tous les échanges avec une API transporteur externe (requêtes envoyées, réponses reçues) pour pouvoir diagnostiquer un litige de livraison
- Prévoir la possibilité d'avoir plusieurs transporteurs actifs simultanément selon la zone géographique, même si un seul est réellement branché au départ
- Tests automatisés sur le calcul des frais selon différentes zones, et sur le contrôle d'accès au suivi de livraison

---

## 6. Livrables attendus en fin de phase

- Zones de livraison configurées et frais calculés automatiquement dans le tunnel de commande
- Mode manuel de gestion de livraison pleinement opérationnel côté back-office
- Suivi de livraison visible et sécurisé côté client
- Architecture prête à accueillir une intégration transporteur sans refonte

---

## 7. Checklist de validation de fin de phase

- [ ] Le frais de livraison s'affiche correctement selon l'adresse sélectionnée dans le tunnel de commande
- [ ] L'admin peut assigner et faire évoluer le statut de livraison d'une commande manuellement
- [ ] Le client voit le statut de livraison mis à jour dans son espace commande
- [ ] Un client ne peut pas consulter le suivi de livraison d'une commande qui ne lui appartient pas
- [ ] Si un lien de suivi public est utilisé, le token associé est suffisamment complexe pour ne pas être devinable
- [ ] Les zones de livraison peuvent être ajoutées/modifiées/désactivées depuis le back-office sans erreur
- [ ] L'historique des statuts de livraison est conservé et consultable
- [ ] (Si intégration transporteur) Un webhook falsifié sans signature valide est rejeté
- [ ] (Si intégration transporteur) Une indisponibilité simulée de l'API transporteur ne bloque pas le tunnel de commande côté client

---

*Phase précédente : 03-commande-paiement.md — Phase suivante : 05-paiements-electroniques.md*
