# User Stories du Projet Pifpaf

Ce document centralise toutes les User Stories (US) du projet. Elles sont regroupées par "Épopées" (Epics) pour organiser le développement par grandes fonctionnalités.

---

## Épopée 1 : Amélioration du Suivi de Livraison

Cette épopée a pour but d'implémenter le cycle de vie de livraison détaillé dans la fiche technique `delivery_lifecycle.md`.

### US1.1: Génération de l'étiquette d'expédition

*   **En tant que** vendeur,
*   **Je veux** pouvoir générer et télécharger l'étiquette d'expédition directement depuis mon tableau de bord des ventes,
*   **Afin de** préparer facilement mes colis pour l'envoi.

**Critères d'acceptation:**
1.  Sur la page de détail d'une vente dont le statut est `PAYMENT_RECEIVED`, un bouton "Créer l'envoi" est visible.
2.  Cliquer sur ce bouton appelle l'API Sendcloud pour créer un colis et récupère l'URL de l'étiquette.
3.  Après la création réussie, la page affiche une confirmation et un lien cliquable "Télécharger l'étiquette".
4.  Cliquer sur le lien ouvre l'étiquette dans un nouvel onglet pour l'impression.
5.  Le statut de la transaction est mis à jour à `SHIPPING_INITIATED`.

### US1.2: Mise à jour automatique du statut de suivi

*   **En tant que** utilisateur (vendeur ou acheteur),
*   **Je veux** voir le statut de la livraison se mettre à jour automatiquement sur la page de la transaction,
*   **Afin de** suivre l'acheminement du colis sans avoir à quitter le site.

**Critères d'acceptation:**
1.  Le webhook Sendcloud est capable de recevoir et de traiter les mises à jour de statut de colis.
2.  Une table de correspondance (mapping) est implémentée pour traduire les statuts de Sendcloud en statuts internes de notre application (`SHIPPED`, `IN_TRANSIT`, `DELIVERED`, `DELIVERY_FAILED`).
3.  Lorsqu'un webhook de changement de statut est reçu et validé, le statut de la `Transaction` correspondante est mis à jour en base de données.
4.  La page de détail de la transaction affiche le statut de livraison actuel de manière claire et compréhensible pour l'utilisateur.

### US1.3: Notification de livraison à l'acheteur

*   **En tant que** acheteur,
*   **Je veux** recevoir une notification par e-mail lorsque mon colis a été livré,
*   **Afin d'**être informé que je peux le récupérer et que je dois confirmer sa bonne réception sur le site.

**Critères d'acceptation:**
1.  Quand le statut d'une transaction passe à `DELIVERED` (via le webhook), un événement est déclenché dans le système.
2.  Cet événement envoie un e-mail à l'acheteur associé à la transaction.
3.  L'e-mail l'informe que son article a été livré et inclut un lien direct vers la page de la transaction pour qu'il puisse confirmer la réception.

### US1.4: Confirmation de réception par l'acheteur

*   **En tant que** acheteur,
*   **Je veux** disposer d'un bouton simple pour confirmer que j'ai bien reçu mon article,
*   **Afin de** finaliser la transaction, ce qui permet au vendeur d'être payé.

**Critères d'acceptation:**
1.  Sur la page de la transaction, lorsque le statut est `DELIVERED`, un bouton "Confirmer la réception" est visible et cliquable **uniquement** pour l'acheteur.
2.  Cliquer sur ce bouton met à jour le statut de la transaction à `COMPLETED`.
3.  Le paiement est transféré du séquestre vers le portefeuille du vendeur.
4.  Le bouton "Confirmer la réception" disparaît, et la transaction est affichée comme "Terminée".
