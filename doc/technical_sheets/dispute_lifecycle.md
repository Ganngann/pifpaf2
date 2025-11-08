# Cycle de vie d'un litige

Ce document décrit le fonctionnement actuel du système de litiges et définit une feuille de route pour son amélioration.

## 1. État des lieux du processus actuel (As-Is)

Le système actuel est fonctionnel mais minimaliste. Il repose sur l'intervention manuelle d'un administrateur pour chaque cas.

### Workflow

1.  **Initiation :**
    *   Un utilisateur (généralement l'acheteur) peut ouvrir un litige à partir de la page d'une transaction via un formulaire simple.
    *   La seule information requise est un champ "Raison" (texte libre, min: 20 caractères).
    *   Le litige est lié à une `Transaction`.

2.  **Conséquences automatiques :**
    *   Un enregistrement est créé dans la table `disputes` avec un statut par défaut (`open`).
    *   Le statut de la `Transaction` associée passe à `DISPUTED`.
    *   Le processus s'arrête ici. Aucune notification n'est envoyée au vendeur ou à l'administration.

3.  **Intervention de l'administrateur :**
    *   L'administrateur consulte la liste des litiges ouverts depuis son tableau de bord.
    *   La vue détaillée d'un litige lui permet de consulter les informations de la transaction, l'acheteur, le vendeur, et l'historique de la conversation associée à l'objet. C'est le seul outil d'aide à la décision.

4.  **Résolution (manuelle par l'administrateur) :**
    *   **En faveur de l'acheteur :** L'administrateur clique sur "Résoudre pour l'acheteur".
        *   Le litige est marqué comme `closed`.
        *   La transaction est marquée comme `refunded`.
        *   **Action critique :** Le portefeuille (`wallet`) de l'acheteur est crédité directement (`$user->wallet += $amount`).
    *   **En faveur du vendeur :** L'administrateur clique sur "Résoudre pour le vendeur".
        *   Le litige est marqué comme `closed`.
        *   La transaction est marquée comme `completed`.
        *   **Action critique :** Le portefeuille (`wallet`) du vendeur est crédité directement.
    *   **Clôture neutre :** L'administrateur peut clore le litige sans action financière.
        *   Le litige est marqué comme `closed`.
        *   Le statut de la transaction parente n'est pas modifié.

### Points faibles identifiés

*   **Incohérence comptable :** La manipulation directe des soldes de portefeuille (`$user->wallet`) contourne le grand livre `WalletHistory`. Cela rend la balance de l'utilisateur intraçable et peut conduire à des erreurs financières. C'est le problème le plus grave.
*   **Workflow trop simple :** Le cycle de vie est binaire (`open` -> `closed`). Il manque des états intermédiaires pour gérer la médiation (ex: "en attente de réponse", "en attente de preuves").
*   **Manque de communication dédiée :** Il n'existe pas de canal de communication spécifique au litige. L'administrateur doit se baser sur la conversation générale entre vendeur et acheteur.
*   **Conditions d'ouverture imprécises :** Un litige peut être ouvert à n'importe quel moment sur une transaction, sans contraintes de temps ou de statut.
*   **Absence de notifications :** Aucune partie n'est notifiée des événements clés (ouverture, résolution), ce qui nuit à la transparence.
*   **Gestion des preuves :** Le formulaire initial ne permet pas de joindre des pièces justificatives (photos, documents).

---

## 2. Définition du processus cible (To-Be)

Le cycle de vie cible vise à structurer, automatiser et fiabiliser la gestion des litiges.

### Nouveaux concepts

*   **Statuts de litige :** `OPEN`, `WAITING_SELLER_RESPONSE`, `WAITING_BUYER_RESPONSE`, `UNDER_ADMIN_REVIEW`, `CLOSED`.
*   **Messagerie de litige :** Un fil de discussion dédié à un litige, où acheteur, vendeur et administrateur peuvent échanger des messages et des pièces jointes.
*   **Fenêtre d'éligibilité :** Des règles métiers claires définissant quand un litige peut être ouvert.

### Workflow Cible

1.  **Éligibilité :**
    *   Un bouton "Ouvrir un litige" n'apparaît sur une transaction que si les conditions sont remplies.
    *   **Pour l'acheteur :** La transaction doit avoir le statut `shipped` ou `pickup_completed`. Le litige peut être ouvert jusqu'à 14 jours après la confirmation de réception.
    *   **Pour le vendeur :** Un litige peut être ouvert si l'acheteur n'a pas confirmé la réception 7 jours après la livraison confirmée par le transporteur.

2.  **Ouverture du litige :**
    *   Le formulaire d'ouverture permet de sélectionner une raison prédéfinie (ex: "Objet non conforme", "Jamais reçu"), de fournir une description détaillée et de joindre plusieurs pièces justificatives.
    *   Le litige est créé avec le statut `OPEN`.
    *   La transaction passe au statut `DISPUTED`.

3.  **Médiation et Communication :**
    *   Une notification est envoyée à l'autre partie, l'invitant à répondre sous 7 jours.
    *   Le litige passe au statut `WAITING_SELLER_RESPONSE` (ou `_BUYER_RESPONSE`).
    *   Les deux parties peuvent échanger via une messagerie dédiée au litige.
    *   Si aucune réponse n'est apportée dans le délai, le litige est automatiquement escaladé à un administrateur.

4.  **Escalade et Révision par l'administrateur :**
    *   Un litige est escaladé à un administrateur si :
        *   Le délai de réponse est expiré.
        *   L'une des parties demande explicitement l'intervention d'un administrateur.
    *   Le litige passe au statut `UNDER_ADMIN_REVIEW`.
    *   L'administrateur peut consulter l'historique complet de la médiation (messages, pièces jointes) pour prendre une décision éclairée.

5.  **Résolution :**
    *   L'administrateur rend sa décision finale et rédige une note de clôture expliquant la raison.
    *   **Si en faveur de l'acheteur :**
        *   Une entrée `WalletHistory` de type `REFUND_DISPUTE` (montant positif) est créée pour l'acheteur.
        *   La transaction passe au statut `refunded`.
    *   **Si en faveur du vendeur :**
        *   Une entrée `WalletHistory` de type `PAYOUT_DISPUTE` (montant positif) est créée pour le vendeur.
        *   La transaction passe au statut `completed`.

6.  **Clôture :**
    *   Le litige passe au statut `CLOSED`.
    *   Toutes les parties sont notifiées de la décision finale et de sa justification.
