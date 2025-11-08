# Cycle de vie d'un échange de messages

Ce document décrit le cycle de vie d'un échange de messages entre un acheteur et un vendeur sur la plateforme.

## 1. Création de la conversation

*   **Initiateur :** Un utilisateur (acheteur potentiel) sur la page d'un article.
*   **Action :** L'acheteur clique sur le bouton "Contacter le vendeur".
*   **Pré-conditions :**
    *   L'utilisateur doit être authentifié.
    *   L'utilisateur ne peut pas être le propriétaire de l'article.
*   **Processus :**
    1.  Le système vérifie si une conversation existe déjà entre cet acheteur et le vendeur pour cet article spécifique.
    2.  **Si non :** Une nouvelle conversation est créée avec les `item_id`, `buyer_id`, et `seller_id`.
    3.  **Si oui :** Le système ne crée pas de nouvelle conversation.
*   **Post-conditions :**
    *   L'utilisateur est redirigé vers la page de la conversation.

## 2. Échange de messages

*   **Acteurs :** L'acheteur et le vendeur.
*   **Actions :**
    *   Envoyer un message.
    *   Recevoir un message.
    *   Lire un message.
*   **Processus :**
    1.  Un utilisateur (acheteur ou vendeur) saisit un message dans le champ de texte de la page de conversation et le soumet.
    2.  Le système enregistre le message dans la base de données, en l'associant à la conversation et à l'utilisateur qui l'a envoyé.
    3.  Le message a initialement un statut `read_at` nul.
    4.  Le destinataire du message reçoit une notification (à implémenter).
    5.  Lorsque le destinataire ouvre la conversation, le système met à jour le champ `read_at` des messages non lus à la date et à l'heure actuelles.
*   **Post-conditions :**
    *   Le message est visible dans la conversation pour les deux utilisateurs.
    *   Le statut de lecture du message est mis à jour.

## 3. États d'un message

*   **Non lu :** `read_at` est `null`.
*   **Lu :** `read_at` contient une date et une heure.

## 4. Notifications (Amélioration future)

*   **Notification en temps réel :** Lorsqu'un message est reçu, une notification pourrait être affichée à l'utilisateur s'il est en ligne. Une approche possible sans WebSockets serait d'utiliser le "polling", où le client interroge régulièrement le serveur pour vérifier s'il y a de nouveaux messages.
*   **Notification par e-mail/push :** Si l'utilisateur n'est pas en ligne, une notification par e-mail ou push pourrait être envoyée après un certain délai.

## 5. Fin de la conversation

Le cycle de vie d'une conversation n'a pas de "fin" explicite dans le système actuel. Les conversations restent dans l'historique des utilisateurs.

### Améliorations futures :

*   **Archivage :** Permettre aux utilisateurs d'archiver une conversation pour la masquer de leur liste principale de conversations.
*   **Suppression :** Permettre aux utilisateurs de supprimer une conversation de leur vue (suppression douce, pas de la base de données).
*   **Clôture :** Une conversation pourrait être considérée comme "clôturée" après qu'une transaction a été effectuée pour l'article concerné.
