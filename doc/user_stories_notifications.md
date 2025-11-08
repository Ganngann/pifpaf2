# Epic: Gestion des Notifications

Cet epic a pour objectif de mettre en place un système de notifications afin d'améliorer la communication avec les utilisateurs et de les tenir informés des événements importants concernant leurs transactions et interactions sur la plateforme.

L'implémentation suivra l'ordre logique ci-dessous pour assurer une construction progressive et cohérente du système.

---

## Partie 1 : Fondations du Système de Notifications

*Ces user stories constituent le socle technique et l'interface de base pour toutes les notifications futures.*

**US-NOTIF-10 : Centre de Notifications**
*   **En tant qu'** utilisateur,
*   **Je veux** avoir accès à un centre de notifications centralisé sur le site (accessible via une icône dans le header),
*   **Afin de** pouvoir consulter l'historique de toutes mes notifications passées et présentes.

**US-NOTIF-11 : Marquer les notifications comme lues**
*   **En tant qu'** utilisateur,
*   **Je veux** pouvoir marquer mes notifications comme "lues", individuellement ou en masse,
*   **Afin de** pouvoir gérer facilement les nouvelles informations et garder mon centre de notifications organisé.

**US-NOTIF-12 : Paramètres de Notifications**
*   **En tant qu'** utilisateur,
*   **Je veux** pouvoir configurer mes préférences de notification dans les paramètres de mon compte,
*   **Afin de** choisir quels types d'alertes je souhaite recevoir et par quel canal (in-app, email).

---

## Partie 2 : Notifications du Flux Transactionnel

*Ces notifications sont essentielles pour le bon déroulement d'une vente, de l'offre initiale à la finalisation.*

**US-NOTIF-01 : Notification de Nouvelle Offre (Vendeur)**
*   **En tant que** vendeur,
*   **Je veux** recevoir une notification instantanée lorsqu'un acheteur fait une offre sur l'un de mes articles,
*   **Afin de** pouvoir y répondre rapidement et ne pas manquer une vente potentielle.

**US-NOTIF-05 : Notification d'Offre Acceptée (Acheteur)**
*   **En tant qu'** acheteur,
*   **Je veux** recevoir une notification lorsque le vendeur accepte mon offre,
*   **Afin de** pouvoir procéder au paiement de l'article.

**US-NOTIF-06 : Notification d'Offre Refusée (Acheteur)**
*   **En tant qu'** acheteur,
*   **Je veux** recevoir une notification lorsque le vendeur refuse mon offre,
*   **Afin de** pouvoir faire une nouvelle offre ou chercher un autre article.

**US-NOTIF-02 : Notification de Paiement Reçu (Vendeur)**
*   **En tant que** vendeur,
*   **Je veux** recevoir une notification de confirmation dès que le paiement d'un acheteur a été validé,
*   **Afin de** savoir que je peux procéder à la préparation de la commande (remise en main propre ou envoi).

**US-NOTIF-07 : Notification de Confirmation d'Envoi (Acheteur)**
*   **En tant qu'** acheteur,
*   **Je veux** recevoir une notification lorsque le vendeur a envoyé mon colis, incluant si possible les informations de suivi,
*   **Afin de** pouvoir suivre l'acheminement de ma commande.

**US-NOTIF-03 : Notification de Confirmation de Réception (Vendeur)**
*   **En tant que** vendeur,
*   **Je veux** recevoir une notification lorsque l'acheteur confirme avoir bien reçu l'article,
*   **Afin de** savoir que la transaction est terminée et que les fonds ont été transférés sur mon portefeuille.

**US-NOTIF-08 : Notification de Rappel de Confirmation (Acheteur)**
*   **En tant qu'** acheteur,
*   **Je veux** recevoir une notification de rappel si je n'ai pas confirmé la réception de mon colis après sa livraison,
*   **Afin de** ne pas oublier de finaliser la transaction, ce qui permet au vendeur d'être payé.

---

## Partie 3 : Notifications de Messagerie

*Ces notifications facilitent la communication directe entre les utilisateurs.*

**US-NOTIF-04 : Notification de Nouveau Message (Vendeur)**
*   **En tant que** vendeur,
*   **Je veux** recevoir une notification lorsqu'un acheteur m'envoie un nouveau message privé,
*   **Afin de** pouvoir maintenir une bonne communication et répondre à ses questions.

**US-NOTIF-09 : Notification de Nouveau Message (Acheteur)**
*   **En tant qu'** acheteur,
*   **Je veux** recevoir une notification lorsqu'un vendeur m'envoie un nouveau message privé,
*   **Afin de** ne pas manquer une réponse importante à mes questions.
