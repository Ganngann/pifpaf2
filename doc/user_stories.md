# User Stories for Pifpaf

## Introduction
Ce document détaille les fonctionnalités du projet Pifpaf sous forme de User Stories (US), organisées par thèmes fonctionnels. Chaque story inclut des Critères d'Acceptation (CA) pour guider le développement.

---
## 🔐 Epic: Gestion des Transactions et Sécurité
*Rendre le processus de transaction robuste, clair et sécurisé pour le vendeur et l'acheteur.*

- **US-TRS-4: Saisie du code de retrait par le vendeur**
  - **En tant que** vendeur, **Je veux** un champ pour saisir le code de retrait fourni par l'acheteur, **Afin de** confirmer la remise en main propre.
  - **Critères d'acceptation :**
    - Sur la page de détail d'une transaction éligible (`status = payment_received`), un formulaire de saisie du `pickup_code` est visible pour le vendeur.
    - L'acheteur ne voit pas ce formulaire.

- **US-TRS-5: Finalisation de la transaction par code de retrait**
  - **En tant que** système, **Je veux** vérifier le code de retrait et, s'il est correct, finaliser la transaction, **Afin de** garantir un paiement sécurisé.
  - **Critères d'acceptation :**
    - Le backend vérifie la correspondance du code.
    - Si correct, le statut de la transaction passe à `completed` et les fonds sont transférés au portefeuille du vendeur.
    - Si incorrect, un message d'erreur est affiché.

- **US-TRS-6: Affichage du code de retrait pour l'acheteur**
  - **En tant qu'** acheteur, **Je veux** voir clairement mon "Code de Retrait" sur la page de la commande, **Afin de** pouvoir le présenter au vendeur.
  - **Critères d'acceptation :**
    - Le `pickup_code` est affiché de manière visible pour l'acheteur sur la page de la transaction.
    - Le vendeur ne voit pas ce code sur son interface.

- **US-TRS-7: Introduire le statut "Livré"**
  - **En tant que** système, **Je veux** un statut `delivered`, **Afin de** marquer la livraison physique et initier la fenêtre de confirmation.
  - **Critères d'acceptation :**
    - `TransactionStatus` est enrichi avec la valeur `delivered`.
    - Un champ `delivered_at` (timestamp) est ajouté à la table `transactions`.

- **US-TRS-8: Fenêtre de confirmation de 72h pour l'acheteur**
  - **En tant qu'** acheteur, **Je veux** être notifié de la livraison et avoir 72h pour agir, **Afin de** confirmer la réception ou signaler un problème.
  - **Critères d'acceptation :**
    - L'interface affiche un message clair sur la fenêtre de 72h.
    - Les boutons "Confirmer la réception" et "Ouvrir un litige" sont mis en évidence.

- **US-TRS-9: Finalisation automatique après 72h**
  - **En tant que** système, **Je veux** automatiquement finaliser les transactions après 72h, **Afin de** ne pas bloquer indéfiniment le paiement du vendeur.
  - **Critères d'acceptation :**
    - Un job planifié recherche les transactions `delivered` depuis plus de 72h.
    - Pour celles-ci, le statut passe à `completed` et les fonds sont transférés.

---
## 🗺️ Epic: Logistique et Adresses
*Fournir une expérience fiable et visuelle lors de la gestion des adresses et de la logistique.*

- **US-LOG-9: Définir une adresse par défaut**
  - **En tant que** utilisateur, **Je veux** pouvoir marquer une adresse comme "par défaut", **Afin de** pré-remplir les formulaires.
  - **Critères d'acceptation :**
    - Une case à cocher "Définir par défaut" est présente dans le formulaire de gestion d'adresses.
    - L'adresse par défaut est pré-sélectionnée lors de la commande.

- **US-LOG-10: Vérification et visualisation des adresses (Issue #107)**
  - **En tant que** utilisateur, **Je veux** que mon adresse soit validée et affichée sur une carte, **Afin de** m'assurer de son exactitude.
  - **Critères d'acceptation :**
    - Appel à une API de géocodage lors de la saisie.
    - Affichage d'une carte avec un marqueur si l'adresse est valide.
    - Message d'erreur si l'adresse est invalide.

---
## 🚚 Epic: Intégration Sendcloud
*Automatiser et simplifier le processus d'expédition pour les vendeurs.*

- **US-BUG-1: Réparer la création d'envoi**
  - **En tant que** vendeur, **Je veux** que le bouton "Créer l'envoi" fonctionne, **Afin de** pouvoir expédier mes commandes.
  - **Critères d'acceptation :**
    - Le clic sur le bouton déclenche l'appel à l'API Sendcloud.
    - Le problème (JS, route, etc.) est corrigé.

- **US-TRS-10: Génération de l'étiquette d'expédition**
  - **En tant que** vendeur, **Je veux** générer et télécharger une étiquette d'expédition, **Afin de** faciliter l'envoi.
  - **Critères d'acceptation :**
    - Un bouton "Générer l'étiquette" appelle le `SendcloudService`.
    - La transaction est mise à jour avec l'ID du colis et le numéro de suivi.
    - Un lien de téléchargement pour l'étiquette est affiché.

- **US-TRS-11: Traitement des webhooks Sendcloud**
  - **En tant que** système, **Je veux** recevoir et traiter les webhooks Sendcloud, **Afin d'**automatiser le suivi.
  - **Critères d'acceptation :**
    - Un endpoint `POST /webhooks/sendcloud` est sécurisé et fonctionnel.
    - Le statut de la transaction est mis à jour en fonction des événements reçus (`shipped`, `in_transit`, `delivered`).

- **US-TRS-12: Notification de livraison à l'acheteur**
  - **En tant qu'** acheteur, **Je veux** recevoir un e-mail lorsque mon colis est "Livré", **Afin d'**être informé rapidement.
  - **Critères d'acceptation :**
    - Le passage au statut `delivered` déclenche l'envoi d'un e-mail à l'acheteur.
    - L'e-mail contient un lien vers la page de la transaction.

---
## 🏦 Epic: Gestion Financière et Virements
*Mettre en place le cycle de vie complet pour que les vendeurs puissent retirer leurs fonds.*

- **US-W1: Enregistrement des informations bancaires**
  - **En tant que** vendeur, **Je veux** enregistrer mes coordonnées bancaires (IBAN), **Afin de** recevoir mes paiements.

- **US-W2: Demande de virement**
  - **En tant que** vendeur, **Je veux** pouvoir demander un virement de mon solde disponible vers mon compte bancaire.

- **US-W3: Suivi du statut d'une demande de virement**
  - **En tant que** vendeur, **Je veux** voir le statut de mes demandes de virement (en attente, approuvé, en cours, terminé, refusé).

- **US-W4: Gestion et validation des demandes de virement (Admin)**
  - **En tant qu'** administrateur, **Je veux** un tableau de bord pour voir, approuver ou refuser les demandes de virement.

- **US-W5: Traitement automatisé du virement**
  - **En tant que** système, **Je veux** initier le transfert d'argent via une API bancaire lorsque l'admin approuve une demande.

- **US-W6: Notifications par email**
  - **En tant que** vendeur, **Je veux** recevoir des notifications par email à chaque étape clé du processus de virement.

---
## 💬 Epic: Messagerie
*Améliorer l'expérience de communication entre les utilisateurs.*

- **US-MSG-005: Notification de nouveau message**
  - **En tant que** utilisateur, **Je veux** recevoir une notification lorsque je reçois un nouveau message, **Afin d'**être informé rapidement.

- **US-MSG-006: Compteur de messages non lus**
  - **En tant que** utilisateur, **Je veux** voir un compteur de messages non lus sur l'icône de messagerie, **Afin de** savoir combien de messages je n'ai pas encore lus.

- **US-MSG-007: Archiver une conversation**
  - **En tant que** utilisateur, **Je veux** pouvoir archiver une conversation, **Afin de** nettoyer ma boîte de réception.

- **US-MSG-008: Supprimer une conversation**
  - **En tant que** utilisateur, **Je veux** pouvoir supprimer une conversation, **Afin de** retirer définitivement les discussions non pertinentes.

- **US-MSG-009: Rechercher dans les conversations**
  - **En tant que** utilisateur, **Je veux** pouvoir rechercher un mot-clé dans mes conversations, **Afin de** retrouver facilement une information.

- **US-MSG-010: Statut en ligne**
  - **En tant que** utilisateur, **Je veux** pouvoir voir si un autre utilisateur est en ligne, **Afin de** savoir si je peux attendre une réponse rapide.

---
## 🔔 Epic: Notifications
*Mettre en place un système de notifications complet et configurable.*

- **US-NOTIF-10: Centre de Notifications**
  - **En tant qu'** utilisateur, **Je veux** un centre de notifications, **Afin de** consulter l'historique de mes notifications.

- **US-NOTIF-11: Marquer les notifications comme lues**
  - **En tant qu'** utilisateur, **Je veux** pouvoir marquer mes notifications comme "lues", **Afin de** gérer les nouvelles informations.

- **US-NOTIF-12: Paramètres de Notifications**
  - **En tant qu'** utilisateur, **Je veux** pouvoir configurer mes préférences de notification, **Afin de** choisir les alertes que je souhaite recevoir.

- **US-NOTIF-01: Notification de Nouvelle Offre (Vendeur)**
  - **En tant que** vendeur, **Je veux** recevoir une notification pour chaque nouvelle offre, **Afin de** répondre rapidement.

- **US-NOTIF-05: Notification d'Offre Acceptée (Acheteur)**
  - **En tant qu'** acheteur, **Je veux** être notifié quand mon offre est acceptée, **Afin de** procéder au paiement.

- **US-NOTIF-06: Notification d'Offre Refusée (Acheteur)**
  - **En tant qu'** acheteur, **Je veux** être notifié quand mon offre est refusée, **Afin de** faire une nouvelle offre ou chercher un autre article.

- **US-NOTIF-02: Notification de Paiement Reçu (Vendeur)**
  - **En tant que** vendeur, **Je veux** être notifié quand le paiement est reçu, **Afin de** préparer la commande.

- **US-NOTIF-07: Notification de Confirmation d'Envoi (Acheteur)**
  - **En tant qu'** acheteur, **Je veux** être notifié quand mon colis est envoyé, **Afin de** suivre ma commande.

- **US-NOTIF-03: Notification de Confirmation de Réception (Vendeur)**
  - **En tant que** vendeur, **Je veux** être notifié quand l'acheteur confirme la réception, **Afin de** savoir que la transaction est terminée.

- **US-NOTIF-08: Notification de Rappel de Confirmation (Acheteur)**
  - **En tant qu'** acheteur, **Je veux** un rappel si je n'ai pas confirmé la réception, **Afin de** ne pas oublier de finaliser la transaction.

- **US-NOTIF-04: Notification de Nouveau Message (Vendeur)**
  - **En tant que** vendeur, **Je veux** être notifié d'un nouveau message, **Afin de** répondre rapidement.

- **US-NOTIF-09: Notification de Nouveau Message (Acheteur)**
  - **En tant qu'** acheteur, **Je veux** être notifié d'un nouveau message, **Afin de** ne pas manquer une réponse.
