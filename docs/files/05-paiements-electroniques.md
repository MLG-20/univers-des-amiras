# Phase 5 — Paiements Électroniques
## L'Univers des Amiras — Boutique E-commerce

**Objectif de la phase :** brancher des moyens de paiement électronique (Wave, Orange Money, éventuellement PayTech) sur l'architecture multi-fournisseur définie dès la conception, sans jamais compromettre la sécurité des transactions financières.

---

## 1. Prérequis avant de commencer

- Phase 3 terminée et validée (paiement à la livraison stable, architecture de paiement découplée en place)
- Comptes marchands ouverts et vérifiés côté client (Wave Business, Orange Money Marchand) — cette démarche administrative peut prendre du temps, à anticiper largement en amont de la phase technique
- Accès de test/sandbox obtenus auprès des fournisseurs de paiement choisis avant tout développement en conditions réelles

---

## 2. Périmètre fonctionnel de la phase

### 2.1 Intégration des fournisseurs
- Implémentation de chaque fournisseur (Wave, Orange Money) en respectant l'interface commune définie en Phase 1/3 : initiation du paiement, vérification du statut, confirmation de disponibilité
- Sélection dynamique du mode de paiement disponible dans le tunnel de commande selon les fournisseurs réellement actifs et configurés

### 2.2 Cycle de vie du paiement
- Statut de paiement distinct et précis : en attente, en cours, confirmé, échoué, remboursé
- Réconciliation entre le statut de paiement du fournisseur externe et le statut enregistré côté boutique (via webhook ou vérification active)
- Gestion des paiements partiels ou des échecs de transaction avec message clair au client et possibilité de réessayer

### 2.3 Back-office
- Visualisation du statut de paiement réel de chaque commande, avec référence de transaction externe
- Possibilité pour l'admin de vérifier manuellement un paiement en cas de doute (litige, paiement non réconcilié automatiquement)

---

## 3. Sécurité — points critiques de cette phase

C'est la phase la plus sensible du projet : toute faille ici a un impact financier direct.

**Vérification côté serveur, jamais confiance au client**
- Ne jamais valider une commande comme "payée" uniquement parce que le navigateur du client redirige vers une page de succès. Cette redirection peut être falsifiée ou interceptée par un attaquant. Le statut de paiement doit toujours être confirmé via une vérification côté serveur directement auprès du fournisseur (webhook signé ou appel API de vérification), jamais via un simple paramètre d'URL de retour.
- Vulnérabilité classique à éviter : un attaquant qui modifie manuellement l'URL de retour de paiement (ex: `?status=success`) pour tenter de faire passer une commande comme payée sans transaction réelle.

**Validation des webhooks**
- Tout webhook entrant provenant d'un fournisseur de paiement (Wave, Orange Money) doit être vérifié via sa signature cryptographique ou une clé secrète partagée avant d'être traité. Un webhook non vérifié est une porte ouverte pour simuler de fausses confirmations de paiement.
- Le traitement d'un webhook doit être idempotent : si le même événement est reçu plusieurs fois (ce qui arrive régulièrement avec les fournisseurs de paiement), il ne doit jamais déclencher deux fois l'expédition ou la double comptabilisation d'une commande.

**Protection contre la manipulation de montant**
- Le montant envoyé au fournisseur de paiement doit toujours provenir du calcul serveur (Phase 3), jamais d'une valeur transmise par le client au moment du choix du mode de paiement.

**Confidentialité des données de transaction**
- Aucune donnée bancaire ou identifiant de paiement complet ne doit jamais transiter ou être stocké côté boutique — les fournisseurs (Wave, Orange Money) gèrent eux-mêmes ces données sensibles ; la boutique ne conserve que la référence de transaction et le statut.
- Toutes les clés API des fournisseurs de paiement sont des secrets critiques : stockage exclusif en variables d'environnement, jamais dans le code, rotation possible en cas de doute de compromission.

**Traçabilité financière**
- Chaque transaction (tentative, succès, échec) doit être journalisée de façon détaillée et immuable pour permettre un audit financier ou traiter un litige client sans ambiguïté.

---

## 4. Performance — points critiques de cette phase

- Les appels aux API de paiement externes doivent avoir un délai d'attente strict et une gestion d'erreur claire, pour ne jamais laisser le client bloqué indéfiniment sur une page de paiement en cas de lenteur du fournisseur
- Traitement asynchrone de la réconciliation des paiements via file d'attente lorsque c'est possible, pour ne pas bloquer l'expérience utilisateur pendant la vérification

---

## 5. Fiabilité & maintenabilité long terme

- Chaque fournisseur de paiement reste isolé dans sa propre implémentation, strictement conforme à l'interface commune — l'ajout ou le retrait d'un fournisseur (ex: intégration future de PayTech) ne doit jamais nécessiter de modifier le tunnel de commande ou les autres fournisseurs déjà en place
- Prévoir un mécanisme de nouvelle tentative automatique en cas d'échec technique temporaire de communication avec le fournisseur (pas en cas d'échec de paiement réel, mais en cas de problème réseau par exemple)
- Documentation claire de la procédure de réconciliation manuelle pour l'admin en cas de paiement resté "en attente" anormalement longtemps
- Tests automatisés simulant des webhooks valides, invalides, et dupliqués pour garantir la robustesse du traitement

---

## 6. Livrables attendus en fin de phase

- Au moins un fournisseur de paiement électronique pleinement intégré et testé (idéalement Wave et Orange Money)
- Statuts de paiement fiables et réconciliés automatiquement
- Back-office permettant à l'admin de vérifier et gérer les paiements en cas de doute
- Architecture prête à accueillir un fournisseur supplémentaire (PayTech ou autre) sans refonte

---

## 7. Checklist de validation de fin de phase

- [ ] Un paiement réussi est confirmé uniquement via vérification serveur, jamais via le simple retour navigateur du client
- [ ] Une tentative de falsification de l'URL de retour (statut succès simulé manuellement) n'affecte pas le statut réel de la commande
- [ ] Un webhook sans signature valide est rejeté et journalisé comme suspect
- [ ] Un même webhook reçu plusieurs fois ne déclenche la confirmation de commande qu'une seule fois (test d'idempotence)
- [ ] Le montant envoyé au fournisseur de paiement correspond exactement au total recalculé côté serveur
- [ ] Aucune clé API de paiement n'apparaît dans le code versionné
- [ ] Un paiement échoué affiche un message clair au client avec possibilité de réessayer
- [ ] L'admin peut consulter la référence de transaction et le statut réel de chaque paiement en back-office
- [ ] Toutes les tentatives de paiement (succès, échec) sont journalisées de manière consultable
- [ ] Le tunnel de paiement ne reste jamais bloqué indéfiniment en cas de lenteur simulée du fournisseur externe

---

*Phase précédente : 04-livraison.md — Phase suivante : 06-backoffice-stats.md*
