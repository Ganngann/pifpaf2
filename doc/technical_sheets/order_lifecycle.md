# Cycle de vie d'une Commande

Ce document décrit le cycle de vie d'une transaction depuis la validation du paiement par l'acheteur jusqu'à sa finalisation.

## Processus Actuel ("As-Is")

Le processus actuel est initié juste après la validation du paiement via le `PaymentController`.

### 1. Initialisation de la Transaction

-   **Déclencheur :** Un paiement réussi dans `PaymentController@store`.
-   **Actions :**
    -   Une nouvelle `Transaction` est créée avec le statut `payment_received`.
    -   Les fonds de l'acheteur sont mis sous séquestre (débités de son portefeuille).
    -   Le statut de l'article (`Item`) est mis à `sold`.
    -   Le statut de l'offre (`Offer`) est mis à `paid`.
    -   Si la remise en main propre est activée pour l'article, un `pickup_code` est généré.

### 2. Déroulement de la Transaction

À partir du statut `payment_received`, le flux dépend principalement des actions de l'acheteur et du vendeur. Il n'y a pas de distinction forte et automatisée entre une remise en main propre et une livraison.

#### Cas A : Actions du Vendeur

1.  **Confirmation de retrait (Remise en main propre) :**
    -   **Action :** Le vendeur peut appeler `TransactionController@confirmPickup`.
    -   **Résultat :** Le statut de la transaction passe à `pickup_completed`.
    -   **NOTE IMPORTANTE :** À ce stade, les fonds **ne sont pas** transférés au vendeur. Le processus est bloqué dans cet état en attendant une action de l'acheteur.

2.  **Création de l'envoi (Livraison) :**
    -   **Action :** Le vendeur peut appeler `TransactionController@ship`.
    -   **Résultat :** Le statut de la transaction passe à `shipping_initiated`. Les informations de suivi Sendcloud sont enregistrées.

3.  **Ajout d'un suivi manuel (Livraison) :**
    -   **Action :** Le vendeur peut appeler `TransactionController@addTracking`.
    -   **Résultat :** Le statut de la transaction passe à `in_transit`.

#### Cas B : Action de l'Acheteur

1.  **Confirmation de Réception :**
    -   **Action :** L'acheteur appelle `TransactionController@confirmReception`.
    -   **Condition :** Cette action n'est possible que si le statut est `payment_received`.
    -   **Résultat :**
        -   Le statut de la transaction passe à `completed`.
        -   **Les fonds sont libérés et transférés sur le portefeuille du vendeur.**
        -   Le cycle de vie de la transaction est considéré comme terminé.

### 3. Synthèse et Problématiques du flux "As-Is"

-   **Point de blocage unique :** Le paiement du vendeur est **exclusivement** dépendant de l'action de l'acheteur (`confirmReception`).
-   **Confusion des flux :** Les actions de remise en main propre (`confirmPickup`) et de livraison (`ship`, `addTracking`) ne sont pas mutuellement exclusives et ne conduisent pas à une finalisation automatique.
-   **Risque pour le vendeur :** Dans le cas d'une remise en main propre, si l'acheteur oublie ou néglige de confirmer la réception, le vendeur n'est jamais payé, même s'il a de son côté confirmé le retrait de l'objet.
-   **Statut `REFUNDED` :** Le statut `refunded` existe mais n'est utilisé dans aucune logique du code actuel.

## Processus Cible ("To-Be")

Le processus cible vise à corriger les problématiques actuelles en clarifiant les flux et en sécurisant le vendeur. Il se base sur le mode de livraison choisi pour la transaction.

### Flux 1 : Remise en Main Propre (`pickup`)

Ce flux utilise le `pickup_code` (généré à la création de la transaction) comme preuve de remise.

1.  **Statut initial :** `payment_received`.
2.  **Rencontre et échange :** L'acheteur et le vendeur se rencontrent. L'acheteur présente son `pickup_code` au vendeur.
3.  **Confirmation par le vendeur :**
    -   **Action :** Le vendeur accède à la transaction sur son dashboard et utilise une nouvelle fonctionnalité "Confirmer la remise" où il saisit le `pickup_code` fourni par l'acheteur.
    -   **Logique :** Le backend vérifie que le code saisi correspond à celui de la transaction.
    -   **Résultat :**
        -   Si le code est correct, le statut de la transaction passe **directement** à `completed`.
        -   **Les fonds sont immédiatement libérés et transférés sur le portefeuille du vendeur.**
4.  **Avantages :**
    -   Le vendeur est payé dès la remise de l'article, sans dépendre d'une action ultérieure de l'acheteur.
    -   Le `pickup_code` sert de preuve irréfutable de la transaction.

### Flux 2 : Livraison (`delivery`)

Ce flux s'appuie sur le suivi du colis et un délai de confirmation pour l'acheteur.

1.  **Statut initial :** `payment_received`.
2.  **Action du vendeur : Envoi du colis**
    -   **Action :** Le vendeur initie l'envoi via `ship()` (Sendcloud) ou `addTracking()`.
    -   **Résultat :** Le statut de la transaction passe à `in_transit`.
3.  **Action (automatisée ou manuelle) : Confirmation de livraison**
    -   **Déclencheur A (Automatique) :** Un webhook du transporteur (ex: Sendcloud) notifie l'application que le colis est livré.
    -   **Déclencheur B (Manuel/Temporisé) :** Si pas de webhook, après un certain délai estimé de livraison, le statut pourrait être mis à jour.
    -   **Résultat :** Le statut passe à `delivered`. **Un délai de confirmation pour l'acheteur est initié (ex: 72 heures).**
4.  **Fenêtre de confirmation de l'acheteur (ex: 72h)**
    -   **Cas 1 : L'acheteur confirme la réception**
        -   **Action :** L'acheteur clique sur "J'ai bien reçu mon colis" (`confirmReception`).
        -   **Résultat :** Le statut passe à `completed`. Les fonds sont libérés pour le vendeur.
    -   **Cas 2 : L'acheteur ouvre un litige**
        -   **Action :** L'acheteur signale un problème.
        -   **Résultat :** Le statut passe à `disputed`. Les fonds restent séquestrés.
    -   **Cas 3 : L'acheteur ne fait rien**
        -   **Action :** À la fin du délai de 72h, une tâche planifiée (cron job) vérifie les transactions en statut `delivered`.
        -   **Résultat :** Le statut est automatiquement mis à jour à `completed`. **Les fonds sont libérés pour le vendeur.**
5.  **Avantages :**
    -   Le vendeur est protégé contre l'oubli de l'acheteur.
    -   L'acheteur dispose d'une fenêtre de temps définie pour signaler un problème.
    -   Le processus est clair et largement automatisé.

### Nouveaux statuts de transaction nécessaires

Pour implémenter ce flux, le statut suivant devrait être ajouté :
-   `DELIVERED` (`delivered`): Indique que le colis a été livré et que la période de confirmation de l'acheteur a commencé.
